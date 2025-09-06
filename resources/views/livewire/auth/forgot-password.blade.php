<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        Password::sendResetLink($this->only('email'));

        session()->flash('status', __('A reset link will be sent if the account exists.'));
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="'Şifrenizi mi Unuttunuz?'" :description="'E-posta adresinizi girin, şifre sıfırlama bağlantısı gönderelim'" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="sendPasswordResetLink" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="'E-posta Adresi'"
            type="email"
            required
            autofocus
            placeholder="email@example.com"
        />

        <button type="submit" class="w-full bg-white text-[#1e40af] hover:bg-[#1e40af] hover:text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-white/30 border-2 border-white">Şifre Sıfırlama Bağlantısı Gönder</button>
    </form>

    <div class="text-center text-sm text-gray-700">
        <span>Veya</span>
        <a href="{{ route('login') }}" class="ml-1 text-blue-600 hover:text-blue-800 font-semibold transition-colors duration-200 underline" wire:navigate>giriş yapın</a>
    </div>
</div>
