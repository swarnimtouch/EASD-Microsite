<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Auth\WebLoginController;
use App\Http\Controllers\Website\WebinarCommentController;
use Illuminate\Support\Facades\Mail;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/login', [WebLoginController::class, 'login'])->name('login');
Route::post('/login', [WebLoginController::class, 'loginPost'])->name('login.post');
Route::get('/dashboard', [WebLoginController::class, 'dashboard'])->name('dashboard');
Route::get('/webinars', [WebLoginController::class, 'webinars'])->middleware('auth:web')->name('webinars');
Route::get('/modules', [WebLoginController::class, 'modules'])->middleware('auth:web')->name('modules');
Route::get('/snippets', [WebLoginController::class, 'snippets'])->middleware('auth:web')->name('snippets');
Route::get('/certificate/{id?}', [WebLoginController::class, 'certificatePage'])->middleware('auth:web')->name('certificate.page');
Route::post('/check-email', [HomeController::class, 'checkEmail'])->name('check_email');
Route::post('/check-mobile', [HomeController::class, 'checkMobile'])->name('check_mobile');
Route::get('/register', [WebLoginController::class, 'register'])->name('register');
Route::post('/register', [WebLoginController::class, 'registerPost'])->name('register.store');
Route::get('/webinar/{id?}', [HomeController::class, 'webinar'])->middleware('auth:web')->name('webinar');
Route::get('/webinar/{webinar}/certificate', [HomeController::class, 'certificate'])->middleware('auth:web')->name('webinar.certificate');
Route::get('/webinar/{webinar}/certificate-template-file', [HomeController::class, 'certificateTemplateFile'])->name('webinar.certificate_template_file');
Route::get('/webinar/{webinar}/post-read-download', [HomeController::class, 'downloadPostRead'])->middleware('auth:web')->name('webinar.post_read.download');
Route::post('/webinar/{webinar}/comments', [WebinarCommentController::class, 'store'])->middleware('auth:web')->name('webinar.comments.store');
Route::post('/webinar/{webinar}/comments/{comment}/upvote', [WebinarCommentController::class, 'upvote'])->middleware('auth:web')->name('webinar.comments.upvote');
Route::get('/logout', [HomeController::class, 'logout'])->name('logout');
