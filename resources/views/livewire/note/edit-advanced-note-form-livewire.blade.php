<?php

use App\Features\Note\Models\Note;
use Livewire\Volt\Component;

new class extends Component
{
    public Note $note;

    public function mount(Note $note): void
    {
        $this->note = $note->load(Note::RELATION_RICH_TEXT_CONTENT);
    }
}; ?>

<div
    class="w-3/4 xl:w-1/2"
    x-data="{
        alpSyncUrl: @js(route('bff.notes.update', ['note' => $this->note->id])),
        alpContent: @js($this->note->richTextContent->content ?? ['ops' => [['insert' => "\n"]]]),
        alpDirty: false,
        alpSyncing: false,
        alpSaveState: 'saved',
        alpDebounceTimer: null,
        alpInit() {
            window.addEventListener('quill-text-change', (event) => {
                this.alpContent = event.detail.content;
                this.alpScheduleSync();
            });

            window.addEventListener('beforeunload', (event) => {
                if (! this.alpDirty) return;

                this.alpSync({ keepalive: true });

                event.preventDefault();
                event.returnValue = '';
            });
        },
        alpScheduleSync() {
            this.alpDirty = true;
            this.alpSaveState = 'unsaved';

            clearTimeout(this.alpDebounceTimer);
            this.alpDebounceTimer = setTimeout(() => this.alpSync(), 3000);
        },
        async alpSync({ keepalive = false } = {}) {
            if (! this.alpDirty || this.alpSyncing) return;

            this.alpSyncing = true;
            this.alpSaveState = 'saving';

            try {
                const response = await fetch(this.alpSyncUrl, {
                    method: 'PUT',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({
                        title: this.$refs.alpTitleInput.value,
                        content: this.alpContent,
                    }),
                    keepalive: keepalive,
                });

                if (! response.ok) throw new Error('Sync failed with status ' + response.status);

                this.alpDirty = false;
                this.alpSaveState = 'saved';
            } catch (error) {
                this.alpSaveState = 'failed';
            } finally {
                this.alpSyncing = false;
            }
        },
    }"
    x-init="alpInit()"
>
    <div class="w-full flex flex-col justify-start items-center">
        <div class="w-full flex flex-col justify-start items-start mb-5">
            <x-forms.label for="title" class="font-bold text-xs mb-1"/>
            <x-forms.input
                name="title"
                :value="$this->note->title"
                class="input input-bordered w-full"
                x-ref="alpTitleInput"
                x-on:input="alpScheduleSync()"
            />
        </div>
        <div class="w-full flex flex-col justify-start items-start">
            <x-forms.label for="content" class="font-bold text-xs mb-1"/>
            <x-forms.quill
                name="content"
                class="w-full block"
                :value="json_encode($this->note->richTextContent->content)"
            />
        </div>
        <div class="w-full flex flex-row justify-end items-center gap-3 pt-4">
            <span x-show="alpSaveState === 'unsaved'" style="display: none;" class="text-xs text-warning">Unsaved changes</span>
            <span x-show="alpSaveState === 'saving'" style="display: none;" class="text-xs text-info">Saving...</span>
            <span x-show="alpSaveState === 'saved'" style="display: none;" class="text-xs text-success">Saved</span>
            <span x-show="alpSaveState === 'failed'" style="display: none;" class="text-xs text-error">Sync failed - will retry on next change</span>
        </div>
    </div>
</div>
