<?php

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

Route::get('/', 'Ecommerce\FrontController@index')->name('front.index');

Route::get('/product', 'Ecommerce\FrontController@product')->name('front.product');
Route::get('/category/{slug}', 'Ecommerce\FrontController@categoryProduct')->name('front.category');
Route::get('/product/{slug}', 'Ecommerce\FrontController@show')->name('front.show_product');

Route::post('cart', 'Ecommerce\CartController@addToCart')->name('front.cart');
Route::get('/cart', 'Ecommerce\CartController@listCart')->name('front.list_cart');
Route::post('/cart/update', 'Ecommerce\CartController@updateCart')->name('front.update_cart');

Route::get('/checkout', 'Ecommerce\CartController@checkout')->name('front.checkout');
Route::post('/checkout', 'Ecommerce\CartController@processCheckout')->name('front.store_checkout');
Route::get('/checkout/{invoice}', 'Ecommerce\CartController@checkoutFinish')->name('front.finish_checkout');

Route::get('/getOngkir/{origin}/{destination}/{weight}/{courier}', 'Ecommerce\CartController@getOngkir')->name('front.cekOngkir');

// Login
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@postLogin')->name('post.newLogin');

// Register
Route::get('/register', 'Auth\LoginController@newRegister')->name('register');
Route::post('/register', 'Auth\LoginController@postRegister')->name('post.newRegister');

// Forgot Password
Route::get('/forgot-password', 'Auth\ResetPasswordController@forgotPasswordForm')->name('forgotPassword');

// Verifikasi Email
Route::get('verify/{token}', 'SellerController@verifySellerRegistration')->name('seller.verify');

// Logout
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// // Auth::routes();
// Route::match(['get', 'post'], '/register', function () {
//     return redirect('/login');
// })->name('register');

Route::group(['prefix' => 'administrator', 'middleware' => 'auth'], function() {
    
    // Admin -> Dashboard
    Route::get('/home', 'HomeController@index')->name('home');

    // Admin -> Account Setting
    Route::get('/account-setting/{id}', 'HomeController@accountSetting')->name('user.acountSetting');
    Route::put('/post-account-setting/{id}', 'HomeController@postAccountSetting')->name('user.postAccountSetting');

    // Admin -> Konsumen
    Route::get('/consumen', 'CustomerController@index')->name('consumen.index');
    Route::get('/consumen/add-consumen', 'CustomerController@create')->name('consumen.create');
    Route::post('/consumen/store-consumen', 'CustomerController@store')->name('consumen.store');
    Route::delete('/consumen/delete-consumen/{id}', 'CustomerController@destroy')->name('consumen.destroy');
    Route::get('/consumen/edit-consumen/{id}', 'CustomerController@edit')->name('consumen.edit');
    Route::put('/consumen/update-consumen/{id}', 'CustomerController@update')->name('consumen.update');

    // Admin -> Pedagang
    Route::get('/seller', 'SellerController@index')->name('seller.newIndex');
    Route::get('/seller/add-seller', 'SellerController@create')->name('seller.create');
    Route::post('/seller/store-seller', 'SellerController@store')->name('seller.store');
    Route::delete('/seller/delete-seller/{id}', 'SellerController@destroy')->name('seller.destroy');
    Route::get('/seller/edit-seller/{id}', 'SellerController@edit')->name('seller.edit');
    Route::put('/seller/update-seller/{id}', 'SellerController@update')->name('seller.update');

    // Store
    // Route::get('/consumen', 'CustomerController@index')->name('consumen.index');
    // Route::get('/consumen/add-consumen', 'CustomerController@create')->name('customer.create');
    // Route::post('/consumen/store-consumen', 'CustomerController@store')->name('customer.store');
    // Route::delete('/consumen/delete-consumen/{id}', 'CustomerController@destroy')->name('customer.destroy');
    // Route::get('/consumen/edit-consumen/{id}', 'CustomerController@edit')->name('customer.edit');
    // Route::put('/consumen/update-consumen/{id}', 'CustomerController@update')->name('customer.update');

    // Admin -> Kategori
    Route::get('/category', 'CategoryController@index')->name('category.index');
    Route::get('/category/add-category', 'CategoryController@create')->name('category.create');
    Route::post('/category/store-category', 'CategoryController@store')->name('category.store');
    Route::delete('/category/delete-category/{id}', 'CategoryController@destroy')->name('category.destroy');
    Route::get('/category/edit-category/{id}', 'CategoryController@edit')->name('category.edit');
    Route::put('/category/update-category/{id}', 'CategoryController@update')->name('category.update');

    // Admin -> Produk
    Route::get('/product', 'ProductController@index')->name('product.index');
    Route::get('/product/add-product', 'ProductController@create')->name('product.create');
    Route::post('/product/store-product', 'ProductController@store')->name('product.store');
    Route::delete('/product/delete-product/{id}', 'ProductController@destroy')->name('product.destroy');
    Route::get('/product/edit-product/{id}', 'ProductController@edit')->name('product.edit');
    Route::put('/product/update-product/{id}', 'ProductController@update')->name('product.update');
    Route::get('/product/bulk', 'ProductController@massUploadForm')->name('product.bulk'); 
    Route::post('/product/bulk', 'ProductController@massUpload')->name('product.saveBulk');

    // Admin -> Pesanan
    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', 'OrderController@index')->name('orders.index');
        Route::get('/{invoice}', 'OrderController@view')->name('orders.view');
        Route::get('/payment/{invoice}', 'OrderController@acceptPayment')->name('orders.approve_payment');
        Route::post('/shipping', 'OrderController@shippingOrder')->name('orders.shipping');
        Route::get('/return/{invoice}', 'OrderController@return')->name('orders.return');
        Route::post('/return', 'OrderController@approveReturn')->name('orders.approve_return');
        Route::delete('/{id}', 'OrderController@destroy')->name('orders.destroy');
    });

    // Admin -> Laporan 
    Route::group(['prefix' => 'reports'], function() {
        Route::match(['get', 'post'], '/', function () {
            return redirect('administrator/reports/order');
        });
        Route::get('/order', 'OrderController@orderReport')->name('report.order');
        Route::get('/reportorder/{daterange}', 'OrderController@orderReportPdf')->name('report.order_pdf');
        Route::get('/return', 'OrderController@returnReport')->name('report.return');
        Route::get('/reportreturn/{daterange}', 'OrderController@returnReportPdf')->name('report.return_pdf');
    });
});

Route::group(['prefix' => 'seller', 'namespace' => 'Seller', 'middleware' => 'seller'], function() {

    // Penjual -> Account Setting
    Route::get('/account-setting/{id}', 'SellerController@accountSetting')->name('seller.setting');
    Route::put('/post-account-setting/{id}', 'SellerController@postAccountSetting')->name('seller.postSetting');

    // Penjual -> Dashboard
    Route::get('/home', 'SellerController@index')->name('seller.dashboard');

    // Penjual -> Kategori
    Route::get('/category', 'CategoryController@index')->name('category.newIndex');
    Route::get('/category/add-category', 'CategoryController@create')->name('category.newCreate');
    Route::post('/category/store-category', 'CategoryController@store')->name('category.newStore');
    Route::delete('/category/delete-category/{id}', 'CategoryController@destroy')->name('category.newDestroy');
    Route::get('/category/edit-category/{id}', 'CategoryController@edit')->name('category.newEdit');
    Route::put('/category/update-category/{id}', 'CategoryController@update')->name('category.newUpdate');

    // Penjual -> Produk
    Route::get('/product', 'ProductController@index')->name('product.newIndex');
    Route::get('/product/add-product', 'ProductController@create')->name('product.newCreate');
    Route::post('/product/store-product', 'ProductController@store')->name('product.newStore');
    Route::delete('/product/delete-product/{id}', 'ProductController@destroy')->name('product.newDestroy');
    Route::get('/product/edit-product/{id}', 'ProductController@edit')->name('product.newEdit');
    Route::put('/product/update-product/{id}', 'ProductController@update')->name('product.newUpdate');
    Route::get('/product/bulk', 'ProductController@massUploadForm')->name('product.bulk'); 
    Route::post('/product/bulk', 'ProductController@massUpload')->name('product.saveBulk');

    // Penjual -> Pesanan
    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', 'OrderController@index')->name('orders.newIndex');
        Route::get('/{invoice}', 'OrderController@view')->name('orders.newView');
        Route::get('/payment/{invoice}', 'OrderController@acceptPayment')->name('orders.new_approve_payment');
        Route::post('/shipping', 'OrderController@shippingOrder')->name('orders.newShipping');
        Route::get('/return/{invoice}', 'OrderController@return')->name('orders.newReturn');
        Route::post('/return', 'OrderController@approveReturn')->name('orders.new_approve_return');
        Route::delete('/{id}', 'OrderController@destroy')->name('orders.newDestroy');
    });

    // Penjual -> Laporan
    Route::group(['prefix' => 'reports'], function() {
        Route::match(['get', 'post'], '/', function () {
            return redirect('seller/reports/order');
        });
        Route::get('/order', 'OrderController@orderReport')->name('report.newOrder');
        Route::get('/reportorder/{daterange}', 'OrderController@orderReportPdf')->name('new_report.order_pdf');
        Route::get('/return', 'OrderController@returnReport')->name('report.newReturn');
        Route::get('/reportreturn/{daterange}', 'OrderController@returnReportPdf')->name('new_report.return_pdf');
    });
    
});

Route::group(['prefix' => 'member', 'namespace' => 'Ecommerce'], function() {
    Route::match(['get', 'post'], '/', function () {
        return redirect('member/dashboard');
    });
    Route::get('login', 'LoginController@loginForm')->name('customer.login');
    Route::post('login', 'LoginController@login')->name('customer.post_login');
    Route::get('verify/{token}', 'FrontController@verifyCustomerRegistration')->name('customer.verify');
    Route::get('register', 'RegisterController@registerForm')->name('customer.register');
    Route::get('forgot-password', 'LoginController@forgotPassword')->name('customer.forgotPassword');
    Route::post('register', 'RegisterController@register')->name('customer.post_register');
    Route::post('reset-password', 'LoginController@resetPassword')->name('customer.resetPassword');
    Route::group(['middleware' => 'customer'], function() {
        Route::get('dashboard', 'LoginController@dashboard')->name('customer.dashboard');
        Route::get('orders', 'OrderController@index')->name('customer.orders');
        Route::get('orders/{invoice}', 'OrderController@view')->name('customer.view_order');
        Route::get('orders/pdf/{invoice}', 'OrderController@pdf')->name('customer.order_pdf');
        Route::post('orders/accept', 'OrderController@acceptOrder')->name('customer.order_accept');
        Route::get('orders/return/{invoice}', 'OrderController@returnForm')->name('customer.order_return');
        Route::put('orders/return/{invoice}', 'OrderController@processReturn')->name('customer.return');
        Route::get('payment/{invoice}', 'OrderController@paymentForm')->name('customer.paymentForm');
        Route::post('payment/save', 'OrderController@storePayment')->name('customer.savePayment');
        Route::get('setting', 'FrontController@customerSettingForm')->name('customer.settingForm');
        Route::post('setting', 'FrontController@customerUpdateProfile')->name('customer.setting');
        Route::get('wishlists', 'WishlistController@index')->name('customer.wishlist');
        Route::post('wishlists', 'WishlistController@saveWishlist')->name('customer.save_wishlist');
        Route::delete('wishlists/{id}', 'WishlistController@deleteWishlist')->name('customer.deleteWishlist');
        Route::get('logout', 'LoginController@logout')->name('customer.logout'); 
    });
});
