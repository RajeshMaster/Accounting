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
    Route::any('index', 'OurdetailController@index');
    Route::any('add', 'OurdetailController@add');
    Route::any('edit', 'OurdetailController@edit');
    Route::any('addeditprocess', 'OurdetailController@addeditprocess');
    Route::any('taxpopup', 'OurdetailController@taxpopup');
    Route::any('balancesheetpopup', 'OurdetailController@balancesheetpopup');
    Route::any('taxprocess', 'OurdetailController@taxprocess');
    Route::any('balsheetprocess', 'OurdetailController@balsheetprocess');
});

// Master User
Route::group(['prefix'=>'User', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'UserController@index');
    Route::any('addedit', 'UserController@addedit');
    Route::any('formValidation', 'UserController@formValidation');
    Route::any('addeditprocess', 'UserController@addeditprocess');
    Route::any('view', 'UserController@view');
    Route::any('changepassword', 'UserController@changepassword');
    Route::any('passwordchangeprocess', 'UserController@passwordchangeprocess');
    Route::any('userIdExist', 'UserController@userIdExist');
    Route::any('mailIdExist', 'UserController@mailIdExist');
});

// Master Bank
Route::group(['prefix'=>'Bank', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'BankController@index');
    Route::any('Singleview', 'BankController@Singleview');
    Route::any('addedit', 'BankController@addedit');
    Route::any('banknamepopup', 'BankController@banknamepopup');
    Route::any('branchnamepopup', 'BankController@branchnamepopup');
    Route::any('formValidation', 'BankController@formValidation');
    Route::any('bankbranchRegister', 'BankController@bankadd');
    Route::any('branchRegister', 'BankController@branchadd');
    Route::any('addeditprocess', 'BankController@addeditprocess');
    Route::any('branch_ajax', 'BankController@branch_ajax');
});

// Customer Employee History
Route::group(['prefix'=>'Customer', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'CustomerController@index');
    Route::any('Onsitehistory', 'CustomerController@Onsitehistory');
    Route::any('addedit', 'CustomerController@addedit');
    Route::any('addeditprocess', 'CustomerController@addeditprocess');
    Route::any('View', 'CustomerController@View');
    Route::any('empnamepopup', 'CustomerController@empnamepopup');
    Route::any('branchname_ajax', 'CustomerController@branchname_ajax');
    Route::any('Branchaddedit', 'CustomerController@Branchaddedit');
    Route::any('Branchaddeditprocess', 'CustomerController@Branchaddeditprocess');
    Route::any('empnamepopupeditprocess', 'CustomerController@empnamepopupeditprocess');
    Route::any('Inchargeaddedit', 'CustomerController@Inchargeaddedit');
    Route::any('Inchargeaddeditprocess', 'CustomerController@Inchargeaddeditprocess');
    Route::any('coverletterpopup', 'CustomerController@coverletterpopup');
    Route::any('letterupload', 'CustomerController@letterupload');
});

// Customer+ Employee History
Route::group(['prefix'=>'Engineerdetails', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'EngineerdetailsController@index');
});

// Engineerdetails+ 
Route::group(['prefix'=>'Engineerdetailsplus', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'EngineerdetailsplusController@index');
});

// Staff
Route::group(['prefix'=>'Staff', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'StaffController@index');
    Route::any('staffaddedit', 'StaffController@staffaddedit');
    Route::any('staffaddeditprocess', 'StaffController@addeditprocess');
    Route::any('view', 'StaffController@view');
    Route::any('importpopup', 'StaffController@importpopup');
    Route::any('importprocess', 'StaffController@importprocess');
    Route::any('rejoin', 'StaffController@rejoin');
    Route::any('resign', 'StaffController@resign');
    Route::any('resignadd', 'StaffController@resignadd');
});

// NonStaff
Route::group(['prefix'=>'NonStaff', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'NonStaffController@index');
	Route::any('nonstaffadd', 'NonStaffController@nonstaffadd');
	Route::any('nonstaffaddeditprocess', 'NonStaffController@nonstfaddeditprocess');
	Route::any('nonstaffview', 'NonStaffController@nonstaffview');
});

// Timesheet
Route::group(['prefix'=>'Timesheet', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('timesheetindex', 'TimesheetController@index');
    Route::any('importstaffpopup', 'StaffController@importpopup');
    Route::any('importprocess', 'TimesheetController@importOldTimeSheetDetails');
    Route::any('timeSheetHistorydetails', 'TimesheetController@timeSheetHistorydetails');
    Route::any('timesheetview', 'TimesheetController@timesheetview');
    Route::any('addeditreg', 'TimesheetController@addedit');
    Route::any('addeditupdate', 'TimesheetController@addedit');
    Route::any('singlerow1', 'TimesheetController@singlerow1');
    Route::any('timeSheetReg', 'TimesheetController@timeSheetRegprocess');
    Route::any('downloadexcel', 'TimesheetController@downloadexcel');
    Route::any('pdfview', 'TimesheetController@pdfview');
    Route::any('uploadpopup', 'TimesheetController@uploadpopup');
    Route::any('uploadprocess', 'TimesheetController@uploadprocess');

});

// Billing
Route::group(['prefix'=>'Billing', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'BillingController@index');
    Route::any('empselectprocess', 'BillingController@empselectprocess');
    Route::any('staffselectpopup', 'BillingController@staffselectpopup');
    Route::any('billhistory', 'BillingController@billhistory');
    Route::any('billdetailview', 'BillingController@billdetailview');
    Route::any('billingregister', 'BillingController@addedit');
    Route::any('addeditprocess', 'BillingController@addeditprocess');
    Route::any('ajaxbranchname', 'BillingController@ajaxbranchname');
    Route::any('getpreviousdetails', 'BillingController@getpreviousdetails');
});

// Expenses
Route::group(['prefix'=>'Expenses', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'ExpensesController@index');
    Route::any('addedit', 'ExpensesController@addedit');
    Route::any('cashaddedit', 'ExpensesController@cashaddedit');
    Route::any('cashedit', 'ExpensesController@cashedit');
    Route::any('ajaxsubsubject', 'ExpensesController@ajaxsubsubject');
    Route::any('ajaxmainsubject', 'ExpensesController@ajaxmainsubject');
    Route::any('addeditprocess', 'ExpensesController@addeditprocess');
    Route::any('cashaddeditprocess', 'ExpensesController@cashaddeditprocess');
    Route::any('multiregprocess', 'ExpensesController@multiregprocess');
    Route::any('edit', 'ExpensesController@edit');
    Route::any('copy', 'ExpensesController@copy');
    Route::any('multipleregister', 'ExpensesController@multipleregister');
    Route::any('multipleregprocess', 'ExpensesController@multipleregprocess');
    Route::any('transferhistory', 'TransferController@transferhistory');
    Route::any('transfersubhistory', 'TransferController@transfersubhistory');
    Route::any('empnamehistory', 'TransferController@empnamehistory');
    Route::any('expenseshistory', 'ExpensesController@expenseshistory');
    Route::any('download', 'TransferController@download');
    Route::any('pettycashdownload', 'ExpensesController@pettycashdownload');
    Route::any('pettycashmainhistory', 'ExpensesController@pettycashmainhistory');
    Route::any('pettycashsubhistorydownload', 'ExpensesController@pettycashsubhistorydownload');
    Route::any('salaryhistorydownload', 'TransferController@salaryhistorydownload');
    Route::get('twoFieldaddedit', 'SettingController@twoFieldaddedit');
    Route::get('threeFieldaddedit', 'SettingController@threeFieldaddedit');
    Route::any('useNotuse', 'SettingController@useNotuse');
    Route::any('historydownload', 'TransferController@historydownload');
    Route::any('pettycashhistory', 'ExpensesController@pettycashhistory');
    Route::any('expensesmainhistorydownload', 'ExpensesController@expensesmainhistorydownload');
    Route::any('expensessubhistorydownload', 'ExpensesController@expensessubhistorydownload');
    Route::any('transfersubhistorydownload', 'TransferController@transfersubhistorydownload');
});

// Estimation
Route::group(['prefix'=>'Estimation', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'EstimationController@index');
    Route::any('view', 'EstimationController@view');
    Route::any('addedit', 'EstimationController@addedit');
    Route::any('branch_ajax', 'EstimationController@branch_ajax');
    Route::any('addeditprocess', 'EstimationController@addeditprocess');
    Route::any('sendmail', 'EstimationController@sendmail');
    Route::any('sendmailprocess', 'SendmailController@sendmailprocess');
    Route::any('newpdf', 'EstimationController@newpdf');
    Route::any('noticepopup', 'EstimationController@noticepopup');
    Route::any('exceldownloadprocess', 'EstimationController@exceldownloadprocess');
    Route::any('browsepopup', 'EstimationController@browsepopup');
    Route::any('coverdownloadprocess', 'EstimationController@coverdownloadprocess');
    Route::any('coverpopup', 'EstimationController@coverpopup');
    Route::any('specification', 'InvoiceController@specification');
    Route::any('newexceldownloadprocess', 'EstimationController@newexceldownloadprocess');
});

// Invoice
Route::group(['prefix'=>'Invoice', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'InvoiceController@index');
    Route::any('newpdf', 'InvoiceController@newpdf');
    Route::any('addedit', 'InvoiceController@addedit');
    Route::any('ajaxsubsubject', 'InvoiceController@ajaxsubsubject'); 
    Route::any('noticepopup', 'InvoiceController@noticepopup');
    Route::any('addeditprocess', 'InvoiceController@addeditprocess');
    Route::get('ajaxgetbankdetails', 'InvoiceController@ajaxgetbankdetails');
    Route::any('addeditinv', 'InvoiceController@addeditinv');
    Route::any('specification', 'InvoiceController@specification');   
    Route::any('sendmail', 'InvoiceController@sendmail');
    Route::any('sendmailprocess', 'SendmailController@sendmailprocess');
    Route::any('exceldownloadprocess', 'InvoiceController@exceldownloadprocess');
    Route::any('paymentaddedit', 'InvoiceController@paymentaddedit');
    Route::any('paymentaddeditprocess', 'PaymentController@paymentaddeditprocess');
    Route::any('getaccount', 'PaymentController@getaccount');
    Route::any('ajaxgetbillingdetails', 'InvoiceController@ajaxgetbillingdetails');
    Route::any('empnamepopup', 'InvoiceController@empnamepopup');
    Route::any('invoiceexceldownloadprocess', 'InvoiceController@invoiceexceldownloadprocess');
    Route::any('assignemployee', 'InvoiceController@assignemployee');
    Route::any('editempassignprocess', 'InvoiceController@editempassignprocess');
    Route::any('invoicecopy', 'InvoiceController@invoicecopy');
    Route::any('invoicecopyprocess', 'InvoiceController@invoicecopyprocess');
    Route::any('newexceldownloadprocess', 'InvoiceController@newexceldownloadprocess');
    Route::any('allinvoiceexceldownloadprocess', 'InvoiceController@allinvoiceexceldownloadprocess');
});

// COMPANY EXPENSES - LOAN DETAILS
Route::group(['prefix'=>'Loandetails', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'LoanController@index');
    Route::any('Viewlist', 'LoanController@Viewlist');
    Route::any('Singleview', 'LoanController@Singleview');
    Route::any('Loanconfirm', 'LoanController@Loanconfirm');
    Route::any('addedit', 'LoanController@addedit');
    Route::any('edit', 'LoanController@edit');
    Route::any('addeditprocess', 'LoanController@addeditprocess');
    Route::any('loanaddedit', 'TransferController@loanaddedit');
});

// Employee History
Route::group(['prefix'=>'EmpHistory', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'EmpHistoryController@index');
});

// COMPANY EXPENSES - SALARY
Route::group(['prefix'=>'Salary', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'SalaryController@index');
    Route::any('empselectionpopup', 'SalaryController@empselectionpopup');
    Route::any('empselectionprocess', 'SalaryController@empselectionprocess');
    Route::any('Singleview', 'SalaryController@Singleview');
    Route::any('Viewlist', 'SalaryController@Viewlist');
    Route::any('addedit', 'SalaryController@addedit');
    Route::any('edit', 'SalaryController@edit');
    Route::any('copy', 'SalaryController@copy');
    Route::any('addeditprocess', 'SalaryController@addeditprocess');
    Route::any('multiaddedit', 'SalaryController@multiaddedit');
    Route::any('copycheck', 'SalaryController@copycheck');
    Route::any('multiaddeditprocess', 'SalaryController@multiaddeditprocess');
});

// Staff Contract
Route::group(['prefix'=>'StaffContr', 'middleware' => 'auth'], function() { 
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'StaffContrController@index'); 
    Route::any('contractdetails', 'StaffContrController@contractdetails');
    Route::any('contractview', 'StaffContrController@contractview'); 
    Route::any('staffContaddeditprocess', 'StaffContrController@addeditprocess'); 
    Route::any('contractdownload', 'StaffContrController@contractdownload'); 
    Route::any('cdate_ajax', 'StaffContrController@cdate_ajax');
    
});
    
// SEND MAIL
Route::group(['prefix'=>'Mailstatus', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'MailstatusController@index');
    Route::any('mailstatusview', 'MailstatusController@mailstatusview');
    Route::any('mailhistory', 'MailstatusController@mailhistory');
});

// MAIL Content
Route::group(['prefix'=>'Mailcontent', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'MailcontentController@index');
    Route::any('addedit', 'MailcontentController@addedit');
    Route::any('addeditprocess', 'MailcontentController@addeditprocess');
    Route::any('view', 'MailcontentController@view');
});

// MAIL Signature
Route::group(['prefix'=>'Mailsignature', 'middleware' => 'auth'], function() {
	Route::get('changelanguage', 'AjaxController@index');
	Route::any('index', 'MailsignatureController@index');
	Route::any('addedit', 'MailsignatureController@addedit');
	Route::any('mailsignaturepopup', 'MailsignatureController@mailsignaturepopup');
	Route::any('addeditprocess', 'MailsignatureController@addeditprocess');
	Route::any('view', 'MailsignatureController@view');
    Route::any('getdatexist', 'MailsignatureController@getdatexist');
});

// COMPANY EXPENSES - Bankdetails
Route::group(['prefix'=>'Bankdetails', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'BankdetailController@index');
    Route::any('Viewlist', 'BankdetailController@Viewlist');
    Route::any('add', 'BankdetailController@add');
    Route::any('edit', 'BankdetailController@edit');
    Route::any('checked', 'BankdetailController@checked');
    Route::any('addeditprocess', 'BankdetailController@addeditprocess');
});

// COMPANY EXPENSES - Transfer
Route::group(['prefix'=>'Transfer', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'TransferController@index');
    Route::any('ajaxsubsubject', 'TransferController@ajaxsubsubject');
    Route::any('ajaxloanname', 'TransferController@ajaxloanname');
    Route::any('addedit', 'TransferController@addedit');
    Route::any('edit', 'TransferController@edit');
    Route::any('addeditprocess', 'TransferController@addeditprocess');
    Route::any('loanaddedit', 'TransferController@loanaddedit');
    Route::any('loanedit', 'TransferController@loanedit');
    Route::any('mulreg', 'TransferController@mulreg');
    Route::any('multiregprocess', 'TransferController@multiregprocess');
    Route::any('loanaddeditprocess', 'TransferController@loanaddeditprocess');
    Route::any('download', 'TransferController@download');
    Route::any('transferhistory', 'TransferController@transferhistory');
    Route::get('twoFieldaddedit', 'SettingController@twoFieldaddedit');
    Route::get('threeFieldaddedit', 'SettingController@threeFieldaddedit');
    Route::any('empnamehistory', 'TransferController@empnamehistory');
    Route::any('transfersubhistory', 'TransferController@transfersubhistory');
    Route::any('useNotuse', 'SettingController@useNotuse');
    Route::any('historydownload', 'TransferController@historydownload');
    Route::any('salaryhistorydownload', 'TransferController@salaryhistorydownload');
    Route::any('transfersubhistorydownload', 'TransferController@transfersubhistorydownload');
    Route::any('empnamehistory', 'TransferController@empnamehistory');
    Route::any('copy', 'ExpensesController@copy');
    Route::any('transferexceldownload', 'TransferController@index');
    Route::any('editothersprocess', 'TransferController@editothersprocess');
    Route::any('others', 'TransferController@others');
    Route::any('empnamepopup', 'TransferController@empnamepopup');
});

// MeetingDetails 
Route::group(['prefix'=>'MeetingDetails', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'MeetingdetailsController@index');  
    Route::any('view', 'MeetingdetailsController@view');
    Route::any('meetingaddedit', 'MeetingdetailsController@meetingaddedit'); 
    Route::any('branch_ajax', 'MeetingdetailsController@branch_ajax');
    Route::any('addeditprocess', 'MeetingdetailsController@addeditprocess');
    Route::any('meetinghistory', 'MeetingdetailsController@meetinghistory'); 
    Route::any('newcustomerpopup', 'MeetingdetailsController@newcustomerpopup'); 
    Route::any('newcustomerregpopup', 'MeetingdetailsController@newcustomerregpopup');
    Route::any('cust_name_exist', 'MeetingdetailsController@cust_name_exist');
    Route::any('getmettingtiming', 'MeetingdetailsController@getmettingtiming');
    Route::any('customerregister', 'MeetingdetailsController@newcustomerregpopup');
    
});

// Sales - Payment
Route::group(['prefix'=>'Payment', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'PaymentController@index');
    Route::any('PaymentEdit', 'PaymentController@PaymentEdit');
    Route::any('getaccount', 'PaymentController@getaccount');
    Route::any('paymentaddeditprocess', 'PaymentController@paymentaddeditprocess');
    Route::any('customerview', 'PaymentController@customerview');
    Route::any('customerspecification', 'PaymentController@specificationview');
});

// Home
Route::group(['prefix'=>'Menu', 'middleware' => 'auth'], function() {
    Route::get('index', 'MenuController@index');
    Route::get('changelanguage', 'AjaxController@index');
});

// Expenses Details
Route::group(['prefix'=>'ExpensesDetails', 'middleware' => 'auth'], function() {
    Route::any('index', 'ExpensesDetailsController@index');
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('transferhistory', 'TransferController@transferhistory');
    Route::any('transfersubhistory', 'TransferController@transfersubhistory');
});

// Setting
Route::group(['prefix'=>'Setting', 'middleware' => 'auth'], function() {
Route::any('index', 'SettingController@index');
Route::get('changelanguage', 'AjaxController@index');
Route::get('singletextpopup', 'SettingController@singletextpopup');
Route::any('SingleFieldaddedit', 'SettingController@SingleFieldaddedit');
Route::get('twotextpopup', 'SettingController@twotextpopup');
Route::get('twoFieldaddedit', 'SettingController@twoFieldaddedit');
Route::get('selectthreefieldDatasforbank', 'SettingController@selectthreefieldDatas');
Route::get('selectthreefieldDatas', 'SettingController@selectthreefieldDatas');
Route::get('threeFieldaddeditforbank', 'SettingController@threeFieldaddeditforbank');
Route::get('threeFieldaddedit', 'SettingController@threeFieldaddedit');
Route::any('uploadpopup', 'SettingController@uploadpopup');
Route::any('useNotuse', 'SettingController@useNotuse');
Route::any('settingpopupupload', 'SettingController@settingpopupupload');
});

// Staff -> Salary
Route::group(['prefix'=>'StaffSalary','middleware' => 'auth'], function() {
    Route::any('index', 'StaffSalaryController@index');
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('salaryview', 'StaffSalaryController@salaryview');
    Route::any('viewsalary', 'StaffSalaryController@viewsalary');
    Route::any('salarystaff_ajax', 'StaffSalaryController@salarystaff_ajax');
    Route::any('singleview', 'StaffSalaryController@singleview');
});

// Sales Details
Route::group(['prefix'=>'Salesdetails', 'middleware' => 'auth'], function() {
	Route::any('index', 'SalesdetailsController@index');
	Route::any('salesexceldownloadprocess', 'SalesdetailsController@index');
	Route::get('changelanguage', 'AjaxController@index');
});

Route::group(['prefix'=>'Salesplus', 'middleware' => 'auth'], function() {
	Route::any('index', 'SalesplusController@index');
	Route::any('salesexceldownloadprocess', 'SalesplusController@index');
	Route::get('changelanguage', 'AjaxController@index');
});

// Visa Renew
Route::group(['prefix'=>'Visarenew', 'middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'VisarenewController@index');
    Route::any('visaimportpopup', 'VisarenewController@visaimportpopup');
    Route::any('importprocess', 'VisarenewController@importprocess');
    Route::any('addedit', 'VisarenewController@addedit');
    Route::any('addeditprocess', 'VisarenewController@addeditprocess');
    Route::any('visaview', 'VisarenewController@visaview');
    Route::any('visaExtensionFormDownload', 'VisarenewController@visaExtensionFormDownload');
});

// Tax Details Process
Route::group(['prefix'=>'Tax','middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'TaxController@index');
    Route::any('taximportpopup', 'TaxController@taximportpopup');
    Route::any('importprocess', 'TaxController@importprocess');
    Route::any('taxPersonalDownload', 'TaxController@taxPersonalDownload');
    Route::any('taxview', 'TaxController@taxview');
    Route::any('familyselectionprocess', 'TaxController@familyselectionprocess');
    Route::any('empselectionpopup', 'TaxController@empselectionpopup');
    Route::any('empselectionprocess', 'TaxController@empselectionprocess');
});

// Accounting Process by Rajesh
Route::group(['prefix'=>'Accounting','middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'AccountingController@index');
    Route::any('getcashDetails', 'AccountingController@getcashDetails');
    Route::any('commitProcess', 'AccountingController@commitProcess');
    // Cash // Rajesh
    Route::any('bank_ajax', 'AccountingController@bank_ajax');
    Route::any('addedit', 'AccountingController@addedit');
    Route::any('cashedit', 'AccountingController@cashedit');
    Route::any('addeditprocess', 'AccountingController@addeditprocess');
    Route::any('getInvoicePopup', 'AccountingController@getInvoicePopup');
    // Transfer // Sarath
    Route::any('empnamepopup', 'AccountingController@empnamepopup');
    Route::any('getsalarypopup', 'AccountingController@getsalarypopup');
    Route::any('transferaddedit', 'AccountingController@transferaddedit');
    Route::any('transferedit', 'AccountingController@transferedit');
    Route::any('tranferaddeditprocess', 'AccountingController@tranferaddeditprocess');
    // Auto Debit // Sastha
    Route::any('getloanpopup', 'AccountingController@getloanpopup');
    Route::any('autoDebitReg', 'AccountingController@autoDebitReg');
    Route::any('autoDebitedit', 'AccountingController@autoDebitedit');
    Route::any('AutoDebitRegprocess', 'AccountingController@AutoDebitRegprocess');
     // Invoice // Sastha
    Route::any('invoiceaddeditprocess', 'AccountingController@invoiceaddeditprocess');
});


// AccBankDetail Process by Rajesh
Route::group(['prefix'=>'AccBankDetail','middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'AccBankDetailController@index');
    Route::any('add', 'AccBankDetailController@add');
    Route::any('addeditprocess', 'AccBankDetailController@addeditprocess');
    Route::any('Viewlist', 'AccBankDetailController@Viewlist');
});

// Auditing Process by Rajesh
Route::group(['prefix'=>'Auditing','middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'AuditingController@index');
});

// Salary Calculation Process By Sastha
Route::group(['prefix'=>'salarycalc','middleware' => 'auth'], function() {
    Route::get('changelanguage', 'AjaxController@index');
    Route::any('index', 'SalarycalcController@index');
    Route::any('view', 'SalarycalcController@view');
    Route::any('edit', 'SalarycalcController@edit');
    Route::any('addedit', 'SalarycalcController@addedit');
    Route::any('addeditprocess', 'SalarycalcController@addeditprocess');
    Route::any('multieditprocess', 'SalarycalcController@multieditprocess');
    Route::any('multiregister', 'SalarycalcController@multiregister');
    Route::any('salarypopup', 'SalarycalcController@salarypopup');
    Route::any('empselectprocess', 'SalarycalcController@empselectprocess');
    Route::any('mailsendprocess', 'SalarycalcController@mailsendprocess');
    Route::any('salarydownload', 'SalarycalcController@salarydownload');
    Route::any('history', 'SalarycalcController@history');
    Route::any('getdataExists', 'SalarycalcController@getdataExists');
    Route::any('dataReg', 'SalarycalcController@dataReg');
    Route::get('getlastmonthdet', 'SalarycalcController@getlastmonthdet');
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