<x-layouts.master-layout>
    <x-slot:pageTitle>Profile</x-slot:pageTitle>

    <x-layouts.authenticated-layout>
        <div class="w-full flex flex-row items-center justify-center">
            <div class="w-full md:w-1/2 lg:w-1/4 xl:w-1/4 p-3 flex flex-col items-center justify-start">
                <livewire:profile-form-livewire/>
                <div class="divider"></div>
                <x-form class="w-full" action="{{ route('auth.logout') }}" method="DELETE">
                    <button class="btn btn-block btn-error">Log out</button>
                </x-form>
            </div>
        </div>
    </x-layouts.authenticated-layout>
</x-layouts.master-layout>
