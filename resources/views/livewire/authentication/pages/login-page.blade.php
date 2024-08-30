<x-layouts.master-layout>
    <div class="w-dvw h-dvh flex flex-col justify-center items-center bg-primary-content">
        <div class="card w-1/4 h-1/3 flex flex-col justify-center items-center bg-base-100 rounded-lg shadow-xl">

            <h1 class="mb-10 text-3xl brand-name-font">YUGUMO</h1>

            <x-form id="login-form" class="flex flex-col justify-around items-center">
                <div class="w-full text-left flex flex-row justify-between items-center">
                    <x-label for="email" class="mr-6"/>
                    <x-input name="email" class="input input-bordered mb-4"/>
                </div>

                <div class="w-full text-left flex flex-row justify-between items-center">
                    <x-label for="password" class="mr-6"/>
                    <x-input name="password" type="password" class="input input-bordered"/>
                </div>
            </x-form>

            <button class="mt-8 btn btn-primary">Log in</button>

        </div>
    </div>
</x-layouts.master-layout>
