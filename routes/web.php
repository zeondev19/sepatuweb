<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\autentikasi;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\KaryawanController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\TransaksiController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Karyawan\CustomerController as KaryawanCustomerController;
use App\Http\Controllers\Karyawan\PelayananController;
use App\Http\Controllers\Karyawan\InvoiceController;
use App\Http\Controllers\Karyawan\ProfileController;
use App\Http\Controllers\Karyawan\LaporanController;
use App\Http\Controllers\Karyawan\SettingsController as KaryawanSettingController;
use App\Http\Controllers\Customer\SettingController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;    



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route untuk halaman utama
Route::get('/', [FrontController::class, 'index']);

// Route untuk halaman pencarian laundry
Route::get('pencarian-laundry', [FrontController::class, 'search']);

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/login', function () {
    return view('auth.login');
})->name('auth.login');

//Show Password Reset Request Form
Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

//Send Password Reset Link
Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

//Show Password Reset Form
Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

//Reset Password
Route::post('reset-password', [NewPasswordController::class, 'store'])
    ->middleware(['guest', 'throttle:6,1'])
    ->name('password.update');

Route::post('/login', [autentikasi::class, 'authenticate'])
    ->middleware('guest')
    ->name('login');

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Modul Admin
    Route::prefix('/')->middleware('role:Admin')->group(function () {
        Route::resource('admin', AdminController::class);
    
        // Pengguna/karyawan
        Route::resource('karyawan', KaryawanController::class);
        Route::get('update-satatus-karyawan', [KaryawanController::class, 'updateKaryawan']);
    
        // Customer
        Route::resource('customer', CustomerController::class);
    
        // Data Transaksi
        Route::resource('transaksi', TransaksiController::class);
        Route::get('filter-transaksi', [TransaksiController::class, 'filtertransaksi']); // filter data transaksi by karyawan
        Route::get('invoice-customer/{invoice}', [TransaksiController::class, 'invoice']); // lihat invoice
        
        Route::get('data-harga', [FinanceController::class, 'dataharga']);
        Route::post('harga-store', [FinanceController::class, 'hargastore']);
        Route::get('edit-harga', [FinanceController::class, 'hargaedit']);
        
        // Finance
        Route::get('finance', [FinanceController::class, 'index'])->name('finance.index');
        
        // Notifikasi
        Route::get('read-notification', [AdminController::class, 'notif']);
        
        // Setting
        Route::get('settings', [SettingsController::class, 'setting']);
        Route::put('proses-setting-page/{id}', [SettingsController::class, 'proses_set_page'])->name('seting-page.update');
        Route::put('set-theme/{id}', [SettingsController::class, 'set_theme'])->name('setting-theme.update');
        Route::put('set-target-laundry/{id}', [SettingsController::class, 'set_target_laundry'])->name('set-target.update');
        Route::post('add-bank', [SettingsController::class, 'bank'])->name('setting.bank');
        Route::put('set-notif/{id}', [SettingsController::class, 'notif'])->name('set-notif.update');
        
        // Profile
        Route::get('profile-admin/{id}', [AdminController::class, 'profile']);
        Route::get('profile-admin-edit', [AdminController::class, 'edit_profile']);
        
    });
    
// Modul Karyawan
    Route::prefix('/')->middleware(['role:Karyawan'])->group(function () {
        Route::resource('pelayanan', PelayananController::class);
        // Transaksi
        Route::get('add-order', [PelayananController::class, 'addorders']);
        Route::get('update-status-laundry', [PelayananController::class, 'updateStatusLaundry']);
    
        // Customer
        Route::get('customers', [CustomerController::class, 'index']);
        Route::get('customers/{id}', [CustomerController::class, 'detail']);
        Route::get('customers-create', [CustomerController::class, 'create']);
        Route::post('customers-store', [CustomerController::class, 'store']);
    
        // Filter
        Route::get('listharga', [PelayananController::class, 'listharga']);
        Route::get('listhari', [PelayananController::class, 'listhari']);
        
        // Laporan
        Route::get('laporan', [LaporanController::class, 'laporan']);
        Route::get('export-excel', [LaporanController::class, 'exportExcel']);
        
        // Invoice
        Route::get('invoice-kar/{id}', [InvoiceController::class, 'invoicekar']);
        Route::get('cetak-invoice/{id}/print', [InvoiceController::class, 'cetakinvoice']);
        
        // Profile
        Route::get('profile-karyawan/{id}', [ProfileController::class, 'karyawanProfile']);
        Route::put('profile-karyawan/update/{id}', [ProfileController::class, 'karyawanProfileSave']);
        
        // Setting
        Route::get('karyawan-setting', [SettingsController::class, 'setting']);
        Route::put('proses-setting-karyawan/{id}', [SettingsController::class, 'proses_setting_karyawan'])->name('proses-setting-karyawan.update');
        
    });
    
    // Modul Customer
    Route::prefix('/')->middleware('role:Customer')->group(function () {
        // Setting
        Route::get('setting', [SettingController::class, 'index'])->name('customer.setting');
        Route::put('setting/{id}', [SettingController::class, 'settingUpdateCustomer'])->name('customer.setting-update');
    
        // Profile
        Route::get('me', [ProfileController::class, 'index']);
        Route::put('me/{id}', [ProfileController::class, 'updateProfile']);
    });
    
Route::get('/halo', function () {
    return view('indexku'); 
});

});

