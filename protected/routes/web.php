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

Auth::routes();
Route::get('/', 'AdminController@dashboard');
Route::get('/home', 'AdminController@dashboard');
Route::get('/dashboard', 'AdminController@dashboard');
Route::get('/stock_master', 'AdminController@stockMaster');
Route::get('/bs_stock', 'AdminController@bsStock');
Route::get('/stock', 'AdminController@stock');
Route::get('/store', 'AdminController@store');
Route::get('/company', 'AdminController@company');
Route::get('/translate', 'AdminController@translate');
Route::get('/shift', 'AdminController@shift');
Route::get('/shift_dipinjam', 'AdminController@shiftDipinjam');
Route::get('/phpjs', 'JavascriptController@phpjs');
Route::get('/language_en', 'AdminController@languageEn');
Route::get('/language_jp', 'AdminController@languageJp');
Route::get('/report_store', 'AdminController@reportStore');
Route::get('/report_all', 'AdminController@reportAll');
Route::get('/report_api', 'AdminController@reportApi');
Route::get('/report_all_api', 'AdminController@reportAllApi');
Route::get('/store_group', 'AdminController@storeGroup');
Route::get('/unregister', 'AdminController@unregister');
Route::get('/report_dealer', 'AdminController@reportDealer');
Route::get('/new_report_api', 'AdminController@newReportApi');
Route::get('/new_report_store', 'AdminController@newReport');
Route::get('/new_report_all', 'AdminController@newReportAll');
Route::get('/close_book', 'AdminController@closeBook');
Route::get('/close_book_api', 'AdminController@closeBookApi');
Route::get('/delete_unregister/{jan_code}', 'Data\UnregisterController@delete');

Route::group(['prefix' => 'data', 'namespace' => 'Data'], function () {
	Route::resource('stock_master', 'StockMasterController');
	Route::resource('bs_stock', 'BsStockController');
	Route::resource('stock', 'StockController');
	Route::resource('store', 'StoreController');
	Route::resource('translate', 'TranslateController');
	Route::resource('company', 'CompanyController');
	Route::resource('shift', 'ShiftController');
	Route::resource('shift_dipinjam', 'ShiftDipinjamController');
	Route::resource('group', 'StoreGroupController');
	Route::resource('user', 'UserController');
	Route::resource('unregister', 'UnregisterController');
	Route::post('reset', 'ResetController@resetData');
});

Route::group(['prefix' => 'excel', 'middleware' => 'auth'], function () {
	Route::get('stock_master', 'ExcelController@importStockMasterForm');
	Route::get('bs_stock', 'ExcelController@importBsStockForm');
	Route::get('stock', 'ExcelController@importStockForm');
	Route::get('shift', 'ExcelController@exportShift');
	Route::get('export_report_all', 'ExcelController@exportReportAll');
	Route::get('export_report_dealer', 'ExcelController@exportReportDealer');
	Route::get('unregister', 'ExcelController@unregister');
	Route::get('close_book', 'ExcelController@importCloseBookForm');
	Route::post('export_stock', 'ExcelController@exportStock');
	Route::post('export_report', 'ExcelController@exportReport');
	Route::post('export_close_book', 'ExcelController@exportCloseBook');
	Route::post('stock', 'ExcelController@importStock');
	Route::post('bs_stock', 'ExcelController@importBsStock');
	Route::post('stock_master', 'ExcelController@importStockMaster');
	Route::post('close_book', 'ExcelController@importCloseBook');
});