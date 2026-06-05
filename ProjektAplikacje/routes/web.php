<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\BillItemController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('welcome'); });

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');

    Route::resource('groups', GroupController::class)->except(['create']);
    Route::post('/groups/{group}/add-user', [GroupController::class, 'addUser'])->name('groups.add-user');

    Route::post('/groups/{group}/bills', [BillController::class, 'store'])->name('bills.store');
    Route::delete('/groups/{group}/bills/{bill}', [BillController::class, 'destroy'])->name('bills.destroy');
    Route::post('/groups/{group}/bills/{bill}/items', [BillItemController::class, 'store'])->name('bill-items.store');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
