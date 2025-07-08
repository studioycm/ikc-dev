<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // redirect to /admin
    return redirect('/admin');
});

Route::get('/login', fn() => redirect(route('filament.admin.auth.login'))
)->name('login');
