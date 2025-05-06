<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Arr;
use App\Features\User\Models\User;
use App\Features\User\Actions\UpdateUserAction;
use Illuminate\Validation\Rule;

new class extends Component {
    public string $email = '';

    public string $password = '';

    public string $currentPassword = '';

    public bool $showSuccessToast = false;

    public function mount(): void
    {
        $this->email = Auth::user()->email;
    }

    public function updateProfile(): void
    {
        $user = Auth::user();

        $validatedData = $this->validatedData($user);

        $user = resolve(UpdateUserAction::class)->handle($user, $validatedData);

        $this->resetDataProps($user);

        $this->showSuccessToast = true;
    }

    private function validatedData(User $user): array
    {
        $validated = $this->validate([
            'email' => [
                'nullable',
                'string',
                'email',
                Rule::unique(User::table(), User::EMAIL)->ignore($user->id),
            ],
            'password' => [
                'nullable',
                'string',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'currentPassword' => 'required_with:email,password|string|current_password:web',
        ]);

        $validated = Arr::only($validated, ['email', 'password']);

        return array_filter($validated, fn(string $data) => !empty($data));
    }

    private function resetDataProps(User $user): void
    {
        $this->password = '';
        $this->currentPassword = '';
        $this->email = $user->email;
    }
}
?>

<x-form wire:submit="updateProfile" class="w-full flex flex-col items-center justify-start">

    {{-- Success toast --}}
    <div x-data="{ display: $wire.entangle('showSuccessToast') }" class="toast toast-top toast-center">
        <div x-show="display" class="alert alert-success">
            <p>Profile updated successfully.</p>
            <button @click="display = false" type="button" class="btn btn-circle btn-ghost btn-xs">
                <x-ionicon-close class="h-4 w-4"/>
            </button>
        </div>
    </div>


    {{-- Email input --}}
    <label class="w-full floating-label">
        <span>Your Email</span>
        <x-input
                wire:model="email" value="{{ $email }}"
                name="email" type="email"
                placeholder="Your Email"
                class="input w-full"
        />
    </label>
    @error('email')<p class="w-full text-start text-error">{{ $message }}</p>@enderror

    {{-- New password input --}}
    <label class="w-full floating-label mt-2">
        <span>New password</span>
        <x-password
                wire:model="password"
                name="password"
                placeholder="New password"
                class="input w-full"
        />
    </label>
    @error('password')<p class="w-full text-start text-error">{{ $message }}</p>@enderror

    {{-- Current password input --}}
    <label class="w-full floating-label mt-2">
        <span>Current password*</span>
        <x-password
                wire:model="currentPassword"
                name="current_password"
                placeholder="Current password*" required
                class="input w-full"
        />
    </label>
    @error('currentPassword')<p class="w-full text-start text-error">{{ $message }}</p>@enderror

    {{-- Form actions --}}
    <div class="w-full mt-3 flex flex-row items-center justify-between">
        <button type="reset" class="w-1/3 btn">Reset</button>
        <button type="submit" class="w-1/3 btn btn-primary">Update</button>
    </div>

</x-form>
