@php
    use Illuminate\Support\Facades\Route;

    $currentRouteName = Route::currentRouteName() ?? '';
    $disableNavWhenAlreadyAtRoute = function (string $routeName) use ($currentRouteName): string {
        return $routeName === $currentRouteName ? 'btn-disabled' : '';
    };

    $navigationItems = [
        [
            'route'=>'notes.home',
            'label'=>'Home',
        ],
        [
            'route'=>'notes.create',
            'label'=>'Add',
        ],
        [
            'route'=>'notes.index',
            'label'=>'Notes',
        ],
    ];
@endphp

<div>
    <div class="navbar bg-base-100 shadow-xl mb-5">

        <h1 class="brand-name-font flex-1 text-xl text-primary">YUGUMO</h1>

        <div class="flex-none">

            @foreach($navigationItems as $navigationItem)
                <a
                    href="{{ route($navigationItem['route']) }}"
                    role="button"
                    class="w-20 text-primary btn btn-ghost {{ $disableNavWhenAlreadyAtRoute($navigationItem['route']) }}"
                >
                    {{ $navigationItem['label'] }}
                </a>
            @endforeach

            <label class="input input-bordered flex items-center gap-2 ml-2">
                <input type="text" class="grow" placeholder="Search"/>
                <x-ionicon-search class="h-4 w-4"/>
            </label>

        </div>

    </div>

    <div class="flex flex-row justify-center">
        {{ $slot }}
    </div>
</div>
