<?php

use App\Extendables\Core\Http\Enums\HttpRequestParamEnum;
use App\Features\Note\Queries\NoteFilterParamEnum;
use Livewire\Volt\Component;

new class extends Component {
    public bool $fullWidth;

    public string $keyword = '';

    public function mount(bool $fullWidth = false): void
    {
        $this->fullWidth = $fullWidth;
    }

    public function search(): void
    {
        if (empty($this->keyword)) {
            return;
        }

        $params = [
            HttpRequestParamEnum::PAGINATE->value => [
                HttpRequestParamEnum::PAGE_SIZE->value => 20,
                HttpRequestParamEnum::PAGE_NUMBER->value => 1,
            ],
            HttpRequestParamEnum::SORT->value => '-updated_at,id',
            HttpRequestParamEnum::FILTER->value => [
                NoteFilterParamEnum::KEYWORD->value => $this->keyword,
            ],
        ];

        $this->redirectRoute('notes.index', $params);
    }
}; ?>

<label @class([
    'input',
    'input-bordered',
    'flex',
    'items-center',
    'gap-2',
    'ml-2',
    'w-full' => $fullWidth,
])>
    <input wire:model="keyword" @keyup.enter="$wire.search()" type="text" class="grow" placeholder="Search"/>
    <x-ionicon-search class="h-4 w-4"/>
</label>
