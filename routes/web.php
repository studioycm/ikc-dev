<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // redirect to /admin
    return redirect('/admin');
});

