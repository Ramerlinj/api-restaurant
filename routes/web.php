<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Use the default l5-swagger route defined by the package (configurable via l5-swagger.php).
