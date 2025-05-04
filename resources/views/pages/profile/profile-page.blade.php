<x-layouts.master-layout>
    <x-slot:pageTitle>Profile</x-slot:pageTitle>

    <x-layouts.authenticated-layout>
        <div class="w-full flex flex-row items-center justify-center">
            <div class="w-full md:w-1/2 lg:w-1/4 xl:w-1/4 flex flex-col items-center justify-start">
                <livewire:profile-form-livewire/>
                <div class="divider"></div>
                <button class="btn btn-block btn-error">Logout</button>
            </div>
        </div>
    </x-layouts.authenticated-layout>
</x-layouts.master-layout>
