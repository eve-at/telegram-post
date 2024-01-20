<?php

use App\Http\Controllers\ChannelController;
use App\Http\Controllers\ConceptController;
use App\Http\Controllers\MediaGroupController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SessionChannelController;
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
        'canLogin' => false, //Route::has('login'),
        'canRegister' => false, //Route::has('register'),
        //'laravelVersion' => Application::VERSION,
        //'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/messages', [MessageController::class, 'index'])
        ->name('messages.index');

    Route::get('/messages/{start}/{end}', [MessageController::class, 'dates'])
        ->name('messages.dates');
    
    Route::resource('channels', ChannelController::class)->except(['show']);
    Route::patch('/session_channel/{channel}/update', [SessionChannelController::class, 'update'])->name('session_channel.update');
    
    Route::resource('posts', PostController::class)->except(['show']);
    
    Route::post('/photos/upload', [PhotoController::class, 'upload'])->name('photos.upload');
    Route::delete('/photos/upload-undo', [PhotoController::class, 'uploadUndo'])->name('photos.upload.undo');
    Route::resource('photos', PhotoController::class)->except(['show']);
   
    Route::post('/videos/upload', [VideoController::class, 'upload'])->name('videos.upload');
    Route::delete('/videos/upload-undo', [VideoController::class, 'uploadUndo'])->name('videos.upload.undo');
    Route::resource('videos', VideoController::class)->except(['show']);
   
    Route::post('/media/upload', [MediaGroupController::class, 'upload'])->name('media.upload');
    Route::delete('/media/upload-undo', [MediaGroupController::class, 'uploadUndo'])->name('media.upload.undo');
    Route::resource('media', MediaGroupController::class, ['parameters' => [
        'media' => 'media' //type-hinted variable for model biding
        //'media_group' => 'media' //type-hinted variable for model biding
    ]])->except(['show']);
    
    Route::resource('polls', PollController::class)->except(['show']);
    
    Route::post('/concepts/store', [ConceptController::class, 'store'])->name('concepts.post');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
