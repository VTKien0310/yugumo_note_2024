<x-layouts.master-layout>
    <x-slot:pageTitle>Add</x-slot:pageTitle>

    <x-layouts.authenticated-layout>
        <div class="w-full flex flex-col justify-start content-center items-center">

            @session('note-type-not-found')
            <div role="alert" class="w-1/2 alert alert-error mb-5">
                <x-ionicon-bug class="h-4 w-4"/>
                <p>{{ session('note-type-not-found') }}</p>
            </div>
            @endsession

            <h1 class="text-4xl mb-10">Which note type would you like to add?</h1>

            <livewire:create-note-for-type-livewire/>

        </div>
    </x-layouts.authenticated-layout>
</x-layouts.master-layout>
