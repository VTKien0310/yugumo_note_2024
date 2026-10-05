{{--
    Markup for one checklist item row. Rendered inside a per-item Alpine scope
    built by checklistNoteEditor.item(...) - expects: alpId, alpContent,
    alpIsCompleted, alpDeleted, alpSaveState and the handlers below.
--}}
<input
    type="text"
    x-bind:id="`checklist-item-${alpId}-content`"
    x-bind:name="`checklist-item-${alpId}-content`"
    x-model="alpContent"
    x-on:input="alpHandleContentInput()"
    maxlength="255"
    class="w-full p-0 input input-ghost"
    x-bind:class="{ 'line-through': alpIsCompleted }"
    aria-label="Checklist item content"
/>
<div class="flex flex-row justify-end items-center content-center">
    <span x-show="alpSaveState === 'saving'" class="text-xs text-base-content/60 mr-1">Saving...</span>
    <span x-show="alpSaveState === 'failed'" class="text-xs text-error mr-1">Sync failed - will retry on next change</span>
    <input
        type="checkbox"
        x-bind:id="`checklist-item-${alpId}-is-completed`"
        x-bind:name="`checklist-item-${alpId}-is-completed`"
        x-model="alpIsCompleted"
        x-on:change="alpHandleCompletionToggle()"
        class="checkbox checkbox-primary ml-1"
        aria-label="Mark as completed"
    />
    <button
        x-on:click="alpDeleteItem()"
        class="btn-with-centered-icon btn btn-error btn-xs btn-square btn-outline ml-1"
        aria-label="Delete checklist item"
    >
        <x-ionicon-close class="h-6 w-6"/>
    </button>
</div>
