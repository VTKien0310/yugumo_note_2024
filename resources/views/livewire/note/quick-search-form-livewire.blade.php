<?php

use App\Extendables\Core\Http\Enums\HttpRequestParamEnum;
use App\Features\Note\Queries\NoteFilterParamEnum;
use Livewire\Volt\Component;
use App\Features\Search\Actions\BuildNoteSearchRequestParamAction;

new class extends Component {
    public bool $forMobile;

    public string $keyword = '';

    public function mount(bool $forMobile = false): void
    {
        $this->forMobile = $forMobile;
    }

    public function search(): void
    {
        if (empty($this->keyword)) {
            return;
        }

        $params = app()->make(BuildNoteSearchRequestParamAction::class)->handle(
            filterConditions: [
                NoteFilterParamEnum::KEYWORD->value => $this->keyword,
            ]
        );

        $this->redirectRoute('notes.index', $params);
    }
}; ?>

<label @class([
    'input',
    'input-bordered',
    'flex',
    'items-center',
    'gap-2',
    $forMobile ? 'ml-1' : 'ml-2',
    'w-full' => $forMobile,
])>
    <input wire:model="keyword" @keyup.enter="$wire.search()" type="text" class="grow" placeholder="Search"/>
    <x-ionicon-search class="h-4 w-4"/>
</label>
