<?php

use App\Features\Note\Models\Note;
use Livewire\Volt\Component;
use App\Features\Note\Actions\CheckUserHasReachedMaximumAllowedBookmarkedNotesAction;
use App\Features\User\Models\User;

new class extends Component {
    public Note $note;

    public bool $bookmarked;

    public bool $disableBookmarkButton;

    public function mount(Note $note): void
    {
        $this->note = $note;

        $noteIsBookmarked = (bool) $note->bookmarked->value;
        $this->bookmarked = $noteIsBookmarked;

        // prevent bookmarking after the maximum allowed quota is reached
        $this->disableBookmarkButton = $noteIsBookmarked ? false : $this->userHasReachedMaximumAllowedBookmarkNotes($note->user);
    }

    public function with(): array
    {
        $bookmarkIconSize = 'w-7 h-7';

        return compact('bookmarkIconSize');
    }

    private function userHasReachedMaximumAllowedBookmarkNotes(User $user): bool
    {
        return app()->make(CheckUserHasReachedMaximumAllowedBookmarkedNotesAction::class)->handle($user);
    }
}; ?>

<button class="btn btn-circle btn-ghost" @disabled($disableBookmarkButton)>
    @if($bookmarked)
        <x-ionicon-bookmark class="{{ $bookmarkIconSize }}"/>
    @else
        <x-ionicon-bookmark-outline class="{{ $bookmarkIconSize }}"/>
    @endif
</button>
