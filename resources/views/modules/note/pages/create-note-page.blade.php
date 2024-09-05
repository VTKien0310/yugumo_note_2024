@php
    $noteTypes = [
        [
            'name'=>'Simple note',
            'description'=>"A text-only note to write down what's on your mind",
            'img'=>Vite::asset('resources/images/simple-note.svg'),
            'img_alt'=>'Simple note illustration'
        ],
        [
            'name'=>'To-do list',
            'description'=>'A list with checkboxes to keep track of things you need',
            'img'=>Vite::asset('resources/images/to-do-list.svg'),
            'img_alt'=>'To-do list illustration'
        ],
    ]
@endphp

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
                                src="{{ $noteType['img'] }}"
                                alt="{{ $noteType['img_alt'] }}"
                            />
                        </figure>
                        <div class="card-body">
                            <h2 class="card-title">{{ $noteType['name'] }}</h2>
                            <p>{{ $noteType['description'] }}</p>
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
