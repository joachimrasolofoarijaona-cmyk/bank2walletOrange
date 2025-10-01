<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\DoSubscriptionController;
use App\Http\Controllers\SubscribeController;
use App\Http\Controllers\subscribeValidationController;
use App\Http\Controllers\ActivateServiceController;
use App\Http\Controllers\UnsubscribeController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\OMRequestController;
use App\Http\Controllers\AnalyticsController;
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


Route::get('/', function () {
    return view('authentication');
});

# __Login routes 
Route::get('/login', [AuthenticationController::class, 'login']);
Route::post('/login', [AuthenticationController::class, 'authentication'])->name('login');

# routes/web.php
Route::post('/logout', [AuthenticationController::class, 'destroy'])->name('logout');

# __Orange sandbox request route__
# __Route::post('/omrequest', [OMRequestController::class, 'handle']);

Route::post('/omrequest', [OMRequestController::class, 'handle'])
    ->withoutMiddleware('check.session')
    ->withoutMiddleware('auth');

# __Route de test temporaire sans middleware__
Route::get('/test-accueil', [IndexController::class, 'showIndex'])->name('test.accueil');

Route::middleware(['check.session'])->group(function () {
    #__ index view __
    Route::get('accueil', [IndexController::class, 'showIndex'])->name('show.index');
    # __validation step__ #
    # __register__
    Route::get('/subscribe', [SubscribeController::class, 'showSubscription'])->name('show.subscribe');
    # __send register__
    Route::post('/subscribe', [SubscribeController::class, 'sendSubscription'])->name('send.subscribe');
    Route::get('/subscribe-validition', [subscribeValidationController::class, 'subscribeValidation'])->name('sub.validation');

    # __Validation sub__
   
    Route::post('/subscribe/sub-confirmation', [DoSubscriptionController::class, 'getCustomerMusoni'])->name('confirm.sub');
    Route::get('/subscribe/sub-confirmation', [DoSubscriptionController::class, 'showSubscriptionForm'])->name('show.sub.form');
    Route::post('/send-validation', [DoSubscriptionController::class, 'sendValidationRequest'])->name('send.subscription.validation');

    # __activate service__
    Route::get('/activate-service', [ActivateServiceController::class, 'showActivationForm'])->name('show.activation.form');
    Route::post('/activate-service', [ActivateServiceController::class, 'activateService'])->name('activate.service');

    # Route::post('/subscribe-validition', [subscribeValidationController::class, 'sendValidation'])->name('send.validation');
    Route::post('/validate',  [subscribeValidationController::class, 'doValidation'])->name('do.validation');

    # __unsubscribe__
    Route::get('/unsubscribe', [UnsubscribeController::class, 'showUnsubscribeForm'])->name('show.unsubscribe.form');
    Route::post('/unsubscribe', [UnsubscribeController::class, 'searchCustomer'])->name('search.customer');
    Route::post('/do-validation', [UnsubscribeController::class, 'sendValidation'])->name('send.unsubscribe.validation');
    Route::post('/do-unsubscribe', [UnsubscribeController::class, 'doUnsubscribe'])->name('do.unsubscribe');

    # __contract__
    Route::get('/contract', [ContractController::class, 'showContractPage'])->name('show.contract');
    Route::post('/contract', [ContractController::class, 'generateContract'])->name('generate.contract');
    
    # __Settings__
    Route::get('/settings', [SettingsController::class, 'showSettings'])->name('show.settings');
    Route::post('/update-settings', [SettingsController::class, 'updateSettings'])->name('update.settings');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    # __Analytics__
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/export', [AnalyticsController::class, 'export'])->name('analytics.export');
    Route::get('/analytics/export/transactions-series', [AnalyticsController::class, 'exportTransactionsSeries'])->name('analytics.export.transactions');
    Route::get('/analytics/export/charges-series', [AnalyticsController::class, 'exportChargesSeries'])->name('analytics.export.charges');
    Route::get('/analytics/export/amounts-series', [AnalyticsController::class, 'exportAmountsSeries'])->name('analytics.export.amounts');
    Route::get('/analytics/transactions-list', [AnalyticsController::class, 'transactionsList'])->name('analytics.transactions');
    Route::get('/analytics/export/top-offices-count', [AnalyticsController::class, 'exportTopOfficesCount'])->name('analytics.export.top.offices.count');
    Route::get('/analytics/export/top-offices-amount', [AnalyticsController::class, 'exportTopOfficesAmount'])->name('analytics.export.top.offices.amount');
    Route::get('/analytics/export/top-libelles', [AnalyticsController::class, 'exportTopLibelles'])->name('analytics.export.top.libelles');
});
