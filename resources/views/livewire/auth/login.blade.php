<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Livewire\Volt\Attributes\Route as VoltRoute;

new #[Layout('components.layouts.auth')] #[VoltRoute(['GET', 'POST'])] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        // Rol bazlı yönlendirme
        $user = Auth::user();
        if ($user->isDoctor()) {
            $this->redirectIntended(default: route('doctor-panel', absolute: false));
        } else {
            $this->redirectIntended(default: route('dashboard', absolute: false));
        }
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="'KlinikGo\'ya Giriş Yapın'" :description="'Klinik yönetim sisteminize erişmek için giriş bilgilerinizi girin'" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="'E-posta Adresi'"
            type="email"
            required
            autofocus
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Password -->
        <div class="relative">
            <flux:input
                wire:model="password"
                :label="'Şifre'"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="'Şifre'"
                viewable
            />

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="absolute end-0 top-0 text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200" wire:navigate>
                    Şifrenizi mi unuttunuz?
                </a>
            @endif
        </div>

        <!-- Remember Me -->
        <flux:checkbox wire:model="remember" :label="'Beni Hatırla'" />

        <div class="flex items-center justify-end">
            <button type="submit" class="w-full bg-[#1e40af] hover:bg-white hover:text-[#1e40af] text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-blue-300 border-2 border-[#1e40af]">
                Giriş Yap
            </button>
        </div>
    </form>

    @if (Route::has('register'))
        <div class="text-center text-sm text-gray-700">
            <span>Hesabınız yok mu?</span>
            <a href="{{ route('register') }}" class="ml-1 text-blue-600 hover:text-blue-800 font-semibold transition-colors duration-200 underline" wire:navigate>Kayıt Ol</a>
        </div>
    @endif
</div>
