<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = 'secretary';
    public ?int $doctor_id = null;
    
    public function mount()
    {
        // Eğer giriş yapan kullanıcı varsa ve doktor ise, varsayılan olarak onun ID'sini ata
        if (Auth::check() && Auth::user()->isDoctor()) {
            $this->doctor_id = Auth::id();
        }
    }
    
    public function getDoctorsProperty()
    {
        return User::where('role', 'doctor')->get();
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:doctor,nurse,secretary'],
        ];
        
        // Eğer sekreter veya hemşire ise doctor_id zorunlu
        if (in_array($this->role, ['secretary', 'nurse'])) {
            $rules['doctor_id'] = ['required', 'exists:users,id'];
        }
        
        $validated = $this->validate($rules);

        $validated['password'] = Hash::make($validated['password']);
        
        // Doktor ise doctor_id null olmalı
        if ($this->role === 'doctor') {
            $validated['doctor_id'] = null;
        }

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="'KlinikGo Hesabı Oluşturun'" :description="'Klinik yönetim sistemi için yeni hesap oluşturmak üzere bilgilerinizi girin'" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="register" class="flex flex-col gap-6">
        <!-- Name -->
        <flux:input
            wire:model="name"
            :label="'Ad Soyad'"
            type="text"
            required
            autofocus
            autocomplete="name"
            :placeholder="'Ad Soyad'"
        />

        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="'E-posta Adresi'"
            type="email"
            required
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="'Şifre'"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="'Şifre'"
            viewable
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            :label="'Şifre Onayı'"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="'Şifre Onayı'"
            viewable
        />
        
        <!-- Role Selection -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Rol</label>
            <select wire:model.live="role" class="w-full border border-white/30 rounded-lg px-3 py-2 focus:ring-2 focus:ring-white bg-white/10 text-white backdrop-blur-sm">
                <option value="doctor" class="text-gray-900">Doktor</option>
                <option value="nurse" class="text-gray-900">Hemşire</option>
                <option value="secretary" class="text-gray-900">Sekreter</option>
            </select>
            @error('role') <span class="text-red-300 text-sm">{{ $message }}</span> @enderror
        </div>
        
        <!-- Doctor Selection (for Secretary/Nurse) -->
        @if(in_array($role, ['secretary', 'nurse']))
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Atanmış Doktor</label>
                <select wire:model="doctor_id" class="w-full border border-white/30 rounded-lg px-3 py-2 focus:ring-2 focus:ring-white bg-white/10 text-white backdrop-blur-sm">
                    <option value="" class="text-gray-900">Bir doktor seçin</option>
                    @foreach($this->doctors as $doctor)
                        <option value="{{ $doctor->id }}" class="text-gray-900">{{ $doctor->name }}</option>
                    @endforeach
                </select>
                @error('doctor_id') <span class="text-red-300 text-sm">{{ $message }}</span> @enderror
            </div>
        @endif

        <div class="flex items-center justify-end">
            <button type="submit" class="w-full bg-white text-[#1e40af] hover:bg-[#1e40af] hover:text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-white/30 border-2 border-white">
                Hesap Oluştur
            </button>
        </div>
    </form>

    <div class="text-center text-sm text-gray-700">
        <span>Zaten bir hesabınız var mı?</span>
        <a href="{{ route('login') }}" class="ml-1 text-blue-600 hover:text-blue-800 font-semibold transition-colors duration-200 underline" wire:navigate>Giriş Yap</a>
    </div>
</div>
