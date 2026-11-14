<?php

use App\Http\Controllers\PortfolioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PortfolioController::class, 'index'])->name('portfolio.index');
Route::get('/projects/{project:slug}', [PortfolioController::class, 'show'])->name('portfolio.project.show');
Route::post('/contact', [PortfolioController::class, 'contact'])->name('portfolio.contact.store');

