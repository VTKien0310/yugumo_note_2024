<div>
    <div class="navbar bg-primary shadow-xl">

        @if(request()->route()->named('notes.recent'))
            <h1 class="brand-name-font flex-1 text-xl text-primary-content">YUGUMO</h1>
        @else
            <a href="{{ route('notes.recent') }}" class="brand-name-font flex-1 text-xl text-primary-content">YUGUMO</a>
        @endif

        <div class="flex-none">

            @if(!request()->route()->named('notes.create'))
                <a href="{{ route('notes.create') }}" role="button" class="btn btn-ghost text-primary-content">
                    New note
                </a>
            @endif

            <label class="input input-bordered flex items-center gap-2 ml-1">
                <input type="text" class="grow" placeholder="Search"/>
                <x-ionicon-search class="h-4 w-4"/>
            </label>

        </div>

    </div>

    <div>
        {{ $slot }}
    </div>
</div>
