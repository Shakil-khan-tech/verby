<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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

Route::group([
  'prefix' => LaravelLocalization::setLocale(),
  'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function () {
  Route::get('/', 'PagesController@index')->name('home')->middleware(['auth']);


  Route::middleware('auth')->group(function () {
    Route::match(['get', 'post'], 'dashboard/widget_dashboard', 'DashboradController@widgetDashboard')->name('dashboard.widget_dashboard');
    Route::get('users/{user}/role', 'UserController@role')->name('users.role');
    Route::patch('users/{user}/role', 'UserController@role_update')->name('users.role_update');
    Route::match(['get', 'post'], 'users/{user}/change_active', 'UserController@change_active')->name('users.change_active');
    // Route::get('users/change_lang/{lang}', 'UserController@change_lang')->name('users.change_lang');
    // Route::get('users/change_lang', 'UserController@change_lang')->name('users.change_lang');
    Route::resource('users', 'UserController');
    Route::resource('roles', 'RoleController');

    Route::get('employees/{employee}/overview', 'EmployeeController@overview')->name('employees.overview');
    Route::get('employees/{employee}/deduction', 'EmployeeController@deduction')->name('employees.deduction');
    Route::get('employees/{employee}/insurance', 'EmployeeController@insurance')->name('employees.insurance');
    Route::get('employees/{employee}/files', 'EmployeeController@files')->name('employees.files');
    Route::post('employees/files/madia-name/{media}', 'EmployeeController@updateMediaName')->name('employees.files.updateName');
    Route::match(['get', 'post'], 'employees/{employee}/files/get', 'EmployeeController@get_files')->name('employees.files.get');


    Route::get('employees/{employee}/contracts', 'EmployeeContractController@index')->name('employees.contracts');
    Route::match(['get', 'post'], 'employees/{employee}/contracts/get', 'EmployeeContractController@get_contracts')->name('employees.contract.get');
    Route::match(['get', 'post'], 'employees/{employee}/contracts/store', 'EmployeeContractController@store_contract')->name('employees.contract.store');
    Route::match(['get', 'post'], 'employees/{employee}/{contract}/populate', 'EmployeeContractController@show')->name('employees.contract.populate');
    Route::match(['get', 'post'], 'employees/{contract}/signeddocument', 'EmployeeContractController@signedDocument')->name('employees.signed.document');
    Route::match(['get', 'post'], 'employee-contracts/{contract}/update-sign-status', 'EmployeeContractController@sign_status')->name('employee-contracts.update-sign-status');

    Route::match(['get', 'post'], 'employees/{employee}/files/store', 'EmployeeController@store_files')->name('employees.files.store');
    Route::match(['get', 'post'], 'employees/files/download/{media}', 'EmployeeController@download_files')->name('employees.files.downloaad');
    Route::match(['get', 'post'], 'employees/files/preview/{media}', 'EmployeeController@preview_files')->name('employees.files.preview_files');
    Route::match(['delete', 'put', 'patch'], 'employees/files/delete/{media}', 'EmployeeController@delete_files')->name('employees.files.delete');
    Route::match(['get', 'post', 'put', 'patch', 'delete'], 'employees/{employee}/vacation', 'EmployeeController@vacation')->name('employees.vacation');
    Route::match(['get', 'post'], 'employees/{employee}/delete_entry_date', 'EmployeeController@delete_entry_date')->name('employees.delete_entry_date');
    Route::match(['get', 'post'], 'employees/getall', 'EmployeeController@getAll')->name('employees.getAll');
    Route::match(['get', 'post'], 'employees/getall_deleted', 'EmployeeController@getall_deleted')->name('employees.getall_deleted');
    Route::match(['get', 'post'], 'employees/deleted', 'EmployeeController@deleted')->name('employees.deleted');
    Route::match(['get', 'post'], 'employees/restore', 'EmployeeController@restore')->name('employees.restore');
    Route::get('employees/statistics', 'EmployeeController@statistics')->name('employees.statistics');
    Route::match(['get', 'post'], 'employees/statistics_ajax', 'EmployeeController@statistics_ajax')->name('employees.statistics_ajax');
    Route::resource('employees', 'EmployeeController');

    Route::match(['get', 'post'], 'vacations', 'VacationController@index')->name('vacations.index');
    // Route::match(['get', 'post'], 'vacations/getall', 'VacationController@getAll')->name('vacation.getAll')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    Route::match(['get', 'post'], 'vacations/getall', 'VacationController@getAll')->name('vacation.getAll');
    Route::match(['get', 'post'], 'vacations/delete', 'VacationController@delete')->name('vacation.delete');

    Route::get('lohn/{employee}/{date}', 'LohnController@view')->name('lohn.view');
    Route::get('lohn', 'LohnController@index')->name('lohn.index');
    // Route::resource('lohn', 'LohnController');
    Route::match(['get', 'post', 'put', 'patch'], 'lohn/{lohn?}', 'LohnController@update')->name('lohn.update');

    Route::get('revision/{id}', 'LohnRevisionController@view')->name('lohnrev.view');

    Route::match(['get', 'post'], 'devices/{device}/auth', 'DeviceController@auth')->name('devices.auth');
    Route::match(['put', 'patch'], 'devices/{device}/auth', 'DeviceController@auth_update')->name('devices.auth_update');
    Route::match(['get', 'post'], 'devices/{device}/report', 'DeviceController@report')->name('devices.report');
    Route::match(['get', 'post'], 'devices/{device}/report_ajax', 'DeviceController@calendarReport')->name('devices.report_ajax');
    Route::match(['get', 'post'], 'devices/check_activity', 'DeviceController@check_activity')->name('devices.check_activity'); //job
    Route::resource('devices', 'DeviceController');
    // Route::resource('plans', 'PlanController');
    Route::get('plans', 'PlanController@index')->name('plans.index');
    Route::match(['get', 'post'], 'plans/{device}', 'PlanController@show')->name('plans.show');
    Route::match(['put', 'patch'], 'plans/{device}', 'PlanController@update')->name('plans.update');
    Route::match(['get', 'post'], 'plans/{device}/records/{inactive?}', 'PlanController@records')->name('plans.records');
    // Route::post('plans', 'PlanController@store')->name('plans.store');
    Route::match(['get', 'post'], 'records/ajax', 'RecordController@ajax')->name('records.ajax');
    Route::match(['get', 'post'], 'records/employees', 'RecordController@employees')->name('records.employees');
    Route::match(['get', 'post'], 'records/rooms/{record?}', 'RecordController@rooms')->name('records.rooms');
    Route::match(['get', 'post'], 'records/calendar', 'RecordController@calendar_index')->name('records.calendar_index');
    Route::match(['get', 'post'], 'records/calendar/{employee}', 'RecordController@calendar_show')->name('records.calendar_show');
    Route::match(['get', 'post'], 'records/calendar/{employee}/ajax', 'RecordController@calendarAjax')->name('records.calendar_ajax');
    Route::match(['put', 'patch'], 'records/calendar/{employee}/store_update', 'RecordController@calendarStoreOrUpdate')->name('records.calendar_store_update');
    Route::match(['put', 'patch'], 'records/calendar/delete', 'RecordController@calendarDelete')->name('records.calendar_delete');
    Route::match(['get', 'post'], 'records/calendar/{employee}/print', 'RecordController@calendarPrint')->name('records.calendar_print');
    Route::match(['get', 'post'], 'records/calendar/{employee}/email', 'RecordController@calendarEmail')->name('records.calendar_email');
    Route::match(['get', 'post'], 'records/calendar_report/bulkemail', 'RecordController@calendarEmailBulk')->name('records.calendar_emailbulk');
    Route::match(['get', 'post'], 'records/calendar_report', 'RecordController@calendarReport')->name('records.calendar_report');
    Route::resource('records', 'RecordController');

    Route::get('calendars', 'CalendarController@index')->name('calendars.index');
    Route::match(['get', 'post'], 'calendars/{device}', 'CalendarController@show')->name('calendars.show');
    // Route::match(['put', 'patch'], 'calendars/{device}', 'CalendarController@update')->name('calendars.update');
    Route::match(['get', 'post'], 'calendars/{device}/load', 'CalendarController@load')->name('calendars.load');
    Route::match(['get', 'post'], 'calendars/{device}/loadSingleDay', 'CalendarController@loadSingleDay')->name('calendars.loadSingleDay');
    Route::match(['get', 'post', 'patch'], 'calendars/{device}/update', 'CalendarController@update')->name('calendars.update');

    Route::match(['get', 'post'], 'pdf/calendar_horizontal/{device}/{employee}', 'PdfController@calendar_horizontal')->name('pdf.calendar_horizontal');
    Route::match(['get', 'post'], 'pdf/calendar_vertical/{device}', 'PdfController@calendar_vertical')->name('pdf.calendar_vertical');
    // Route::match(['get', 'post'], 'pdf/calendar_month/{device}', 'PdfController@calendar_month')->name('pdf.calendar_month');
    Route::match(['get', 'post'], 'pdf/issues', 'PdfController@issues')->name('pdf.issues');
    Route::match(['get', 'post'], 'pdf/supplies', 'PdfController@supplies')->name('pdf.supplies');

    Route::get('monthly_reports', 'MonthlyReportsController@index')->name('monthly_reports.index');
    Route::match(['get', 'post'], 'monthly_reports/{device}', 'MonthlyReportsController@show')->name('monthly_reports.show');
    Route::match(['put', 'patch'], 'monthly_reports/{device}', 'MonthlyReportsController@update')->name('monthly_reports.update');

    Route::get('daily_reports', 'DailyReportsController@index')->name('daily_reports.index');
    Route::match(['get', 'post'], 'daily_reports/{device}', 'DailyReportsController@show')->name('daily_reports.show');

    Route::get('daily_reports_hotel', 'DailyReportsHotelController@index')->name('daily_reports_hotel.index');
    Route::match(['get', 'post'], 'daily_reports_hotel/{device}', 'DailyReportsHotelController@show')->name('daily_reports_hotel.show');

    Route::get('monthly_performance', 'MonthlyPerformanceController@index')->name('monthly_performance.index');
    Route::match(['get', 'post'], 'monthly_performance/{device}', 'MonthlyPerformanceController@show')->name('monthly_performance.show');

    Route::get('budget', 'BudgetController@index')->name('budget.index');
    Route::match(['get', 'post'], 'budget/{device}', 'BudgetController@show')->name('budget.show');
    Route::get('/budget-excel/{device}', 'BudgetController@downloadExcel')->name('download.excel');

    Route::resource('holidays', 'HolidayController');

    Route::match(['get', 'post'], 'insurance/{employee}/{date}', 'InsuranceController@show')->name('insurance.show');
    Route::match(['get', 'post'], 'insurance_email/{employee}/{date}', 'InsuranceController@insuranceEmail')->name('insurance.email');

    Route::match(['get', 'post'], 'emails/getall', 'EmailController@getAll')->name('emails.getAll');
    Route::resource('emails', 'EmailController');

    Route::match(['get', 'post'], 'issues/listings', 'IssueController@listings')->name('issues.listings');
    Route::match(['get', 'post'], 'issues/listings/fix', 'IssueController@fix_listings')->name('issues.fix_listings');

    Route::match(['delete', 'put', 'patch'], 'issues/listings/delete/{listing}', 'IssueController@delete_listings')->name('issues.delete_listings');
    Route::match(['put', 'patch'], 'issues/listings/store', 'IssueController@store_listings')->name('issues.store_listings');
    Route::match(['get', 'post'], 'issues/listings/ajax', 'IssueController@listings_ajax')->name('issues.listings.ajax');
    Route::match(['get', 'post'], 'issues/rooms_ajax/{device}', 'IssueController@rooms_ajax')->name('issues.rooms.ajax');
    Route::resource('issues', 'IssueController');

    Route::match(['get', 'post'], 'supplies/listings', 'SupplyController@listings')->name('supplies.listings');
    Route::match(['get', 'post'], 'supplies/listings/fix', 'SupplyController@fix_listings')->name('supplies.fix_listings');
    Route::match(['delete', 'put', 'patch'], 'supplies/listings/delete/{listing}', 'SupplyController@delete_listings')->name('supplies.delete_listings');
    Route::match(['put', 'patch'], 'supplies/listings/store', 'SupplyController@store_listings')->name('supplies.store_listings');
    Route::match(['get', 'post'], 'supplies/listings/ajax', 'SupplyController@listings_ajax')->name('supplies.listings.ajax');
    Route::resource('supplies', 'SupplyController');
    Route::match(['get', 'post'], 'test', 'TestController@test')->name('test.test');

    Route::get('contracts', 'ContractController@index')->name('contracts.index');
    Route::match(['get', 'post'], 'contracts/files/store', 'ContractController@store_files')->name('contracts.files.store');
    Route::match(['get', 'post'], 'contracts/files/get', 'ContractController@get_files')->name('contracts.files.get');
    Route::get('/contracts/download/{id}', 'ContractController@download')->name('contracts.download');
    Route::delete('/contracts/{id}', 'ContractController@destroy')->name('contracts.destroy');

    Route::get('reminders', 'ReminderController@index')->name('reminders.index');
    Route::match(['get', 'post'], 'reminders/employees/get', 'ReminderController@getEmployees')->name('reminders.employees.get');

  });

  Route::middleware('signed')->group(function () {
    Route::get('external/{employee}/records/report', 'ExternalController@records_report')->name('external.records_report');
    Route::match(['get', 'post'], 'issues/listings/external/{listing}', 'IssueController@listings_external')->name('issues.listings_external'); //url signed
  });
  Route::match(['get', 'post'], 'issues/listings/external/{listing}/fix', 'IssueController@listings_external_fix')->name('issues.listings_external_fix'); //update
  Route::post('external/{employee}/records/report/feedback', 'ExternalController@records_report_feedback')->name('external.records_report_feedback');
});

/** OTHER PAGES THAT SHOULD NOT BE LOCALIZED **/


require __DIR__ . '/auth.php';
