<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GradeLevelController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\EnrollmentHistoryController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\GcashInformationController;
use App\Http\Controllers\FeeBreakdownController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\GcashTransactionController;
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
Route::get('/', function () {return view('welcome');});
Route::get('/error', function () {return view('error.index');})->name('error');
Route::middleware(['auth', 'checkRole:Developer'])->get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    return redirect()
    ->route('dashboard.developer')
    ->with([
        'success' => 'Clear successfully!',
        'icon' => 'success'
    ]);
});
Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


//Nasa BladeServiceProvider setup nito



Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function () {



Route::get('/developer', [DashboardController::class, 'developer'])->name('developer');
Route::get('/admin', [DashboardController::class, 'admin'])->name('admin');




});



Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/student/payments', [PaymentController::class, 'studentPayments'])->name('student.payments');
    Route::get('/student/fee-breakdown', [PaymentController::class, 'studentFeeBreakdown'])->name('student.fee-breakdown');
    Route::post('/student/pay-via-gcash', [PaymentController::class, 'payViaGcash'])->name('student.pay-via-gcash');
});

Route::prefix('payments')->name('payment.')->middleware(['auth'])->group(function () {


    Route::get('/admin', [PaymentController::class, 'admin'])
    ->name('admin')
    ->middleware('checkRole:Admin');


    Route::get('/cashier', [PaymentController::class, 'cashier'])
    ->name('cashier')
    ->middleware('checkRole:Cashier');

    Route::get('/search', [PaymentController::class, 'search'])->name('search');

    Route::get('/{userId}/payments', [PaymentController::class, 'getPaymentHistory'])->name('history');
    Route::get('/{userId}/fee-breakdown', [PaymentController::class, 'getFeeBreakdown'])->name('userBreakfee');
  
    Route::get('/{userId}/previous-balance', [PaymentController::class, 'getPreviousBalance']);
    Route::get('/{studentId}/payment-details', [PaymentController::class, 'getPaymentDetails']);






    Route::get('/student', [PaymentController::class, 'student'])
    ->name('student')
    ->middleware('checkRole:Student');
      



  Route::post('/gcash', [PaymentController::class, 'payViaGcash'])->name('spay-via-gcash');
  Route::post('/{userId}/walk-in', [PaymentController::class, 'storeWalkInPayment'])->name('walk-in');




    
    Route::get('/parent', [PaymentController::class, 'parent'])
    ->name('parent')
    ->middleware('checkRole:Parent');
});



Route::prefix('modules')->name('modules.')->middleware(['auth', 'checkRole:Developer'])->group(function () {
    Route::get('/', [ModuleController::class, 'index'])->name('index');
    Route::post('store', [ModuleController::class, 'store'])->name('store');  
    Route::put('{module}', [ModuleController::class, 'update'])->name('update');  
    Route::delete('{module}', [ModuleController::class, 'destroy'])->name('destroy'); 
});



Route::prefix('users')->name('users.')->middleware(['auth', 'checkRole:Developer'])->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');  
    Route::post('store', [UserController::class, 'store'])->name('store');  
    Route::put('{user}', [UserController::class, 'update'])->name('update');  
    Route::delete('{user}', [UserController::class, 'destroy'])->name('destroy');  
});



Route::prefix('settings')->name('settings.')->middleware(['auth', 'checkRole:Developer'])->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index');
    Route::post('/store', [SettingsController::class, 'store'])->name('store');  
    Route::put('/{setting}', [SettingsController::class, 'update'])->name('update');  
    Route::delete('/{setting}', [SettingsController::class, 'destroy'])->name('destroy'); 
});



Route::prefix('roles')->name('roles.')->middleware(['auth', 'checkRole:Developer'])->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('index');  
    Route::post('store', [RoleController::class, 'store'])->name('store');  
    Route::put('{role}', [RoleController::class, 'update'])->name('update');  
    Route::delete('{role}', [RoleController::class, 'destroy'])->name('destroy'); 
    Route::get('/{role}/modules', [ModuleController::class, 'getModulesForRole']);
    Route::post('/{role}/modules', [ModuleController::class, 'updateModulesForRole']);
});



Route::prefix('profiles')->name('profiles.')->middleware(['auth'])->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');  
    Route::put('{user}', [ProfileController::class, 'update'])->name('update');
    Route::put('/{user}/picture', [ProfileController::class, 'updatePicture'])->name('updatePicture');
    Route::put('/{user}/account', [ProfileController::class, 'updateAccount'])->name('updateAccount');
});



Route::prefix('grade')->name('grade.')->middleware(['auth', 'checkRole:Admin'])->group(function () {
    Route::get('/', [GradeLevelController::class, 'index'])->name('index');
    Route::post('/store', [GradeLevelController::class, 'store'])->name('store');
    Route::put('/{gradeLevel}', [GradeLevelController::class, 'update'])->name('update');
    Route::delete('/{gradeLevel}', [GradeLevelController::class, 'destroy'])->name('destroy');
    Route::put('/{gradeLevel}/update-sections', [GradeLevelController::class, 'updateSections'])->name('update-sections');
    Route::get('/{gradeLevel}/sections', [GradeLevelController::class, 'getSectionsForGrade']);
    Route::post('/{gradeLevel}/sections', [GradeLevelController::class, 'updateSectionsForGrade']);
});



Route::prefix('academic')->name('academic.')->middleware(['auth', 'checkRole:Admin'])->group(function () {
    Route::get('/', [AcademicYearController::class, 'index'])->name('index');
    Route::post('/store', [AcademicYearController::class, 'store'])->name('store');
    Route::put('/{academicYear}', [AcademicYearController::class, 'update'])->name('update');
    Route::delete('/{academicYear}', [AcademicYearController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/set-current', [AcademicYearController::class, 'setCurrent'])->name('academic.set-current');
});



Route::prefix('enrollees')->name('enrollees.')->middleware(['auth', 'checkRole:Admin'])->group(function () {
    Route::get('/get-sections/{grade}', [EnrollmentHistoryController::class, 'getSections']);
    Route::get('/', [EnrollmentHistoryController::class, 'index'])->name('index');
    Route::post('/store', [EnrollmentHistoryController::class, 'enroll'])->name('store');
    Route::put('/{enrollment}', [EnrollmentHistoryController::class, 'update'])->name('update');
    Route::delete('/{enrollment}', [EnrollmentHistoryController::class, 'destroy'])->name('destroy');
});



Route::prefix('section')->name('section.')->middleware(['auth', 'checkRole:Admin'])->group(function () {
    Route::get('/', [SectionController::class, 'index'])->name('index');
    Route::post('/store', [SectionController::class, 'store'])->name('store');
    Route::put('/{section}', [SectionController::class, 'update'])->name('update');
    Route::delete('/{section}', [SectionController::class, 'destroy'])->name('destroy');
});



Route::prefix('gcash')->name('gcash.')->middleware(['auth', 'checkRole:Admin'])->group(function () {
    Route::get('/', [GcashInformationController::class, 'index'])->name('index');


   
    Route::post('/store', [GcashInformationController::class, 'store'])->name('store');
    Route::get('/{gcashInformation}/edit', [GcashInformationController::class, 'edit'])->name('edit');
    Route::put('/{gcashInformation}', [GcashInformationController::class, 'update'])->name('update');
    Route::delete('/{gcashInformation}', [GcashInformationController::class, 'destroy'])->name('destroy');


Route::get('/mygcashtrans', [GcashInformationController::class, 'mygcash'])
->name('mygcash')
->middleware('checkRole:Student');


Route::get('/getActive', [GcashTransactionController::class, 'getActive'])
->name('getActive')
->middleware('checkRole:Student');

Route::post('/{id}/set-active', [GcashInformationController::class, 'isActive'])
->name('setActive')
->middleware('checkRole:Admin');



Route::get('/allpending', [GcashTransactionController::class, 'allpending'])
->name('allpending')
->middleware('checkRole:Cashier');

Route::post('/update-status', [GcashTransactionController::class, 'updateStatus'])->name('update-status');


    





// Route::get('/gcash-transactions', [GcashInformationController::class, 'index'])->name('index');
//     Route::get('/gcash-transactions/create', [GcashInformationController::class, 'create'])->name('create');
//     Route::post('/gcash-transactions', [GcashInformationController::class, 'store'])->name('store');
//     Route::get('/gcash-transactions/{gcashTransaction}', [GcashInformationController::class, 'show'])->name('show');
//     Route::post('/gcash-transactions/{gcashTransaction}/approve', [GcashInformationController::class, 'approve'])->name('approve');
//     Route::post('/gcash-transactions/{gcashTransaction}/reject', [GcashInformationController::class, 'reject'])->name('reject');






});



Route::prefix('fees')->name('fees.')->middleware(['auth', 'checkRole:Admin'])->group(function () {
    Route::get('/', [FeeBreakdownController::class, 'index'])->name('index');
    Route::post('/store', [FeeBreakdownController::class, 'store'])->name('store');
    Route::put('/{feeBreakdown}', [FeeBreakdownController::class, 'update'])->name('update');
    Route::delete('/{feeBreakdown}', [FeeBreakdownController::class, 'destroy'])->name('destroy');
});