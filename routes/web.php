<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\TourasPayoutController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Admin/Home');
});

Route::get('/SendPayout', function () {
    return Inertia::render('Admin/SendPayout');
})->name('home');

Route::get('/TourasPayout', function () {
    return Inertia::render('Admin/TourasPayout');
})->name('touras.payout');

Route::post('/payout/send', [PayoutController::class, 'send'])->name('payout.send');
Route::post('/payout/decrypt', [PayoutController::class, 'decryptPayload'])->name('payout.decrypt');
Route::get('/payout/decrypt', function () {
    return Inertia::render('Admin/DecryptPayload');
});

Route::post('/touras/add-beneficiary', [TourasPayoutController::class, 'addBeneficiary'])->name('touras.addBeneficiary');
Route::post('/touras/payout-with-bene', [TourasPayoutController::class, 'payoutWithBene'])->name('touras.payoutWithBene');
Route::post('/touras/get-bene-list', [TourasPayoutController::class, 'getBeneList'])->name('touras.getBeneList');
Route::post('/touras/payout-without-bene', [TourasPayoutController::class, 'payoutWithoutBene'])->name('touras.payoutWithoutBene');