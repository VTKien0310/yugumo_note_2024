<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Features\Note\Actions\ListNoteOfUserAction;
use App\Features\Note\Actions\MakeNoteListDisplayDataAction;
use App\Features\Note\Models\Note;
use App\Features\Note\ValueObjects\NoteListDisplayDataValueObject;

new class extends Component {
    use WithPagination;

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

        return compact('notes', 'selectablePageRange', 'firstPageUrl', 'lastPageUrl', 'totalCount');
    }

    private function buildSelectablePageRange(array $requestQueryString, int $currentPage, int $lastPage): array
    {
        $maxSelectablePageRangeSize = 5;
        $selectablePageRangeCountToCenter = 2;

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

    {{-- Table --}}
    <div class="overflow-x-auto mb-5 px-5">
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
            @php /* @var NoteListDisplayDataValueObject[] $notes */ @endphp
            @foreach ($notes as $note)
                <tr class="hover">
                    <td>{{ $note->title }}</td>
                    <td>{{ $note->type }}</td>
                    <td>{{ $note->shortenedContent }}</td>
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
    <div class="flex flex-row justify-between items-center mb-5 px-5">
        <div>
            <p class="font-semibold">Total: {{ $totalCount }}</p>
        </div>
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
