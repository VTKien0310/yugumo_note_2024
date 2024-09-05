<x-layouts.master-layout>
    <x-slot:pageTitle>Add</x-slot:pageTitle>

    <x-layouts.authenticated-layout>
        <div class="w-full flex flex-col justify-start content-center items-center">

            <h1 class="text-4xl mb-10">Which note category would you like to add?</h1>

            <div class="w-full flex flex-row justify-center content-center items-center gap-5">
                @foreach($noteTypes as $noteType)
                    <div class="card bg-base-100 w-96 shadow-xl">
                        <figure>
                            <img
                                src="{{ $noteType->illustrationPath }}"
                                alt="{{ $noteType->illustrationAlt }}"
                            />
                        </figure>
                        <div class="card-body">
                            <h2 class="card-title">{{ $noteType->name }}</h2>
                            <p>{{ $noteType->description }}</p>
                            <div class="card-actions justify-center">
                                <button class="btn btn-primary btn-block">Add</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </x-layouts.authenticated-layout>
</x-layouts.master-layout>
