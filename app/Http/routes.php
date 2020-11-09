<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('login.login');
});

Route::get('/pdf', function () {
    Fpdf::AddPage();
    Fpdf::SetFont('Courier', 'B', 38);
    Fpdf::Cell(50, 25, 'Hello Worlda!');
    Fpdf::Output();
});

// LOGIN PAGE
Route::get('login', 'LoginController@index');
Route::post('login', 'LoginController@authenticate');
// END LOGIN PAGE

// FORGET_PASSWORD
Route::any('forgetpassword', 'LoginController@forgetpassword');
Route::any('formValidation', 'LoginController@formValidation');
Route::any('addeditprocess', 'LoginController@addeditprocess');
// END_FORGET_PASSWORD

// LOGOUT PROCESS
Route::get('logout', 'Auth\AuthController@logout');
// Route::get('logout', 'LoginController@logout');
// END LOGOUT PROCESS

Route::group(['prefix'=>'Ourdetail', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'OurdetailController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('add', 'OurdetailController@add')->middleware('accessright:0,1,2,3,4');
    Route::any('edit', 'OurdetailController@edit')->middleware('accessright:0,1,2,3,4');
    Route::any('addeditprocess', 'OurdetailController@addeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('taxpopup', 'OurdetailController@taxpopup')->middleware('accessright:0,1,2,3,4');
    Route::any('balancesheetpopup', 'OurdetailController@balancesheetpopup')->middleware('accessright:0,1,2,3,4');
    Route::any('taxprocess', 'OurdetailController@taxprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('balsheetprocess', 'OurdetailController@balsheetprocess')->middleware('accessright:0,1,2,3,4');
});

// Master User
Route::group(['prefix'=>'User', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'UserController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('addedit', 'UserController@addedit')->middleware('accessright:0,1,2,3,4');
    Route::any('formValidation', 'UserController@formValidation')->middleware('accessright:0,1,2,3,4');
    Route::any('addeditprocess', 'UserController@addeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('view', 'UserController@view')->middleware('accessright:0,1,2,3,4');
    Route::any('changepassword', 'UserController@changepassword')->middleware('accessright:0,1,2,3,4');
    Route::any('passwordchangeprocess', 'UserController@passwordchangeprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('userIdExist', 'UserController@userIdExist')->middleware('accessright:0,1,2,3,4');
    Route::any('mailIdExist', 'UserController@mailIdExist')->middleware('accessright:0,1,2,3,4');
});

// Master Bank
Route::group(['prefix'=>'Bank', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'BankController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('Singleview', 'BankController@Singleview')->middleware('accessright:0,1,2,3,4');
    Route::any('addedit', 'BankController@addedit')->middleware('accessright:0,1,2,3,4');
    Route::any('banknamepopup', 'BankController@banknamepopup')->middleware('accessright:0,1,2,3,4');
    Route::any('branchnamepopup', 'BankController@branchnamepopup')->middleware('accessright:0,1,2,3,4');
    Route::any('formValidation', 'BankController@formValidation')->middleware('accessright:0,1,2,3,4');
    Route::any('bankbranchRegister', 'BankController@bankadd')->middleware('accessright:0,1,2,3,4');
    Route::any('branchRegister', 'BankController@branchadd')->middleware('accessright:0,1,2,3,4');
    Route::any('addeditprocess', 'BankController@addeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('branch_ajax', 'BankController@branch_ajax')->middleware('accessright:0,1,2,3,4');
});

// Customer Employee History
Route::group(['prefix'=>'Customer', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'CustomerController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('Onsitehistory', 'CustomerController@Onsitehistory')->middleware('accessright:0,1,2,3,4');
    Route::any('addedit', 'CustomerController@addedit')->middleware('accessright:0,1,2,3,4');
    Route::any('addeditprocess', 'CustomerController@addeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('View', 'CustomerController@View')->middleware('accessright:0,1,2,3,4');
    Route::any('empnamepopup', 'CustomerController@empnamepopup')->middleware('accessright:0,1,2,3,4');
    Route::any('branchname_ajax', 'CustomerController@branchname_ajax')->middleware('accessright:0,1,2,3,4');
    Route::any('Branchaddedit', 'CustomerController@Branchaddedit')->middleware('accessright:0,1,2,3,4');
    Route::any('Branchaddeditprocess', 'CustomerController@Branchaddeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('empnamepopupeditprocess', 'CustomerController@empnamepopupeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('Inchargeaddedit', 'CustomerController@Inchargeaddedit')->middleware('accessright:0,1,2,3,4');
    Route::any('Inchargeaddeditprocess', 'CustomerController@Inchargeaddeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('coverletterpopup', 'CustomerController@coverletterpopup')->middleware('accessright:0,1,2,3,4');
    Route::any('letterupload', 'CustomerController@letterupload')->middleware('accessright:0,1,2,3,4');
});

// Customer+ Employee History
Route::group(['prefix'=>'Engineerdetails', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'EngineerdetailsController@index')->middleware('accessright:0,1,2,3,4');
});

// Engineerdetails+ 
Route::group(['prefix'=>'Engineerdetailsplus', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'EngineerdetailsplusController@index')->middleware('accessright:0,1,2,3,4');
});

// Staff
Route::group(['prefix'=>'Staff', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'StaffController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('staffaddedit', 'StaffController@staffaddedit')->middleware('accessright:0,1,2,3,4');
    Route::any('staffaddeditprocess', 'StaffController@addeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('view', 'StaffController@view')->middleware('accessright:0,1,2,3,4');
    Route::any('importpopup', 'StaffController@importpopup')->middleware('accessright:0,1,2,3,4');
    Route::any('importprocess', 'StaffController@importprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('rejoin', 'StaffController@rejoin')->middleware('accessright:0,1,2,3,4');
    Route::any('resign', 'StaffController@resign')->middleware('accessright:0,1,2,3,4');
    Route::any('resignadd', 'StaffController@resignadd')->middleware('accessright:0,1,2,3,4');
});

// NonStaff
Route::group(['prefix'=>'NonStaff', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'NonStaffController@index')->middleware('accessright:0,1,2,3,4');
	Route::any('nonstaffadd', 'NonStaffController@nonstaffadd')->middleware('accessright:0,1,2,3,4');
	Route::any('nonstaffaddeditprocess', 'NonStaffController@nonstfaddeditprocess')->middleware('accessright:0,1,2,3,4');
	Route::any('nonstaffview', 'NonStaffController@nonstaffview')->middleware('accessright:0,1,2,3,4');
});

// Timesheet
Route::group(['prefix'=>'Timesheet', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('timesheetindex', 'TimesheetController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('importstaffpopup', 'StaffController@importpopup')->middleware('accessright:0,1,2,3,4');
    Route::any('importprocess', 'TimesheetController@importOldTimeSheetDetails')->middleware('accessright:0,1,2,3,4');
    Route::any('timeSheetHistorydetails', 'TimesheetController@timeSheetHistorydetails')->middleware('accessright:0,1,2,3,4');
    Route::any('timesheetview', 'TimesheetController@timesheetview')->middleware('accessright:0,1,2,3,4');
    Route::any('addeditreg', 'TimesheetController@addedit')->middleware('accessright:0,1,2,3,4');
    Route::any('addeditupdate', 'TimesheetController@addedit')->middleware('accessright:0,1,2,3,4');
    Route::any('singlerow1', 'TimesheetController@singlerow1')->middleware('accessright:0,1,2,3,4');
    Route::any('timeSheetReg', 'TimesheetController@timeSheetRegprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('downloadexcel', 'TimesheetController@downloadexcel')->middleware('accessright:0,1,2,3,4');
    Route::any('pdfview', 'TimesheetController@pdfview')->middleware('accessright:0,1,2,3,4');
    Route::any('uploadpopup', 'TimesheetController@uploadpopup')->middleware('accessright:0,1,2,3,4');
    Route::any('uploadprocess', 'TimesheetController@uploadprocess')->middleware('accessright:0,1,2,3,4');

});

// Billing
Route::group(['prefix'=>'Billing', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'BillingController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('empselectprocess', 'BillingController@empselectprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('staffselectpopup', 'BillingController@staffselectpopup')->middleware('accessright:0,1,2,3,4');
    Route::any('billhistory', 'BillingController@billhistory')->middleware('accessright:0,1,2,3,4');
    Route::any('billdetailview', 'BillingController@billdetailview')->middleware('accessright:0,1,2,3,4');
    Route::any('billingregister', 'BillingController@addedit')->middleware('accessright:0,1,2,3,4');
    Route::any('addeditprocess', 'BillingController@addeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('ajaxbranchname', 'BillingController@ajaxbranchname')->middleware('accessright:0,1,2,3,4');
    Route::any('getpreviousdetails', 'BillingController@getpreviousdetails')->middleware('accessright:0,1,2,3,4');
});

// Expenses
Route::group(['prefix'=>'Expenses', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'ExpensesController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('addedit', 'ExpensesController@addedit')->middleware('accessright:0,1,2,3,4');
    Route::any('cashaddedit', 'ExpensesController@cashaddedit')->middleware('accessright:0,1,2,3,4');
    Route::any('cashedit', 'ExpensesController@cashedit')->middleware('accessright:0,1,2,3,4');
    Route::any('ajaxsubsubject', 'ExpensesController@ajaxsubsubject')->middleware('accessright:0,1,2,3,4');
    Route::any('ajaxmainsubject', 'ExpensesController@ajaxmainsubject')->middleware('accessright:0,1,2,3,4');
    Route::any('addeditprocess', 'ExpensesController@addeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('cashaddeditprocess', 'ExpensesController@cashaddeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('multiregprocess', 'ExpensesController@multiregprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('edit', 'ExpensesController@edit')->middleware('accessright:0,1,2,3,4');
    Route::any('copy', 'ExpensesController@copy')->middleware('accessright:0,1,2,3,4');
    Route::any('multipleregister', 'ExpensesController@multipleregister')->middleware('accessright:0,1,2,3,4');
    Route::any('multipleregprocess', 'ExpensesController@multipleregprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('transferhistory', 'TransferController@transferhistory')->middleware('accessright:0,1,2,3,4');
    Route::any('transfersubhistory', 'TransferController@transfersubhistory')->middleware('accessright:0,1,2,3,4');
    Route::any('empnamehistory', 'TransferController@empnamehistory')->middleware('accessright:0,1,2,3,4');
    Route::any('expenseshistory', 'ExpensesController@expenseshistory')->middleware('accessright:0,1,2,3,4');
    Route::any('download', 'TransferController@download')->middleware('accessright:0,1,2,3,4');
    Route::any('pettycashdownload', 'ExpensesController@pettycashdownload')->middleware('accessright:0,1,2,3,4');
    Route::any('pettycashmainhistory', 'ExpensesController@pettycashmainhistory')->middleware('accessright:0,1,2,3,4');
    Route::any('pettycashsubhistorydownload', 'ExpensesController@pettycashsubhistorydownload')->middleware('accessright:0,1,2,3,4');
    Route::any('salaryhistorydownload', 'TransferController@salaryhistorydownload')->middleware('accessright:0,1,2,3,4');
    Route::get('twoFieldaddedit', 'SettingController@twoFieldaddedit')->middleware('accessright:0,1,2,3,4');
    Route::get('threeFieldaddedit', 'SettingController@threeFieldaddedit')->middleware('accessright:0,1,2,3,4');
    Route::any('useNotuse', 'SettingController@useNotuse')->middleware('accessright:0,1,2,3,4');
    Route::any('historydownload', 'TransferController@historydownload')->middleware('accessright:0,1,2,3,4');
    Route::any('pettycashhistory', 'ExpensesController@pettycashhistory')->middleware('accessright:0,1,2,3,4');
    Route::any('expensesmainhistorydownload', 'ExpensesController@expensesmainhistorydownload')->middleware('accessright:0,1,2,3,4');
    Route::any('expensessubhistorydownload', 'ExpensesController@expensessubhistorydownload')->middleware('accessright:0,1,2,3,4');
    Route::any('transfersubhistorydownload', 'TransferController@transfersubhistorydownload')->middleware('accessright:0,1,2,3,4');
});

// Estimation
Route::group(['prefix'=>'Estimation', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'EstimationController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('view', 'EstimationController@view')->middleware('accessright:0,1,2,3,4');
    Route::any('addedit', 'EstimationController@addedit')->middleware('accessright:0,1,2,3,4');
    Route::any('branch_ajax', 'EstimationController@branch_ajax')->middleware('accessright:0,1,2,3,4');
    Route::any('addeditprocess', 'EstimationController@addeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('sendmail', 'EstimationController@sendmail')->middleware('accessright:0,1,2,3,4');
    Route::any('sendmailprocess', 'SendmailController@sendmailprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('newpdf', 'EstimationController@newpdf')->middleware('accessright:0,1,2,3,4');
    Route::any('noticepopup', 'EstimationController@noticepopup')->middleware('accessright:0,1,2,3,4');
    Route::any('exceldownloadprocess', 'EstimationController@exceldownloadprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('browsepopup', 'EstimationController@browsepopup')->middleware('accessright:0,1,2,3,4');
    Route::any('coverdownloadprocess', 'EstimationController@coverdownloadprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('coverpopup', 'EstimationController@coverpopup')->middleware('accessright:0,1,2,3,4');
    Route::any('specification', 'InvoiceController@specification')->middleware('accessright:0,1,2,3,4');
    Route::any('newexceldownloadprocess', 'EstimationController@newexceldownloadprocess')->middleware('accessright:0,1,2,3,4');
});

// Invoice
Route::group(['prefix'=>'Invoice', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'InvoiceController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('newpdf', 'InvoiceController@newpdf')->middleware('accessright:0,1,2,3,4');
    Route::any('addedit', 'InvoiceController@addedit')->middleware('accessright:0,1,2,3,4');
    Route::any('ajaxsubsubject', 'InvoiceController@ajaxsubsubject')->middleware('accessright:0,1,2,3,4'); 
    Route::any('noticepopup', 'InvoiceController@noticepopup')->middleware('accessright:0,1,2,3,4');
    Route::any('addeditprocess', 'InvoiceController@addeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::get('ajaxgetbankdetails', 'InvoiceController@ajaxgetbankdetails')->middleware('accessright:0,1,2,3,4');
    Route::any('addeditinv', 'InvoiceController@addeditinv')->middleware('accessright:0,1,2,3,4');
    Route::any('specification', 'InvoiceController@specification')->middleware('accessright:0,1,2,3,4');   
    Route::any('sendmail', 'InvoiceController@sendmail')->middleware('accessright:0,1,2,3,4');
    Route::any('sendmailprocess', 'SendmailController@sendmailprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('exceldownloadprocess', 'InvoiceController@exceldownloadprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('paymentaddedit', 'InvoiceController@paymentaddedit')->middleware('accessright:0,1,2,3,4');
    Route::any('paymentaddeditprocess', 'PaymentController@paymentaddeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('getaccount', 'PaymentController@getaccount')->middleware('accessright:0,1,2,3,4');
    Route::any('ajaxgetbillingdetails', 'InvoiceController@ajaxgetbillingdetails')->middleware('accessright:0,1,2,3,4');
    Route::any('empnamepopup', 'InvoiceController@empnamepopup')->middleware('accessright:0,1,2,3,4');
    Route::any('invoiceexceldownloadprocess', 'InvoiceController@invoiceexceldownloadprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('assignemployee', 'InvoiceController@assignemployee')->middleware('accessright:0,1,2,3,4');
    Route::any('editempassignprocess', 'InvoiceController@editempassignprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('invoicecopy', 'InvoiceController@invoicecopy')->middleware('accessright:0,1,2,3,4');
    Route::any('invoicecopyprocess', 'InvoiceController@invoicecopyprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('newexceldownloadprocess', 'InvoiceController@newexceldownloadprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('allinvoiceexceldownloadprocess', 'InvoiceController@allinvoiceexceldownloadprocess')->middleware('accessright:0,1,2,3,4');
     Route::any('invoiceallPdfdownloadprocess', 'InvoiceController@invoiceallPdfdownloadprocess')->middleware('accessright:0,1,2,3,4');
});

// COMPANY EXPENSES - LOAN DETAILS
Route::group(['prefix'=>'Loandetails', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'LoanController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('Viewlist', 'LoanController@Viewlist')->middleware('accessright:0,1,2,3,4');
    Route::any('Singleview', 'LoanController@Singleview')->middleware('accessright:0,1,2,3,4');
    Route::any('Loanconfirm', 'LoanController@Loanconfirm')->middleware('accessright:0,1,2,3,4');
    Route::any('addedit', 'LoanController@addedit')->middleware('accessright:0,1,2,3,4');
    Route::any('edit', 'LoanController@edit')->middleware('accessright:0,1,2,3,4');
    Route::any('addeditprocess', 'LoanController@addeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('loanaddedit', 'TransferController@loanaddedit')->middleware('accessright:0,1,2,3,4');
});

// Employee History
Route::group(['prefix'=>'EmpHistory', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'EmpHistoryController@index')->middleware('accessright:0,1,2,3,4');
});

// COMPANY EXPENSES - SALARY
Route::group(['prefix'=>'Salary', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'SalaryController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('empselectionpopup', 'SalaryController@empselectionpopup')->middleware('accessright:0,1,2,3,4');
    Route::any('empselectionprocess', 'SalaryController@empselectionprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('Singleview', 'SalaryController@Singleview')->middleware('accessright:0,1,2,3,4');
    Route::any('Viewlist', 'SalaryController@Viewlist')->middleware('accessright:0,1,2,3,4');
    Route::any('addedit', 'SalaryController@addedit')->middleware('accessright:0,1,2,3,4');
    Route::any('edit', 'SalaryController@edit')->middleware('accessright:0,1,2,3,4');
    Route::any('copy', 'SalaryController@copy')->middleware('accessright:0,1,2,3,4');
    Route::any('addeditprocess', 'SalaryController@addeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('multiaddedit', 'SalaryController@multiaddedit')->middleware('accessright:0,1,2,3,4');
    Route::any('copycheck', 'SalaryController@copycheck')->middleware('accessright:0,1,2,3,4');
    Route::any('multiaddeditprocess', 'SalaryController@multiaddeditprocess')->middleware('accessright:0,1,2,3,4');
});

// Staff Contract
Route::group(['prefix'=>'StaffContr', 'middleware' => 'auth'], function() { 
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'StaffContrController@index')->middleware('accessright:0,1,2,3,4'); 
    Route::any('contractdetails', 'StaffContrController@contractdetails')->middleware('accessright:0,1,2,3,4');
    Route::any('contractview', 'StaffContrController@contractview')->middleware('accessright:0,1,2,3,4'); 
    Route::any('staffContaddeditprocess', 'StaffContrController@addeditprocess')->middleware('accessright:0,1,2,3,4'); 
    Route::any('contractdownload', 'StaffContrController@contractdownload')->middleware('accessright:0,1,2,3,4'); 
    Route::any('cdate_ajax', 'StaffContrController@cdate_ajax')->middleware('accessright:0,1,2,3,4');
    
});
    
// SEND MAIL
Route::group(['prefix'=>'Mailstatus', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'MailstatusController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('mailstatusview', 'MailstatusController@mailstatusview')->middleware('accessright:0,1,2,3,4');
    Route::any('mailhistory', 'MailstatusController@mailhistory')->middleware('accessright:0,1,2,3,4');
});

// MAIL Content
Route::group(['prefix'=>'Mailcontent', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'MailcontentController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('addedit', 'MailcontentController@addedit')->middleware('accessright:0,1,2,3,4');
    Route::any('addeditprocess', 'MailcontentController@addeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('view', 'MailcontentController@view')->middleware('accessright:0,1,2,3,4');
});

// MAIL Signature
Route::group(['prefix'=>'Mailsignature', 'middleware' => 'auth'], function() {
	Route::get('changelanguage', 'AjaxController@index')->middleware('accessright:0,1,2,3,4');
	Route::any('index', 'MailsignatureController@index')->middleware('accessright:0,1,2,3,4');
	Route::any('addedit', 'MailsignatureController@addedit')->middleware('accessright:0,1,2,3,4');
	Route::any('mailsignaturepopup', 'MailsignatureController@mailsignaturepopup')->middleware('accessright:0,1,2,3,4');
	Route::any('addeditprocess', 'MailsignatureController@addeditprocess')->middleware('accessright:0,1,2,3,4');
	Route::any('view', 'MailsignatureController@view')->middleware('accessright:0,1,2,3,4');
    Route::any('getdatexist', 'MailsignatureController@getdatexist')->middleware('accessright:0,1,2,3,4');
});

// COMPANY EXPENSES - Bankdetails
Route::group(['prefix'=>'Bankdetails', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'BankdetailController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('Viewlist', 'BankdetailController@Viewlist')->middleware('accessright:0,1,2,3,4');
    Route::any('add', 'BankdetailController@add')->middleware('accessright:0,1,2,3,4');
    Route::any('edit', 'BankdetailController@edit');
    Route::any('checked', 'BankdetailController@checked')->middleware('accessright:0,1,2,3,4');
    Route::any('addeditprocess', 'BankdetailController@addeditprocess')->middleware('accessright:0,1,2,3,4');
});

// COMPANY EXPENSES - Transfer
Route::group(['prefix'=>'Transfer', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'TransferController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('ajaxsubsubject', 'TransferController@ajaxsubsubject')->middleware('accessright:0,1,2,3,4');
    Route::any('ajaxloanname', 'TransferController@ajaxloanname')->middleware('accessright:0,1,2,3,4');
    Route::any('addedit', 'TransferController@addedit')->middleware('accessright:0,1,2,3,4');
    Route::any('edit', 'TransferController@edit')->middleware('accessright:0,1,2,3,4');
    Route::any('addeditprocess', 'TransferController@addeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('loanaddedit', 'TransferController@loanaddedit')->middleware('accessright:0,1,2,3,4');
    Route::any('loanedit', 'TransferController@loanedit')->middleware('accessright:0,1,2,3,4');
    Route::any('mulreg', 'TransferController@mulreg')->middleware('accessright:0,1,2,3,4');
    Route::any('multiregprocess', 'TransferController@multiregprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('loanaddeditprocess', 'TransferController@loanaddeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('download', 'TransferController@download')->middleware('accessright:0,1,2,3,4');
    Route::any('transferhistory', 'TransferController@transferhistory')->middleware('accessright:0,1,2,3,4');
    Route::get('twoFieldaddedit', 'SettingController@twoFieldaddedit')->middleware('accessright:0,1,2,3,4');
    Route::get('threeFieldaddedit', 'SettingController@threeFieldaddedit')->middleware('accessright:0,1,2,3,4');
    Route::any('empnamehistory', 'TransferController@empnamehistory')->middleware('accessright:0,1,2,3,4');
    Route::any('transfersubhistory', 'TransferController@transfersubhistory')->middleware('accessright:0,1,2,3,4');
    Route::any('useNotuse', 'SettingController@useNotuse')->middleware('accessright:0,1,2,3,4');
    Route::any('historydownload', 'TransferController@historydownload')->middleware('accessright:0,1,2,3,4');
    Route::any('salaryhistorydownload', 'TransferController@salaryhistorydownload')->middleware('accessright:0,1,2,3,4');
    Route::any('transfersubhistorydownload', 'TransferController@transfersubhistorydownload')->middleware('accessright:0,1,2,3,4');
    Route::any('empnamehistory', 'TransferController@empnamehistory')->middleware('accessright:0,1,2,3,4');
    Route::any('copy', 'ExpensesController@copy')->middleware('accessright:0,1,2,3,4');
    Route::any('transferexceldownload', 'TransferController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('editothersprocess', 'TransferController@editothersprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('others', 'TransferController@others')->middleware('accessright:0,1,2,3,4');
    Route::any('empnamepopup', 'TransferController@empnamepopup')->middleware('accessright:0,1,2,3,4');
});

// MeetingDetails 
Route::group(['prefix'=>'MeetingDetails', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'MeetingdetailsController@index')->middleware('accessright:0,1,2,3,4');  
    Route::any('view', 'MeetingdetailsController@view')->middleware('accessright:0,1,2,3,4');
    Route::any('meetingaddedit', 'MeetingdetailsController@meetingaddedit')->middleware('accessright:0,1,2,3,4'); 
    Route::any('branch_ajax', 'MeetingdetailsController@branch_ajax')->middleware('accessright:0,1,2,3,4');
    Route::any('addeditprocess', 'MeetingdetailsController@addeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('meetinghistory', 'MeetingdetailsController@meetinghistory')->middleware('accessright:0,1,2,3,4'); 
    Route::any('newcustomerpopup', 'MeetingdetailsController@newcustomerpopup')->middleware('accessright:0,1,2,3,4'); 
    Route::any('newcustomerregpopup', 'MeetingdetailsController@newcustomerregpopup')->middleware('accessright:0,1,2,3,4');
    Route::any('cust_name_exist', 'MeetingdetailsController@cust_name_exist')->middleware('accessright:0,1,2,3,4');
    Route::any('getmettingtiming', 'MeetingdetailsController@getmettingtiming')->middleware('accessright:0,1,2,3,4');
    Route::any('customerregister', 'MeetingdetailsController@newcustomerregpopup')->middleware('accessright:0,1,2,3,4');
    
});

// Sales - Payment
Route::group(['prefix'=>'Payment', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'PaymentController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('PaymentEdit', 'PaymentController@PaymentEdit')->middleware('accessright:0,1,2,3,4');
    Route::any('getaccount', 'PaymentController@getaccount')->middleware('accessright:0,1,2,3,4');
    Route::any('paymentaddeditprocess', 'PaymentController@paymentaddeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('customerview', 'PaymentController@customerview')->middleware('accessright:0,1,2,3,4');
    Route::any('customerspecification', 'PaymentController@specificationview')->middleware('accessright:0,1,2,3,4');
});

// Home
Route::group(['prefix'=>'Menu', 'middleware' => 'auth'], function() {
    Route::get('index', 'MenuController@index')->middleware('accessright:0,1,2,3,4');
    Route::get('changelanguage', 'AjaxController@index');
});

// Expenses Details
Route::group(['prefix'=>'ExpensesDetails', 'middleware' => 'auth'], function() {
    Route::any('index', 'ExpensesDetailsController@index')->middleware('accessright:0,1,2,3,4');
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('transferhistory', 'TransferController@transferhistory')->middleware('accessright:0,1,2,3,4');
    Route::any('transfersubhistory', 'TransferController@transfersubhistory')->middleware('accessright:0,1,2,3,4');
});

// Setting
Route::group(['prefix'=>'Setting', 'middleware' => 'auth'], function() {
Route::any('index', 'SettingController@index');
Route::get('changelanguage', 'AjaxController@index')->middleware('accessright:0,1,2,3,4');
Route::get('singletextpopup', 'SettingController@singletextpopup')->middleware('accessright:0,1,2,3,4');
Route::any('SingleFieldaddedit', 'SettingController@SingleFieldaddedit')->middleware('accessright:0,1,2,3,4');
Route::get('twotextpopup', 'SettingController@twotextpopup')->middleware('accessright:0,1,2,3,4');
Route::get('twoFieldaddedit', 'SettingController@twoFieldaddedit')->middleware('accessright:0,1,2,3,4');
Route::get('selectthreefieldDatasforbank', 'SettingController@selectthreefieldDatas')->middleware('accessright:0,1,2,3,4');
Route::get('selectthreefieldDatas', 'SettingController@selectthreefieldDatas')->middleware('accessright:0,1,2,3,4');
Route::get('threeFieldaddeditforbank', 'SettingController@threeFieldaddeditforbank')->middleware('accessright:0,1,2,3,4');
Route::get('threeFieldaddedit', 'SettingController@threeFieldaddedit')->middleware('accessright:0,1,2,3,4');
Route::any('uploadpopup', 'SettingController@uploadpopup')->middleware('accessright:0,1,2,3,4');
Route::any('useNotuse', 'SettingController@useNotuse')->middleware('accessright:0,1,2,3,4');
Route::any('settingpopupupload', 'SettingController@settingpopupupload')->middleware('accessright:0,1,2,3,4');
Route::get('selectcrditCardDatas', 'SettingController@selectcrditCardDatas')->middleware('accessright:0,1,2,3,4');
Route::get('creditAddEdit', 'SettingController@creditAddEdit')->middleware('accessright:0,1,2,3,4');


});

// Staff -> Salary
Route::group(['prefix'=>'StaffSalary','middleware' => 'auth'], function() {
    Route::any('index', 'StaffSalaryController@index')->middleware('accessright:0,1,2,3,4');
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('salaryview', 'StaffSalaryController@salaryview')->middleware('accessright:0,1,2,3,4');
    Route::any('viewsalary', 'StaffSalaryController@viewsalary')->middleware('accessright:0,1,2,3,4');
    Route::any('salarystaff_ajax', 'StaffSalaryController@salarystaff_ajax')->middleware('accessright:0,1,2,3,4');
    Route::any('singleview', 'StaffSalaryController@singleview')->middleware('accessright:0,1,2,3,4');
});

// Sales Details
Route::group(['prefix'=>'Salesdetails', 'middleware' => 'auth'], function() {
	Route::any('index', 'SalesdetailsController@index')->middleware('accessright:0,1,2,3,4');
	Route::any('salesexceldownloadprocess', 'SalesdetailsController@index')->middleware('accessright:0,1,2,3,4');
	Route::get('changelanguage', 'AjaxController@index');
});

Route::group(['prefix'=>'Salesplus', 'middleware' => 'auth'], function() {
	Route::any('index', 'SalesplusController@index')->middleware('accessright:0,1,2,3,4');
	Route::any('salesexceldownloadprocess', 'SalesplusController@index')->middleware('accessright:0,1,2,3,4');
	Route::get('changelanguage', 'AjaxController@index');
});

// Visa Renew
Route::group(['prefix'=>'Visarenew', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'VisarenewController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('visaimportpopup', 'VisarenewController@visaimportpopup')->middleware('accessright:0,1,2,3,4');
    Route::any('importprocess', 'VisarenewController@importprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('addedit', 'VisarenewController@addedit')->middleware('accessright:0,1,2,3,4');
    Route::any('addeditprocess', 'VisarenewController@addeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('visaview', 'VisarenewController@visaview')->middleware('accessright:0,1,2,3,4');
    Route::any('visaExtensionFormDownload', 'VisarenewController@visaExtensionFormDownload')->middleware('accessright:0,1,2,3,4');
});

// Tax Details Process
Route::group(['prefix'=>'Tax','middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('index', 'TaxController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('taximportpopup', 'TaxController@taximportpopup')->middleware('accessright:0,1,2,3,4');
    Route::any('importprocess', 'TaxController@importprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('taxPersonalDownload', 'TaxController@taxPersonalDownload')->middleware('accessright:0,1,2,3,4');
    Route::any('taxview', 'TaxController@taxview')->middleware('accessright:0,1,2,3,4');
    Route::any('familyselectionprocess', 'TaxController@familyselectionprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('empselectionpopup', 'TaxController@empselectionpopup')->middleware('accessright:0,1,2,3,4');
    Route::any('empselectionprocess', 'TaxController@empselectionprocess')->middleware('accessright:0,1,2,3,4');
});

// Accounting Process by Rajesh
Route::group(['prefix'=>'Accounting','middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('index', 'AccountingController@index')->middleware('accessright:0,1,2,3,4,5');
    Route::any('getcashDetails', 'AccountingController@getcashDetails')->middleware('accessright:0,1,2,3,4');
    Route::any('commitProcess', 'AccountingController@commitProcess')->middleware('accessright:0,1,2,3,4');
    // Cash // Rajesh
    Route::any('bank_ajax', 'AccountingController@bank_ajax')->middleware('accessright:0,1,2,3,4');
    Route::any('addedit', 'AccountingController@addedit')->middleware('accessright:0,1,2,3,4');
    Route::any('cashedit', 'AccountingController@cashedit')->middleware('accessright:0,1,2,3,4');
    Route::any('addeditprocess', 'AccountingController@addeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('getInvoicePopup', 'AccountingController@getInvoicePopup')->middleware('accessright:0,1,2,3,4');
    // Transfer // Sarath
    Route::any('empnamepopup', 'AccountingController@empnamepopup')->middleware('accessright:0,1,2,3,4');
    Route::any('getsalarypopup', 'AccountingController@getsalarypopup')->middleware('accessright:0,1,2,3,4');
    Route::any('transferaddedit', 'AccountingController@transferaddedit')->middleware('accessright:0,1,2,3,4');
    Route::any('transferedit', 'AccountingController@transferedit')->middleware('accessright:0,1,2,3,4');
    Route::any('tranferaddeditprocess', 'AccountingController@tranferaddeditprocess')->middleware('accessright:0,1,2,3,4');
    // Auto Debit // Sastha
    Route::any('getloanpopup', 'AccountingController@getloanpopup')->middleware('accessright:0,1,2,3,4');
    Route::any('autoDebitReg', 'AccountingController@autoDebitReg')->middleware('accessright:0,1,2,3,4');
    Route::any('autoDebitedit', 'AccountingController@autoDebitedit')->middleware('accessright:0,1,2,3,4');
    Route::any('AutoDebitRegprocess', 'AccountingController@AutoDebitRegprocess')->middleware('accessright:0,1,2,3,4');
     // Invoice // Sastha
    Route::any('invoiceaddeditprocess', 'AccountingController@invoiceaddeditprocess')->middleware('accessright:0,1,2,3,4');
});


// AccBankDetail Process by Rajesh
Route::group(['prefix'=>'AccBankDetail','middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'AccBankDetailController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('add', 'AccBankDetailController@add')->middleware('accessright:0,1,2,3,4');
    Route::any('addeditprocess', 'AccBankDetailController@addeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('Viewlist', 'AccBankDetailController@Viewlist')->middleware('accessright:0,1,2,3,4');
});

Route::group(['prefix'=>'CreditCardPay','middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'CreditCardPayController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('addedit', 'CreditCardPayController@addedit')->middleware('accessright:0,1,2,3,4');
    Route::any('addeditprocess', 'CreditCardPayController@addeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('creditCardAddDtls', 'CreditCardPayController@creditCardAddDtls')->middleware('accessright:0,1,2,3,4');
    Route::any('detailsaddedit', 'CreditCardPayController@detailsaddedit')->middleware('accessright:0,1,2,3,4');
    Route::any('detailseditprocess', 'CreditCardPayController@detailseditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('GetInsMonth_ajax', 'CreditCardPayController@GetInsMonth_ajax')->middleware('accessright:0,1,2,3,4');
    Route::any('deleteRecords', 'CreditCardPayController@deleteRecords')->middleware('accessright:0,1,2,3,4');
});

// Auditing Process by Rajesh
Route::group(['prefix'=>'Auditing','middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'AuditingController@index')->middleware('accessright:0,1,2,3,4,5');
    Route::any('auditingexldwnldprocess', 'AuditingController@auditingexldwnldprocess')->middleware('accessright:0,1,2,3,4,5');
    Route::any('invoiceallPdfdownloadAudit', 'AuditingController@invoiceallPdfdownloadAudit')->middleware('accessright:0,1,2,3,4,5');
    Route::any('confirmProcess_ajax', 'AuditingController@confirmProcess_ajax')->middleware('accessright:0,1,2,3,4,5');
    
});

// Salary Calculation Process By Sastha
Route::group(['prefix'=>'salarycalc','middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('index', 'SalarycalcController@index')->middleware('accessright:0,1,2,3,4');
    Route::any('view', 'SalarycalcController@view')->middleware('accessright:0,1,2,3,4');
    Route::any('edit', 'SalarycalcController@edit')->middleware('accessright:0,1,2,3,4');
    Route::any('addedit', 'SalarycalcController@addedit')->middleware('accessright:0,1,2,3,4');
    Route::any('addeditprocess', 'SalarycalcController@addeditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('multieditprocess', 'SalarycalcController@multieditprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('multiregister', 'SalarycalcController@multiregister')->middleware('accessright:0,1,2,3,4');
    Route::any('salarypopup', 'SalarycalcController@salarypopup')->middleware('accessright:0,1,2,3,4');
    Route::any('empselectprocess', 'SalarycalcController@empselectprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('mailsendprocess', 'SalarycalcController@mailsendprocess')->middleware('accessright:0,1,2,3,4');
    Route::any('salarydownload', 'SalarycalcController@salarydownload')->middleware('accessright:0,1,2,3,4');
    Route::any('history', 'SalarycalcController@history')->middleware('accessright:0,1,2,3,4');
    Route::any('getdataExists', 'SalarycalcController@getdataExists')->middleware('accessright:0,1,2,3,4');
    Route::any('dataReg', 'SalarycalcController@dataReg')->middleware('accessright:0,1,2,3,4');
    Route::get('getlastmonthdet', 'SalarycalcController@getlastmonthdet')->middleware('accessright:0,1,2,3,4');
});

// Salary Calculation Plus Process By Sastha
Route::group(['prefix'=>'salarycalcplus','middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'SalarycalcplusController@index');
    Route::any('view', 'SalarycalcplusController@view');
    Route::any('edit', 'SalarycalcplusController@edit');
    Route::any('addedit', 'SalarycalcplusController@addedit');
    Route::any('addeditprocess', 'SalarycalcplusController@addeditprocess');
    Route::any('multieditprocess', 'SalarycalcplusController@multieditprocess');
    Route::any('multiregister', 'SalarycalcplusController@multiregister');
    Route::any('salarypopup', 'SalarycalcplusController@salarypopup');
    Route::any('empselectprocess', 'SalarycalcplusController@empselectprocess');
    Route::any('mailsendprocess', 'SalarycalcplusController@mailsendprocess');
    Route::any('history', 'SalarycalcplusController@history');
    Route::any('getdataExists', 'SalarycalcplusController@getdataExists');
    Route::any('dataReg', 'SalarycalcplusController@dataReg');
    Route::get('getsalamount', 'SalarycalcplusController@getajaxtotamt');
    Route::get('getlastmonthdet', 'SalarycalcplusController@getlastmonthdet');
    Route::any('salarydownloadprocess', 'SalarycalcplusController@salarydownloadprocess');
    Route::any('transferdetailsdownload', 'SalarycalcplusController@transferdetailsdownload');
    Route::any('salaryplusdownload', 'SalarycalcplusController@salaryplusdownload');
    Route::any('salaryplusPayrollSingleDownload', 'SalarycalcplusController@salaryplusPayrollSingleDownload');
    Route::any('gensenDownload', 'SalarycalcplusController@gensenDownload');
    Route::any('getTransferedAmount', 'SalarycalcplusController@getTransferedAmount');
    // Start Madasamy Code 22/05/20
    Route::any('historyTotal', 'SalarycalcplusController@historyTotal');
    // End Madasamy Code 22/05/20
});

// Audit Payment Screen Process by Rajesh
Route::group(['prefix'=>'AudPayment','middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'AudPaymentController@index')->middleware('accessright:0,1,2,3,4,5');
    Route::any('customerspecification', 'AudPaymentController@specificationview')->middleware('accessright:0,1,2,3,4,5');
    Route::any('customerview', 'AudPaymentController@customerview')->middleware('accessright:0,1,2,3,4,5');
});