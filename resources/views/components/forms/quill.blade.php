@props([
    'name' => '',
    'id' => null,
])

@php
    $resolvedId = $id ?? $name;
    $model = $attributes->whereStartsWith('wire:model');
@endphp

<div
    {{ $attributes->whereDoesntStartWith('wire:model') }}
    wire:ignore
    x-data="{
        alpQuill: null,
        alpInit() {
            this.alpQuill = new Quill(this.$refs.alpEditor, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ header: [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ color: [] }, { background: [] }],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['blockquote', 'code-block'],
                        ['link'],
                        ['clean'],
                    ],
                },
            });

            this.$nextTick(() => {
                const alpInitial = this.$refs.alpInput.value;
                if (alpInitial) {
                    try {
                        this.alpQuill.setContents(JSON.parse(alpInitial));
                    } catch (e) {}
                }

                this.alpQuill.on('text-change', () => {
                    this.$refs.alpInput.value = JSON.stringify(this.alpQuill.getContents());
                    this.$refs.alpInput.dispatchEvent(new Event('input', { bubbles: true }));
                });
            });
        },
    }"
    x-init="alpInit()"
>
    <input
        type="hidden"
        name="{{ $name }}"
        id="{{ $resolvedId }}"
        x-ref="alpInput"
        @if ($model->first())
            {{ $attributes->whereStartsWith('wire:model') }}
        @endif
    >

    <div x-ref="alpEditor" class="ql-editor-host"></div>
</div>
