@php
    use Illuminate\Support\Facades\Route;
    use App\Features\Note\Queries\NoteSortFieldEnum;
    use App\Extendables\Core\Http\Enums\HttpRequestParamEnum;
    use App\Features\Search\Actions\BuildNoteSearchRequestParamAction;

    $currentRouteName = Route::currentRouteName() ?? '';
    $disableNavWhenAlreadyAtRoute = function (string $routeName) use ($currentRouteName): string {
        return $routeName === $currentRouteName ? 'btn-disabled' : '';
    };
    $highlightNavWhenNotAtRoute = function (string $routeName) use ($currentRouteName): string {
        return $routeName === $currentRouteName ? '' : 'text-primary';
    };

    $defaultNotesListParams = app()->make(BuildNoteSearchRequestParamAction::class)->handle();

    $navigationItems = [
        [
            'route' => 'notes.home',
            'label' => 'Home',
            'params' => [],
        ],
        [
            'route' => 'notes.create',
            'label' => 'Add',
            'params' => [],
        ],
        [
            'route' => 'notes.index',
            'label' => 'Notes',
            'params' => $defaultNotesListParams,
        ],
    ];
@endphp

<div class="drawer">
    <input id="drawer-toggle-input" type="checkbox" class="drawer-toggle"/>

    <div class="drawer-content flex flex-col min-h-dvh">

        <div class="navbar bg-base-100 w-full shadow-lg mb-8">
            <div class="flex-none lg:hidden">
                <label for="drawer-toggle-input" aria-label="open sidebar" class="btn btn-square btn-ghost">
                    <x-ionicon-menu class="inline-block h-6 w-6 stroke-current"/>
                </label>
            </div>
            <div class="brand-name-font hidden lg:block flex-1 text-xl text-primary mx-2 px-2">YUGUMO</div>
            <div class="hidden flex-none lg:block">
                <ul class="menu menu-horizontal">
                    @foreach($navigationItems as $navigationItem)
                        <li>
                            <a
                                href="{{ route($navigationItem['route'], $navigationItem['params']) }}"
                                role="button"
                                class="w-20 btn btn-ghost {{ $disableNavWhenAlreadyAtRoute($navigationItem['route']) }} {{ $highlightNavWhenNotAtRoute($navigationItem['route']) }}"
                            >
                                {{ $navigationItem['label'] }}
                            </a>
                        </li>
                    @endforeach

                    <li>
                        <livewire:quick-search-form-livewire/>
                    </li>
                </ul>
            </div>
            <div class="w-full lg:hidden">
                <livewire:quick-search-form-livewire :for-mobile="true"/>
            </div>
        </div>

        {{ $slot }}

    </div>

    <div class="drawer-side z-10">
        <label for="drawer-toggle-input" aria-label="close sidebar" class="drawer-overlay"></label>
        <ul class="menu bg-base-100 min-h-full w-80 p-4">
            <li class="mb-3">
                <p class="brand-name-font text-xl text-primary">YUGUMO</p>
            </li>
            @foreach($navigationItems as $navigationItem)
                <li>
                    <a
                        href="{{ route($navigationItem['route']) }}"
                        role="button"
                        class="w-full justify-start text-primary btn btn-ghost {{ $disableNavWhenAlreadyAtRoute($navigationItem['route']) }}"
                    >
                        {{ $navigationItem['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
