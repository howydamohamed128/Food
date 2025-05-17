<?php

use App\Models\Order;
use App\Lib\TabbyService;
use PhpOffice\PhpWord\PhpWord;
use App\Models\Catalog\Category;
use PhpOffice\PhpWord\Writer\HTML;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ReservationTimeIsClosestNotification;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
// */
// Route::redirect('/', 'admin');


Route::get('/', [HomeController::class, 'index'])->name('website');


