<?php

use App\Http\Controllers\ChannelController;
use App\Http\Controllers\MediaGroupController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VideoController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');

    Route::resource('posts', PostController::class)->except(['show']);

    Route::post('/photos/upload', [PhotoController::class, 'upload'])->name('photos.upload');
    Route::delete('/photos/upload-undo', [PhotoController::class, 'uploadUndo'])->name('photos.upload.undo');
    Route::resource('photos', PhotoController::class)->except(['show']);
   
    Route::post('/videos/upload', [VideoController::class, 'upload'])->name('videos.upload');
    Route::delete('/videos/upload-undo', [VideoController::class, 'uploadUndo'])->name('videos.upload.undo');
    Route::resource('videos', VideoController::class)->except(['show']);
   
    // Route::get('/medias/{id}/{filename}', [MediaGroupController::class, 'fetch'])
    //     ->where(['id' => '[0-9]+', 'filename' => '[A-Za-z0-9.]+'])
    //     ->name('medias.fetch');
    Route::post('/medias/upload', [MediaGroupController::class, 'upload'])->name('medias.upload');
    Route::delete('/medias/upload-undo', [MediaGroupController::class, 'uploadUndo'])->name('medias.upload.undo');
    Route::resource('medias', MediaGroupController::class, ['parameters' => [
        'media' => 'media_group' //type-hinted variable for model biding
    ]])->except(['show']);
    
    Route::resource('polls', PollController::class)->except(['show']);

    Route::get('/channel/edit', [ChannelController::class, 'edit'])->name('channels.edit');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
