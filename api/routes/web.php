<?php

use Illuminate\Support\Facades\Route;

Route::view('/docs', 'docs.swagger')->name('docs.swagger');

Route::get('/docs/openapi.json', function () {
    return response()->file(resource_path('docs/openapi.json'), [
        'Content-Type' => 'application/json',
    ]);
})->name('docs.openapi');
