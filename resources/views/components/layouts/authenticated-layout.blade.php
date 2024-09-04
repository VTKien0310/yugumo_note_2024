<div>
    <div class="navbar bg-primary shadow-xl">
        <h1 class="brand-name-font flex-1 text-xl text-primary-content">YUGUMO</h1>
        <div class="flex-none">
            <label class="input input-bordered flex items-center gap-2">
                <input type="text" class="grow" placeholder="Search"/>
                <x-ionicon-search class="h-4 w-4"/>
            </label>
        </div>
    </div>

    <div>
        {{ $slot }}
    </div>
</div>
