<div class="drawer grid grid-cols-12 gap-0">
    <label for="my-drawer" class="btn btn-ghost drawer-button w-fit h-fit col-start-1 col-end-2">
        <x-solar-hamburger-menu-linear class="w-10 h-10"/>
    </label>
    <input id="my-drawer" type="checkbox" class="drawer-toggle"/>

    <div class="drawer-content w-full col-start-2 col-end-13">
        {{ $slot }}
    </div>

    <div class="drawer-side">
        <label for="my-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
        <ul class="menu bg-base-200 text-base-content min-h-full w-80 p-4">
            <!-- Sidebar content here -->
            <li><a>Sidebar Item 1</a></li>
            <li><a>Sidebar Item 2</a></li>
        </ul>
    </div>
</div>
