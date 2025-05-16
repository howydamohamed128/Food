<?php

use App\Lib\TabbyService;
use App\Models\Catalog\Category;
use App\Models\Order;
use App\Notifications\ReservationTimeIsClosestNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Writer\HTML;


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
// Route::get('testo', function () {
//     // $order = Order::latest()->first();
//     // return view('mails.order-invoice',['order' => $order]);
//     // $htmlTemplate = Category::latest()->first()->description;

// //    $phpWord = new PhpWord();
// //    $section = $phpWord->addSection();
// //    Html::addHtml($section, $htmlTemplate);
// //    $targetFile = __DIR__ . "/1.docx";
// //    $phpWord->save($targetFile, 'Word2007');
// });
// //Route::group(['prefix' => GlobalLaravelLocalization::setLocale()], function () {
//    // Your other localized routes...
//
//    Livewire::setUpdateRoute(function ($handle) {
//        return Route::post('/livewire/update', $handle);
//    });
//});
//Route::group([
//    'prefix' => GlobalLaravelLocalization::setLocale(),
//    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
//], function () {
//    Route::get('/', [HomeContoller::class, 'index'])->name('home');
//    Route::get('/about-us', [HomeContoller::class, 'aboutUs'])->name('page.about-us');
//    Route::get('/faq', [HomeContoller::class, 'faqs'])->name('page.faqs');
//    Route::get('/term', [HomeContoller::class, 'term'])->name('page.term');
//    Route::get('/privacy-policy', [HomeContoller::class, 'privacyPolicy'])->name('page.privacy-policy');
//});
//Route::middleware('localization')->prefix(GlobalLaravelLocalization::setLocale())->group(function () {

// Route::get('/', [HomeController::class, 'index'])->name('website');
// Route::post('website-form', [HomeController::class, 'store'])->name('website-form');
// Route::get('/about-us', [HomeController::class, 'aboutUs'])->name('page.about-us');
// Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('page.privacy-policy');
// Route::view('contact-us', "site.pages.contact-us")->name('page.contact-us');
// Route::post('contact-us', function (\Illuminate\Http\Request $request) {
//     $request->validate([
//         'name' => 'required|max:20',
//         'email' => 'required|email',
//         'phone' => 'required|regex:~^(5)(\d){8}$~',
//         'subject' => 'required|max:20',
//         'message' => 'required|max:1000',
//     ]);
//     $contact = Contact::create(
//         [
//             ...$request->all(),
//             'title' => $request->get('name'),
//         ]
//     );
//     return redirect()->back()->withSuccess(__('Contact message sent successfully'));
// })->name('page.contact-us.save');
// //});


// Route::redirect('/', 'admin');

// Route::get('categories/arrange', function () {
//     foreach (request()->get("list") as $record) {
//         Category::find($record['id'])->update(['parent_id' => $record['parent'] ?? null]);
//     }
// })->name('cp.categories.arrange');

// Route::get('orders/{order}/invoice', function (Order $order) {
//     return view('filament.pages.print', ['order' => $order]);
// })->name('orders.invoice');


// Route::put('admin/fcm', function () {
//     auth()->user()->deviceTokens()->delete();
//     auth()->user()->deviceTokens()->create(['token' => \request()->get('device_token')]);
//     return 200;
// })->middleware('auth')->name('admin.fcm-token');
