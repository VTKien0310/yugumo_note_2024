<?php

use App\Features\NoteType\ValueObjects\NoteTypeViewDataValueObject;
use Livewire\Volt\Component;
use App\Features\Note\Actions\ListNoteOfUserAction;
use App\Features\Note\Actions\MakeNoteListDisplayDataAction;
use App\Features\Note\Models\Note;
use App\Features\Note\ValueObjects\NoteListDisplayDataValueObject;
use App\Features\Note\Actions\DeleteNoteByIdAction;
use App\Extendables\Core\Http\Enums\HttpRequestHeaderEnum;
use App\Features\NoteType\Actions\MakeAllNoteTypeViewDataAction;
use Livewire\Attributes\Url;
use App\Extendables\Core\Http\Enums\HttpRequestParamEnum;
use App\Features\Note\Queries\NoteFilterParamEnum;
use App\Extendables\Core\Http\Request\States\QueryString\SortCondition;
use App\Extendables\Core\Utils\SortDirectionEnum;
use App\Features\Note\Queries\NoteSortFieldEnum;
use App\Features\Search\Actions\BuildNoteSearchRequestParamAction;
use App\Extendables\Core\Utils\BoolIntValueEnum;

new class extends Component {
    #[Url(as: HttpRequestParamEnum::FILTER->value)]
    public array $filterQueryString = [];

    #[Url(as: HttpRequestParamEnum::SORT->value)]
    public string $sortQueryString = '';

    public array $typesFilter = [];

    public bool $bookmarkedOnly = false;

    public string $keywordFilter = '';

    public string $sortField = '';

    public string $sortDirection = '';

    public function mount(): void
    {
        $this->keywordFilter = $this->filterQueryString[NoteFilterParamEnum::KEYWORD->value] ?? '';

        $this->typesFilter = explode(',', $this->filterQueryString[NoteFilterParamEnum::TYPE_ID->value] ?? '');

        $this->bookmarkedOnly = (bool) ($this->filterQueryString[NoteFilterParamEnum::BOOKMARKED->value] ?? false);

        $sortConfig = $this->makeSortConfig($this->sortQueryString);
        $this->sortField = $sortConfig->field;
        $this->sortDirection = $sortConfig->direction->value;
    }

    private function makeSortConfig(string $sortQueryString): SortCondition
    {
        $requestedSorts = explode(',', $sortQueryString);

        $transformRequestDataToSortCondition = function (string $requestedSort): SortCondition {
            $isDescSort = str_starts_with($requestedSort, '-');

            return new SortCondition(
                field: $isDescSort ? substr($requestedSort, 1) : $requestedSort,
                direction: $isDescSort ? SortDirectionEnum::DESC : SortDirectionEnum::ASC
            );
        };
        $requestedSorts = array_map($transformRequestDataToSortCondition, $requestedSorts);

        // remove default appended "id" sort
        $requestedSorts = array_filter($requestedSorts,
            fn(SortCondition $sort): bool => $sort->field !== NoteSortFieldEnum::ID->value);

        // only use the 1st requested sort, default to sort "updated_at" desc if no sort are requested
        return array_values($requestedSorts)[0] ?? new SortCondition(
            field: NoteSortFieldEnum::UPDATED_AT->value,
            direction: SortDirectionEnum::DESC
        );
    }

    public function with(): array
    {
        $paginatedNotes = app()->make(ListNoteOfUserAction::class)->handle();

        $makeNoteListDisplayDataAction = app()->make(MakeNoteListDisplayDataAction::class);
        $notes = array_map(
            fn(Note $note): NoteListDisplayDataValueObject => $makeNoteListDisplayDataAction->handle($note),
            $paginatedNotes->items()
        );

        $requestQueryString = (array) request()->query();

        $currentPage = $paginatedNotes->currentPage();
        $lastPage = $paginatedNotes->lastPage();
        $selectablePageRange = $this->buildSelectablePageRange($requestQueryString, $currentPage, $lastPage);

        $firstPageUrl = $this->buildPageUrl($requestQueryString, 1);
        $lastPageUrl = $this->buildPageUrl($requestQueryString, $lastPage);

        $totalCount = $paginatedNotes->total();

        $noteTypes = app()->make(MakeAllNoteTypeViewDataAction::class)->handle();

        $bookmarkIconSize = 'w-5 h-5';

        return compact('notes', 'selectablePageRange', 'firstPageUrl', 'lastPageUrl', 'totalCount', 'noteTypes', 'bookmarkIconSize');
    }

    public function deleteNote(string $id): void
    {
        app()->make(DeleteNoteByIdAction::class)->handle($id);

        // refresh page
        $this->redirect(request()->header(HttpRequestHeaderEnum::REFERER->value));
    }

    private function buildSelectablePageRange(array $requestQueryString, int $currentPage, int $lastPage): array
    {
        $maxSelectablePageRangeSize = 3;
        $selectablePageRangeCountToCenter = 1;

        $selectablePageRangeEnd = min($currentPage + $selectablePageRangeCountToCenter, $lastPage);
        $selectablePageRangeStart = max($selectablePageRangeEnd - $maxSelectablePageRangeSize, 0) + 1;

        $selectablePage = $selectablePageRangeStart;
        $selectablePageRange = [];
        while ($selectablePage <= $lastPage && count($selectablePageRange) < $maxSelectablePageRangeSize) {
            $selectablePageRange[] = [
                'number' => $selectablePage,
                'url' => $this->buildPageUrl($requestQueryString, $selectablePage),
                'is_current_page' => $selectablePage === $currentPage
            ];

            $selectablePage++;
        }

        return $selectablePageRange;
    }

    private function buildPageUrl(array $requestQueryString, int $pageNumber): string
    {
        $requestQueryString[HttpRequestParamEnum::PAGINATE->value][HttpRequestParamEnum::PAGE_NUMBER->value] = $pageNumber;

        return route('notes.index', $requestQueryString);
    }

    public function applyAdvancedConfig(): void
    {
        $params = app()->make(BuildNoteSearchRequestParamAction::class)->handle(
            filterConditions: $this->buildFilterParams(),
            sortConditions: $this->buildSortParams()
        );

        $this->redirectRoute('notes.index', $params);
    }

    private function buildFilterParams(): array
    {
        $filter = [];

        if (!empty($this->keywordFilter)) {
            $filter[NoteFilterParamEnum::KEYWORD->value] = $this->keywordFilter;
        }

        // prevent passing of empty string in the request
        $validFilteringTypeIds = collect($this->typesFilter)->filter();
        if ($validFilteringTypeIds->isNotEmpty()) {
            $filter[NoteFilterParamEnum::TYPE_ID->value] = $validFilteringTypeIds->implode(',');
        }

        if ($this->bookmarkedOnly) {
            $filter[NoteFilterParamEnum::BOOKMARKED->value] = BoolIntValueEnum::TRUE->value;
        }

        return $filter;
    }

    /**
     * @return SortCondition[]
     */
    private function buildSortParams(): array
    {
        if (empty($this->sortField)) {
            return [];
        }

        return [
            new SortCondition(
                field: $this->sortField,
                direction: $this->sortDirection === 'asc' ? SortDirectionEnum::ASC : SortDirectionEnum::DESC
            )
        ];
    }
}; ?>

<div>

    {{-- Filters --}}
    <div class="mb-5 px-5">
        <div class="collapse collapse-arrow bg-base-200 border border-base-300">

            {{-- Collapse trigger --}}
            <input type="checkbox" checked/>
            <div class="collapse-title text-xl font-medium">Advanced</div>

            {{-- Collapse content --}}
            <div class="collapse-content">
                <div class="block lg:flex flex-row justify-between items-start">
                    {{-- Keyword filter --}}
                    <div class="w-full md:w-1/3 lg:w-1/3 p-4">
                        <div class="flex flex-row justify-start items-center pb-8">
                            <p class="font-semibold">Bookmarked</p>
                            <input wire:model="bookmarkedOnly" type="checkbox" class="toggle ml-2"/>
                        </div>
                        <p class="font-semibold">Keyword</p>
                        <div class="pt-2">
                            <input wire:model="keywordFilter" @keyup.enter="$wire.applyAdvancedConfig()" type="text"
                                   class="input input-bordered w-full"
                            />
                        </div>
                    </div>

                    {{-- Type filter --}}
                    <div class="w-full md:w-1/3 lg:w-1/3 p-4">
                        <p class="font-semibold">Type</p>
                        <div class="flex flex-col xl:flex-row justify-start xl:justify-between items-start pt-2">
                            @php /** @var NoteTypeViewDataValueObject $noteType */ @endphp
                            @foreach($noteTypes as $noteType)
                                <label class="w-full xl:w-fit p-0 pt-1 xl:pt-0 label cursor-pointer">
                                    <span class="label-text xl:pr-2">{{ $noteType->name }}</span>
                                    <input
                                        type="checkbox"
                                        value="{{ $noteType->id }}"
                                        x-model="$wire.typesFilter"
                                        class="checkbox"
                                    />
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Sort --}}
                    <div class="w-full md:w-1/3 lg:w-1/3 p-4">
                        <div class="flex flex-row justify-start items-center">
                            <p class="font-semibold">Sort</p>
                        </div>

                        {{-- Sort field --}}
                        <p class="pt-2">Field:</p>
                        <div class="flex flex-row flex-wrap justify-start items-center gap-4">
                            <label class="label cursor-pointer inline-flex items-center gap-2">
                                <input
                                    x-model="$wire.sortField"
                                    type="radio"
                                    name="sortField"
                                    value="updated_at"
                                    class="radio"
                                />
                                <span class="label-text">Updated at</span>
                            </label>
                            <label class="label cursor-pointer inline-flex items-center gap-2">
                                <input
                                    x-model="$wire.sortField"
                                    type="radio"
                                    name="sortField"
                                    value="created_at"
                                    class="radio"
                                />
                                <span class="label-text">Created at</span>
                            </label>
                            <label class="label cursor-pointer inline-flex items-center gap-2">
                                <input
                                    x-model="$wire.sortField"
                                    type="radio"
                                    name="sortField"
                                    value="type"
                                    class="radio"
                                />
                                <span class="label-text">Type</span>
                            </label>
                            <label class="label cursor-pointer inline-flex items-center gap-2">
                                <input
                                    x-model="$wire.sortField"
                                    type="radio"
                                    name="sortField"
                                    value="title"
                                    class="radio"
                                />
                                <span class="label-text">Title</span>
                            </label>
                        </div>

                        {{-- Sort direction --}}
                        <p class="pt-2">Direction:</p>
                        <div class="flex flex-row flex-wrap justify-start items-center gap-4">
                            <label class="label cursor-pointer inline-flex items-center gap-2">
                                <input
                                    x-model="$wire.sortDirection"
                                    type="radio"
                                    name="sortDirection"
                                    value="asc"
                                    class="radio"
                                />
                                <span class="label-text">Ascending</span>
                            </label>

                            <label class="label cursor-pointer inline-flex items-center gap-2">
                                <input
                                    x-model="$wire.sortDirection"
                                    type="radio"
                                    name="sortDirection"
                                    value="desc"
                                    class="radio"
                                />
                                <span class="label-text">Descending</span>
                            </label>
                        </div>

                    </div>
                </div>
                <div class="flex flex-row justify-end items-center pt-4">
                    <button wire:click="applyAdvancedConfig" class="btn btn-primary">Apply</button>
                </div>
            </div>

        </div>
    </div>

    {{-- Total number of records --}}
    <div class="mb-5 px-5">
        <p class="font-semibold">Total: {{ $totalCount }}</p>
    </div>

    {{-- Grid for smaller screen --}}
    <div class="grid lg:hidden grid-cols-1 md:grid-cols-2 gap-2 mb-5 px-5">
        @php /** @var NoteListDisplayDataValueObject $note */ @endphp
        @foreach ($notes as $note)
            <div class="card bg-base-100 w-full shadow-xl">
                <div class="card-body">
                    <div class="flex flex-row justify-between items-center mb-1">
                        <div class="badge badge-ghost">{{ $note->type }}</div>
                        @if($note->bookmarked)
                            <x-ionicon-bookmark class="{{ $bookmarkIconSize }}"/>
                        @else
                            <div class="{{ $bookmarkIconSize }}">
                                {{--                                Dummy element to maintain the same width and height as the bookmark icon.--}}
                            </div>
                        @endif
                    </div>
                    <h2 class="card-title">{{ $note->shortTitle }}</h2>
                    <p>{{ $note->shortContent }}</p>
                    <div class="divider"></div>
                    <p class="italic mb-2">
                        Updated at: {{ $note->updatedAt }}
                        <br/> Created at: {{ $note->createdAt }}
                    </p>
                    <div class="card-actions justify-end">
                        <a href="{{ route('notes.show', ['note' => $note->id]) }}">
                            <button class="btn btn-sm btn-square btn-primary">
                                <x-ionicon-information class="h-4 w-4"/>
                            </button>
                        </a>

                        <div>
                            <button
                                onclick="{{ "delete_confirmation_$note->id.showModal()" }}"
                                class="btn btn-sm btn-square btn-error"
                            >
                                <x-ionicon-trash class="h-4 w-4"/>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Table for larger screen --}}
    <div id="notes-list-table" class="overflow-x-auto mb-5 px-5 hidden lg:block">
        <table class="table">
            <thead>
            <tr>
                <th>Title</th>
                <th>Type</th>
                <th>Content</th>
                <th>Updated at</th>
                <th>Created at</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @php /** @var NoteListDisplayDataValueObject $note */ @endphp
            @foreach ($notes as $note)
                <tr class="hover">
                    <td>{{ $note->mediumTitle }}</td>
                    <td>{{ $note->type }}</td>
                    <td>{{ $note->mediumContent }}</td>
                    <td>{{ $note->updatedAt }}</td>
                    <td>{{ $note->createdAt }}</td>
                    <td>
                        <div class="w-full h-full flex flex-row justify-start items-center">
                            <a href="{{ route('notes.show', ['note' => $note->id]) }}">
                                <button class="btn btn-sm btn-square btn-primary">
                                    <x-ionicon-information class="h-3 w-3"/>
                                </button>
                            </a>

                            <div>
                                <button
                                    onclick="{{ "delete_confirmation_$note->id.showModal()" }}"
                                    class="btn btn-sm btn-square btn-error ml-2"
                                >
                                    <x-ionicon-trash class="h-3 w-3"/>
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- Paginator --}}
    <div class="flex flex-row justify-end items-center mb-5 px-5">
        <div class="join">
            <a href="{{ $firstPageUrl }}" class="join-item btn">«</a>
            @foreach($selectablePageRange as $page)
                <a
                    href="{{ $page['url'] }}"
                    @class([
                        'join-item',
                         'btn',
                         'btn-disabled'=>$page['is_current_page'],
                    ])
                >
                    {{ $page['number'] }}
                </a>
            @endforeach
            <a href="{{ $lastPageUrl }}" class="join-item btn">»</a>
        </div>
    </div>

    {{-- Delete confirmation dialog --}}
    @php /** @var NoteListDisplayDataValueObject $note */ @endphp
    @foreach($notes as $note)
        <dialog id="{{ "delete_confirmation_$note->id" }}" class="modal">
            <div class="modal-box">
                <h3 class="text-lg font-bold">Delete the note?</h3>
                <p class="py-4">This will delete <span class="font-bold">{{ $note->shortTitle }}</span></p>
                <div class="modal-action">
                    <form method="dialog">
                        <button class="btn">Cancel</button>
                    </form>

                    <button wire:click="deleteNote('{{ $note->id }}')" class="btn btn-error">Delete</button>
                </div>
            </div>
        </dialog>
    @endforeach

</div>
