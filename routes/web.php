<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');
Route::get('/download-data.csv', [DashboardController::class, 'download'])->name('public-data.download');
Route::get('/sample-data/{type}.csv', function (string $type) {
    $samples = [
        'issues' => 'issues.csv',
        'interventions' => 'interventions.csv',
        'resources' => 'resources.csv',
        'laws-policies' => 'laws-policies.csv',
    ];

    abort_unless(isset($samples[$type]), 404);

    return response()->download(base_path('data/samples/'.$samples[$type]));
})->name('sample-data.download');
