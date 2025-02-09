<?php

use Livewire\Volt\Component;
use App\Features\Note\Actions\ListNoteOfUserAction;
use App\Features\Note\Actions\MakeNoteListDisplayDataAction;
use App\Features\Note\Models\Note;
use App\Features\Note\ValueObjects\NoteListDisplayDataValueObject;

new class extends Component {
    public string $firstPageUrl;

    public string $lastPageUrl;

    public int $totalCount;

    public array $selectablePageRange;

    public array $notes;

    public function mount(): void
    {
        $paginatedNotes = app()->make(ListNoteOfUserAction::class)->handle();

        $makeNoteListDisplayDataAction = app()->make(MakeNoteListDisplayDataAction::class);
        $this->notes = array_map(
            fn(Note $note): array => $makeNoteListDisplayDataAction->handle($note)->toResponseDataRecursive(),
            $paginatedNotes->items()
        );

        $requestQueryString = (array) request()->query();

        $currentPage = $paginatedNotes->currentPage();
        $lastPage = $paginatedNotes->lastPage();
        $this->selectablePageRange = $this->buildSelectablePageRange($requestQueryString, $currentPage, $lastPage);

        $this->firstPageUrl = $this->buildPageUrl($requestQueryString, 1);
        $this->lastPageUrl = $this->buildPageUrl($requestQueryString, $lastPage);

        $this->totalCount = $paginatedNotes->total();
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
}; ?>

<div>

    {{-- Total --}}
    <div class="mb-5 px-5">
        <p class="font-semibold">Total: {{ $totalCount }}</p>
    </div>

    {{-- Grid --}}
    <div class="grid lg:hidden grid-cols-1 md:grid-cols-2 gap-2 mb-5 px-5">
        @foreach ($notes as $note)
            <div class="card bg-base-100 w-full shadow-xl">
                <div class="card-body">
                    <div class="badge badge-ghost">{{ $note['type'] }}</div>
                    <h2 class="card-title">{{ $note['short_title'] }}</h2>
                    <p>{{ $note['short_content'] }}</p>
                    <div class="divider"></div>
                    <p class="italic mb-2">Updated at: {{ $note['updated_at'] }} <br/> Created at: {{ $note['created_at'] }}</p>
                    <div class="card-actions justify-end">
                        <a href="{{ route('notes.show', ['note' => $note['id']]) }}">
                            <button class="btn btn-sm btn-square btn-primary">
                                <x-ionicon-information class="h-4 w-4"/>
                            </button>
                        </a>

                        <div>
                            <button class="btn btn-sm btn-square btn-error">
                                <x-ionicon-trash class="h-4 w-4"/>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Table --}}
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
            @foreach ($notes as $note)
                <tr class="hover">
                    <td>{{ $note['medium_title'] }}</td>
                    <td>{{ $note['type'] }}</td>
                    <td>{{ $note['medium_content'] }}</td>
                    <td>{{ $note['updated_at'] }}</td>
                    <td>{{ $note['created_at'] }}</td>
                    <td>
                        <div class="w-full h-full flex flex-row justify-start items-center">
                            <a href="{{ route('notes.show', ['note' => $note['id']]) }}">
                                <button class="btn btn-sm btn-square btn-primary">
                                    <x-ionicon-information class="h-3 w-3"/>
                                </button>
                            </a>

                            <div>
                                <button class="btn btn-sm btn-square btn-error ml-2">
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

    {{-- Pagination --}}
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

</div>
