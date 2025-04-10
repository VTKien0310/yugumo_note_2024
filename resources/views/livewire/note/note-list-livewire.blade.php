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

new class extends Component {
    #[Url(as: HttpRequestParamEnum::FILTER->value)]
    public array $filterQueryString = [];

    #[Url(as: HttpRequestParamEnum::SORT->value)]
    public string $sortQueryString = '';

    public array $typesFilter = [];

    public string $keywordFilter = '';

    public string $sortField = '';

    public string $sortDirection = '';

    public function mount(): void
    {
        $this->keywordFilter = $this->filterQueryString[NoteFilterParamEnum::KEYWORD->value] ?? '';

        $this->typesFilter = explode(',', $this->filterQueryString[NoteFilterParamEnum::TYPE_ID->value] ?? '');

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
        $requestedSorts = array_filter($requestedSorts, fn(SortCondition $sort): bool => $sort->field !== 'id');

        // only use the 1st requested sort, default to sort "updated_at" desc if no sort are requested
        return array_values($requestedSorts)[0] ?? new SortCondition(
            field: 'updated_at',
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

        return compact('notes', 'selectablePageRange', 'firstPageUrl', 'lastPageUrl', 'totalCount', 'noteTypes');
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
        $requestQueryString['page']['number'] = $pageNumber;

        return route('notes.index', $requestQueryString);
    }

    public function applyAdvancedConfig(): void
    {
        $params = [
            HttpRequestParamEnum::PAGINATE->value => [
                'size' => 20,
                'number' => 1,
            ],
            HttpRequestParamEnum::SORT->value => $this->buildSortParams(),
        ];

        $filter = $this->buildFilterParams();
        if (!empty($filter)) {
            $params[HttpRequestParamEnum::FILTER->value] = $filter;
        }

        $this->redirectRoute('notes.index', $params);
    }

    private function buildFilterParams(): array
    {
        $filter = [];

        if (!empty($this->keywordFilter)) {
            $filter[NoteFilterParamEnum::KEYWORD->value] = $this->keywordFilter;
        }

        $validFilteringTypeIds = collect($this->typesFilter)->filter();
        if ($validFilteringTypeIds->isNotEmpty()) {
            $filter[NoteFilterParamEnum::TYPE_ID->value] = $validFilteringTypeIds->implode(',');
        }

        return $filter;
    }

    private function buildSortParams(): string
    {
        $defaultSort = '-updated_at,id';
        if (empty($this->sortField)) {
            return $defaultSort;
        }

        $sortDirection = $this->sortDirection === SortDirectionEnum::DESC->value ? '-' : '';

        // append default "id" sort
        return $sortDirection.$this->sortField.',id';
    }
}; ?>

<div>

    {{-- Filters --}}
    <div class="mb-5 px-5">
        <div class="collapse collapse-arrow bg-base-200 border border-base-300">

            {{-- Collapse trigger --}}
            <input type="checkbox"/>
            <div class="collapse-title text-xl font-medium">Advanced</div>

            {{-- Collapse content --}}
            {{-- We need to keep the original data from Livewire for the sort config reset function --}}
            <div x-data="{ sortField: $wire.sortField, sortDirection: $wire.sortDirection }" class="collapse-content">
                <div class="block lg:flex flex-row justify-between items-start">
                    {{-- Keyword filter --}}
                    <div class="w-full md:w-1/3 lg:w-1/3 p-4">
                        <p class="font-semibold">Keyword</p>
                        <div class="pt-2">
                            <input wire:model="keywordFilter" type="text" class="input input-bordered w-full"/>
                        </div>
                    </div>

                    {{-- Type filter --}}
                    <div class="w-full md:w-1/3 lg:w-1/3 p-4">
                        <p class="font-semibold">Type</p>
                        <div class="flex flex-col lg:flex-row justify-start lg:justify-between items-start pt-2">
                            @php /** @var NoteTypeViewDataValueObject $noteType */ @endphp
                            @foreach($noteTypes as $noteType)
                                <label class="w-full lg:w-fit p-0 pt-1 lg:pt-0 label cursor-pointer">
                                    <span class="label-text lg:pr-2">{{ $noteType->name }}</span>
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
                    <div
                        class="w-full md:w-1/3 lg:w-1/3 p-4"
                    >
                        <div class="flex flex-row justify-start items-center">
                            <p class="font-semibold">Sort</p>

                            <button
                                @click="sortField = $wire.sortField; sortDirection = $wire.sortDirection"
                                class="btn btn-sm lg:btn-xs btn-outline btn-circle ml-2"
                            >
                                <x-ionicon-refresh class="h-5 w-5 lg:h-4 lg:w-4"/>
                            </button>
                        </div>

                        {{-- Sort field --}}
                        <p class="pt-2">Field:</p>
                        <div class="flex flex-row flex-wrap justify-start items-center gap-4">
                            <label class="label cursor-pointer inline-flex items-center gap-2">
                                <input
                                    x-model="sortField"
                                    type="radio"
                                    name="sortField"
                                    value="updated_at"
                                    class="radio"
                                />
                                <span class="label-text">Updated at</span>
                            </label>
                            <label class="label cursor-pointer inline-flex items-center gap-2">
                                <input
                                    x-model="sortField"
                                    type="radio"
                                    name="sortField"
                                    value="created_at"
                                    class="radio"
                                />
                                <span class="label-text">Created at</span>
                            </label>
                            <label class="label cursor-pointer inline-flex items-center gap-2">
                                <input
                                    x-model="sortField"
                                    type="radio"
                                    name="sortField"
                                    value="type"
                                    class="radio"
                                />
                                <span class="label-text">Type</span>
                            </label>
                            <label class="label cursor-pointer inline-flex items-center gap-2">
                                <input
                                    x-model="sortField"
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
                                    x-model="sortDirection"
                                    type="radio"
                                    name="sortDirection"
                                    value="asc"
                                    class="radio"
                                />
                                <span class="label-text">Ascending</span>
                            </label>

                            <label class="label cursor-pointer inline-flex items-center gap-2">
                                <input
                                    x-model="sortDirection"
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
                    <button
                        @click="$wire.sortField = sortField; $wire.sortDirection = sortDirection; $wire.applyAdvancedConfig();"
                        class="btn btn-primary"
                    >
                        Apply
                    </button>
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
                    <div class="badge badge-ghost">{{ $note->type }}</div>
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
