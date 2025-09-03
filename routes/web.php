<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'role.redirect'])->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'role.redirect'])
    ->name('dashboard');

Route::get('/settings', function () {
    return view('settings');
})->middleware('auth')->name('settings');

Route::get('/patients', function () {
    return view('patients');
})->middleware('auth')->name('patients');

Route::get('/operations', function () {
    return view('operations');
})->middleware('auth')->name('operations');

Route::get('/reports', function () {
    return view('reports');
})->middleware('auth')->name('reports');

Route::get('/messages', function () {
    return view('messages');
})->middleware('auth')->name('messages');

Route::get('/doctor-panel', function () {
    return view('doctor-panel');
})->middleware(['auth', 'role.redirect'])->name('doctor-panel');

Route::get('/clinic', function () {
    return view('clinic');
})->middleware('auth')->name('clinic');

Route::middleware(['auth'])->group(function () {
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
