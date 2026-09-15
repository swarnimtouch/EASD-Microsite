<?php

use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SiteSettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\FAQController;
use App\Http\Controllers\Admin\WebinarController;
use App\Http\Controllers\Admin\SpecialityController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\WebinarCommentController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\CertificateController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.login');
});
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('post_login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('/user', UserController::class);
    Route::post('/user/delete-multiple', [UserController::class, 'deleteMultiple'])->name('user.deleteMultiple');
    Route::get('users/datatable', [UserController::class, 'datatable'])->name('user.datatable');
    Route::get('/my-profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/password', [ProfileController::class, 'password'])
        ->name('password');
    Route::post('check-email-exists', [ProfileController::class, 'checkEmailExists'])->name('check-email-exists');
    Route::post('check-mobile-exists', [ProfileController::class, 'checkMobileExists'])->name('check-mobile-exists');

    Route::post('/password/update', [ProfileController::class, 'updatePassword'])
        ->name('password.update');
    Route::get('site-settings', [SiteSettingsController::class, 'index'])->name('settings');
    Route::post('site-settings/update', [SiteSettingsController::class, 'update'])->name('settings.update');


    Route::get('content', [ContentController::class, 'index'])->name('content');
    Route::get('content/add-edit/{id?}', [ContentController::class, 'edit'])->name('content.add_edit_form');
    Route::put('content/update/{id}', [ContentController::class, 'update'])->name('content.update');
    Route::get('content/datatable', [ContentController::class, 'datatable'])->name('content.datatable');

    Route::prefix('faqs')->group(function () {
        Route::get('/', [FAQController::class, 'index'])->name('faqs');
        Route::get('/datatable', [FAQController::class, 'datatable'])->name('faqs.datatable');
        Route::get('/add-edit/{id?}', [FAQController::class, 'addEditForm'])->name('faqs.add_edit_form');
        Route::match(['POST', 'PUT'], '/save/{id?}', [FAQController::class, 'save'])->name('faqs.save');
        Route::post('/status-change/{id}', [FAQController::class, 'statusChange'])->name('faqs.status_change');
        Route::post('/delete/{id}', [FAQController::class, 'delete'])->name('faqs.delete');
        Route::post('/delete-multiple', [FAQController::class, 'deleteMultiple'])->name('faqs.delete_multiple');
    });

    Route::prefix('doctors')->group(function () {
        Route::get('/', [DoctorController::class, 'index'])->name('doctors');
        Route::get('/datatable', [DoctorController::class, 'datatable'])->name('doctors.datatable');
        Route::get('/export', [DoctorController::class, 'export'])->name('doctors.export');
        Route::get('/add-edit/{id?}', [DoctorController::class, 'addEditForm'])->name('doctors.add_edit_form');
        Route::match(['POST', 'PUT'], '/save/{id?}', [DoctorController::class, 'save'])->name('doctors.save');
        Route::post('/status-change/{id}', [DoctorController::class, 'statusChange'])->name('doctors.status_change');
        Route::post('/delete/{id}', [DoctorController::class, 'delete'])->name('doctors.delete');
        Route::post('/delete-multiple', [DoctorController::class, 'deleteMultiple'])->name('doctors.delete_multiple');
    });

    Route::prefix('webinars')->group(function () {
        Route::get('/', [WebinarController::class, 'index'])->name('webinars');
        Route::get('/datatable', [WebinarController::class, 'datatable'])->name('webinars.datatable');
        Route::get('/add-edit/{id?}', [WebinarController::class, 'addEditForm'])->name('webinars.add_edit_form');
        Route::match(['POST', 'PUT'], '/save/{id?}', [WebinarController::class, 'save'])->name('webinars.save');
        Route::post('/status-change/{id}', [WebinarController::class, 'statusChange'])->name('webinars.status_change');
        Route::post('/delete/{id}', [WebinarController::class, 'delete'])->name('webinars.delete');
        Route::post('/delete-multiple', [WebinarController::class, 'deleteMultiple'])->name('webinars.delete_multiple');
    });

    Route::prefix('specialities')->group(function () {
        Route::get('/', [SpecialityController::class, 'index'])->name('specialities');
        Route::get('/datatable', [SpecialityController::class, 'datatable'])->name('specialities.datatable');
        Route::get('/add-edit/{id?}', [SpecialityController::class, 'addEditForm'])->name('specialities.add_edit_form');
        Route::match(['POST', 'PUT'], '/save/{id?}', [SpecialityController::class, 'save'])->name('specialities.save');
        Route::post('/status-change/{id}', [SpecialityController::class, 'statusChange'])->name('specialities.status_change');
        Route::post('/delete/{id}', [SpecialityController::class, 'delete'])->name('specialities.delete');
    });

    Route::prefix('countries')->group(function () {
        Route::get('/', [CountryController::class, 'index'])->name('countries');
        Route::get('/datatable', [CountryController::class, 'datatable'])->name('countries.datatable');
        Route::get('/add-edit/{id?}', [CountryController::class, 'addEditForm'])->name('countries.add_edit_form');
        Route::match(['POST', 'PUT'], '/save/{id?}', [CountryController::class, 'save'])->name('countries.save');
        Route::post('/status-change/{id}', [CountryController::class, 'statusChange'])->name('countries.status_change');
        Route::post('/delete/{id}', [CountryController::class, 'delete'])->name('countries.delete');
    });

    Route::prefix('webinar-comments')->group(function () {
        Route::get('/', [WebinarCommentController::class, 'index'])->name('webinar_comments');
        Route::post('/status-change/{comment}', [WebinarCommentController::class, 'statusChange'])->name('webinar_comments.status_change');
        Route::delete('/{comment}', [WebinarCommentController::class, 'delete'])->name('webinar_comments.delete');
    });

    Route::prefix('modules')->group(function () {
        Route::get('/', [ModuleController::class, 'index'])->name('modules');
        Route::get('/datatable', [ModuleController::class, 'datatable'])->name('modules.datatable');
        Route::get('/add-edit/{id?}', [ModuleController::class, 'addEditForm'])->name('modules.add_edit_form');
        Route::match(['POST', 'PUT'], '/save/{id?}', [ModuleController::class, 'save'])->name('modules.save');
        Route::post('/status-change/{module}', [ModuleController::class, 'statusChange'])->name('modules.status_change');
        Route::post('/delete-multiple', [ModuleController::class, 'deleteMultiple'])->name('modules.delete_multiple');
        Route::post('/delete/{module}', [ModuleController::class, 'delete'])->name('modules.delete');
    });

    Route::prefix('certificates')->group(function () {
        Route::get('/', [CertificateController::class, 'index'])->name('certificates');
        Route::get('/edit/{webinar}', [CertificateController::class, 'edit'])->name('certificates.edit');
        Route::post('/update/{webinar}', [CertificateController::class, 'update'])->name('certificates.update');
        Route::get('/preview/{webinar}', [CertificateController::class, 'preview'])->name('certificates.preview');
    });

});
