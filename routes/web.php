<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TeamController;


Route::middleware('auth')->group(function () {
    //RESTfulルートを自動的に生成
    Route::post('photo/{photo}/favorite', [PhotoController::class, 'favorite'])->name('favorite');
    Route::post('photo/{photo}/unfavorite', [PhotoController::class, 'unfavorite'])->name('unfavorite');
    Route::get('photo/memory', [PhotoController::class, 'memoryindex'])->name('memory');
    Route::resource('photo', PhotoController::class);
    
    Route::resource('tag', TagController::class);
    Route::get('tag/{tag}', [TagController::class,'memoedit'])->name('memoedit');
    Route::put('tag/{tag}/edit', [TagController::class,'update'])->name('tag.update');
    
    // Route::post('/tag', [TagController::class,'create'])->name('setting.tag');
    // Route::get('/setting/tag', [TagController::class, 'setting'])->name('setting.tag');
    
});



Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware('auth')->group(function () {	
    Route::get('/team/create', [TeamController::class, 'create'])->name('team.create');
    Route::post('/tag/firstcreate', [TeamController::class, 'register'])->name('team.firstcreate');	
    
    Route::get('/team/option', [TeamController::class, 'option'])->name('team.option');
    
    Route::get('/team/join', [TeamController::class, 'join'])->name('team.join');	
    Route::post('/team/join', [TeamController::class, 'store'])->name('team.join');	
    
    
});




require __DIR__.'/auth.php';
