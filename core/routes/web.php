<?php

use Illuminate\Support\Facades\Route;

Route::get('/clear', function(){
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::namespace('Gateway')->prefix('ipn')->name('ipn.')->group(function () {
    Route::post('paypal', 'Paypal\ProcessController@ipn')->name('Paypal');
    Route::get('paypal-sdk', 'PaypalSdk\ProcessController@ipn')->name('PaypalSdk');
    Route::post('perfect-money', 'PerfectMoney\ProcessController@ipn')->name('PerfectMoney');
    Route::post('stripe', 'Stripe\ProcessController@ipn')->name('Stripe');
    Route::post('stripe-js', 'StripeJs\ProcessController@ipn')->name('StripeJs');
    Route::post('stripe-v3', 'StripeV3\ProcessController@ipn')->name('StripeV3');
    Route::post('skrill', 'Skrill\ProcessController@ipn')->name('Skrill');
    Route::post('paytm', 'Paytm\ProcessController@ipn')->name('Paytm');
    Route::post('payeer', 'Payeer\ProcessController@ipn')->name('Payeer');
    Route::post('paystack', 'Paystack\ProcessController@ipn')->name('Paystack');
    Route::post('voguepay', 'Voguepay\ProcessController@ipn')->name('Voguepay');
    Route::get('flutterwave/{trx}/{type}', 'Flutterwave\ProcessController@ipn')->name('Flutterwave');
    Route::post('razorpay', 'Razorpay\ProcessController@ipn')->name('Razorpay');
    Route::post('instamojo', 'Instamojo\ProcessController@ipn')->name('Instamojo');
    Route::get('blockchain', 'Blockchain\ProcessController@ipn')->name('Blockchain');
    Route::get('blockio', 'Blockio\ProcessController@ipn')->name('Blockio');
    Route::post('coinpayments', 'Coinpayments\ProcessController@ipn')->name('Coinpayments');
    Route::post('coinpayments-fiat', 'Coinpayments_fiat\ProcessController@ipn')->name('CoinpaymentsFiat');
    Route::post('coingate', 'Coingate\ProcessController@ipn')->name('Coingate');
    Route::post('coinbase-commerce', 'CoinbaseCommerce\ProcessController@ipn')->name('CoinbaseCommerce');
    Route::get('mollie', 'Mollie\ProcessController@ipn')->name('Mollie');
    Route::post('cashmaal', 'Cashmaal\ProcessController@ipn')->name('Cashmaal');
    Route::post('authorize-net', 'AuthorizeNet\ProcessController@ipn')->name('AuthorizeNet');
    Route::post('2check-out', 'TwoCheckOut\ProcessController@ipn')->name('TwoCheckOut');
    Route::post('mercado-pago', 'MercadoPago\ProcessController@ipn')->name('MercadoPago');
});

// User Support Ticket
Route::prefix('ticket')->group(function () {
    Route::get('/', 'TicketController@supportTicket')->name('ticket');
    Route::get('/new', 'TicketController@openSupportTicket')->name('ticket.open');
    Route::post('/create', 'TicketController@storeSupportTicket')->name('ticket.store');
    Route::get('/view/{ticket}', 'TicketController@viewTicket')->name('ticket.view');
    Route::post('/reply/{ticket}', 'TicketController@replyTicket')->name('ticket.reply');
    Route::get('/download/{ticket}', 'TicketController@ticketDownload')->name('ticket.download');
});


/*
|--------------------------------------------------------------------------
| Start Admin Area
|--------------------------------------------------------------------------
*/

Route::namespace('Admin')->prefix('admin')->name('admin.')->group(function () {
    Route::namespace('Auth')->group(function () {
        Route::get('/', 'LoginController@showLoginForm')->name('login');
        Route::post('/', 'LoginController@login')->name('login');
        Route::get('logout', 'LoginController@logout')->name('logout');
        // Admin Password Reset
        Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.reset');
        Route::post('password/reset', 'ForgotPasswordController@sendResetCodeEmail');
        Route::post('password/verify-code', 'ForgotPasswordController@verifyCode')->name('password.verify.code');
        Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset.form');
        Route::post('password/reset/change', 'ResetPasswordController@reset')->name('password.change');

    });

    Route::middleware('admin')->group(function () {
        Route::get('dashboard', 'AdminController@dashboard')->name('dashboard');
        Route::get('profile', 'AdminController@profile')->name('profile');
        Route::post('profile', 'AdminController@profileUpdate')->name('profile.update');
        Route::get('password', 'AdminController@password')->name('password');
        Route::post('password', 'AdminController@passwordUpdate')->name('password.update');

        //Notification
        Route::get('notifications','AdminController@notifications')->name('notifications');
        Route::get('notification/read/{id}','AdminController@notificationRead')->name('notification.read');
        Route::get('notifications/read-all','AdminController@readAll')->name('notifications.readAll');

        //Report Bugs
        Route::get('request-report','AdminController@requestReport')->name('request.report');
        Route::post('request-report','AdminController@reportSubmit');

        Route::get('system-info','AdminController@systemInfo')->name('system.info');

        //Day setting
        Route::get('time-intervals','TimeIntervalController@index')->name('time.intervals');
        Route::post('day-store/{id?}','TimeIntervalController@saveDay')->name('time.intervals.store');
        Route::post('day-delete','TimeIntervalController@delete')->name('time.intervals.delete');

        Route::name('plan.')->group(function(){
            //LoanPlanController
            Route::get('loan-plans', 'LoanPlanController@index')->name('loan.index');
            Route::get('loan-plan/add', 'LoanPlanController@create')->name('loan.create');
            Route::get('loan-plan/edit/{id}', 'LoanPlanController@edit')->name('loan.edit');
            Route::post('loan-plan/save/{id?}', 'LoanPlanController@saveLoanPlan')->name('loan.save');
            Route::post('loan-plan/status', 'LoanPlanController@status')->name('loan.status');
            //SavingsPlanController
            Route::get('savings-plans', 'SavingsPlanController@index')->name('savings.index');
            Route::get('savings-plan/add', 'SavingsPlanController@create')->name('savings.create');
            Route::get('savings-plan/edit/{id}', 'SavingsPlanController@edit')->name('savings.edit');
            Route::post('savings-plan/save/{id?}', 'SavingsPlanController@saveSavingsPlan')->name('savings.save');
            Route::post('savings-plan/status', 'SavingsPlanController@status')->name('savings.status');
        });

        //ManageLoanController
        Route::prefix('loan')->name('loan.')->group(function(){
            Route::get('pending', 'ManageLoanController@pendingLoans')->name('pending');
            Route::get('active', 'ManageLoanController@activeLoans')->name('active');
            Route::get('paid', 'ManageLoanController@paidLoans')->name('paid');
            Route::get('all', 'ManageLoanController@allLoans')->name('all');
            Route::get('register', 'ManageLoanController@showLoanForm')->name('save');
            Route::post('register', 'ManageLoanController@saveLoan')->name('save');
            Route::get('pending/details/{id}', 'ManageLoanController@pendingDetails')->name('pending.details');
            Route::post('approve', 'ManageLoanController@approveLoan')->name('approve');
            Route::post('installment', 'ManageLoanController@installment')->name('installment');
            Route::post('close', 'ManageLoanController@close')->name('close');
            Route::get('user/{id}', 'ManageLoanController@userLoans')->name('user');
        });

        //ManageSavingsController
        Route::prefix('savings')->name('savings.')->group(function(){
            Route::get('pending', 'ManageSavingsController@pendingSavings')->name('pending');
            Route::get('active', 'ManageSavingsController@activeSavings')->name('active');
            Route::get('matured/pending', 'ManageSavingsController@pendingMaturedSavings')->name('matured.pending');
            Route::get('matured/paid', 'ManageSavingsController@paidMaturedSavings')->name('matured.paid');
            Route::post('matured/transfer', 'ManageSavingsController@maturedTransfer')->name('matured.transfer');
            Route::get('all', 'ManageSavingsController@allSavings')->name('all');
            Route::get('register/', 'ManageSavingsController@showSavingsForm')->name('save');
            Route::post('register/', 'ManageSavingsController@saveSavings')->name('save');
            Route::get('pending/details/{id}', 'ManageSavingsController@pendingDetails')->name('pending.details');
            Route::post('approve', 'ManageSavingsController@approveSavings')->name('approve');
            Route::post('installment', 'ManageSavingsController@installment')->name('installment');
            Route::post('close', 'ManageSavingsController@close')->name('close');
            Route::get('user/{id}', 'ManageSavingsController@userSavings')->name('user');
        });

        //ManageCollectionController
        Route::get('collections/all/{filter?}', 'ManageCollectionController@collections')->name('collection.all');
        Route::get('collections/loan/{date?}/{staff_id?}/{admin_receive?}', 'ManageCollectionController@collections')->name('collection.loan');
        Route::get('collections/savings/{date?}/{staff_id?}/{admin_receive?}', 'ManageCollectionController@collections')->name('collection.savings');
        Route::get('staff/loan-collections/pending', 'ManageCollectionController@staffCollection')->name('staff.collection.loan.pending');
        Route::get('staff/savings-collections/pending', 'ManageCollectionController@staffCollection')->name('staff.collection.savings.pending');
        Route::get('staff/loan-collections/paid', 'ManageCollectionController@staffCollection')->name('staff.collection.loan.paid');
        Route::get('staff/savings-collections/paid', 'ManageCollectionController@staffCollection')->name('staff.collection.savings.paid');
        Route::post('staff-collections', 'ManageCollectionController@collectionConfirmation')->name('collection.confirm');

        // //Register Member
        Route::get('users/add', 'ManageUsersController@addUsers')->name('users.add');
        Route::post('users/add', 'RegisterController@register')->name('users.register');

        // Users Manager
        Route::get('users', 'ManageUsersController@allUsers')->name('users.all');
        Route::get('users/active', 'ManageUsersController@activeUsers')->name('users.active');
        Route::get('users/banned', 'ManageUsersController@bannedUsers')->name('users.banned');
        Route::get('users/email-verified', 'ManageUsersController@emailVerifiedUsers')->name('users.email.verified');
        Route::get('users/email-unverified', 'ManageUsersController@emailUnverifiedUsers')->name('users.email.unverified');
        Route::get('users/sms-unverified', 'ManageUsersController@smsUnverifiedUsers')->name('users.sms.unverified');
        Route::get('users/sms-verified', 'ManageUsersController@smsVerifiedUsers')->name('users.sms.verified');

        Route::get('users/{scope}/search', 'ManageUsersController@search')->name('users.search');
        Route::get('user/detail/{id}', 'ManageUsersController@detail')->name('users.detail');
        Route::post('user/update/{id}', 'ManageUsersController@update')->name('users.update');
        Route::get('user/send-email/{id}', 'ManageUsersController@showEmailSingleForm')->name('users.email.single');
        Route::post('user/send-email/{id}', 'ManageUsersController@sendEmailSingle')->name('users.email.single');
        Route::get('user/login/{id}', 'ManageUsersController@login')->name('users.login');
        Route::get('user/deposits/{id}', 'ManageUsersController@deposits')->name('users.deposits');
        Route::get('user/deposits/via/{method}/{type?}/{userId}', 'ManageUsersController@depositViaMethod')->name('users.deposits.method');
        Route::post('user-check', 'ManageUsersController@checkUser')->name('user.check');

        // Staffs Manager
        Route::get('staffs', 'ManageStaffsController@allStaffs')->name('staffs.all');
        Route::get('staffs/active', 'ManageStaffsController@activeStaffs')->name('staffs.active');
        Route::get('staffs/banned', 'ManageStaffsController@bannedStaffs')->name('staffs.banned');
        Route::get('staffs/email-verified', 'ManageStaffsController@emailVerifiedStaffs')->name('staffs.email.verified');
        Route::get('staffs/email-unverified', 'ManageStaffsController@emailUnverifiedStaffs')->name('staffs.email.unverified');
        Route::get('staffs/sms-unverified', 'ManageStaffsController@smsUnverifiedStaffs')->name('staffs.sms.unverified');
        Route::get('staffs/sms-verified', 'ManageStaffsController@smsVerifiedStaffs')->name('staffs.sms.verified');

        Route::get('staff/detail/{id}', 'ManageStaffsController@detail')->name('staffs.detail');
        Route::post('staff/update/{id}', 'ManageStaffsController@update')->name('staffs.update');
        Route::get('staff/send-email/{id}', 'ManageStaffsController@showEmailSingleForm')->name('staffs.email.single');
        Route::post('staff/send-email/{id}', 'ManageStaffsController@sendEmailSingle')->name('staffs.email.single');
        Route::get('staff/login/{id}', 'ManageStaffsController@login')->name('staffs.login');
        Route::get('staff/products/{id}', 'ManageStaffsController@products')->name('staffs.products');
        Route::get('staff/payments/via/{method}/{type?}/{staffId}', 'ManageStaffsController@depositViaMethod')->name('staffs.deposits.method');
        Route::post('staff/register', 'ManageStaffsController@register')->name('staffs.register');
        Route::post('check-mail', 'ManageStaffsController@checkUser')->name('staffs.checkUser');

        // Login History
        Route::get('users/login/history/{id}', 'ManageUsersController@userLoginHistory')->name('users.login.history.single');
        Route::get('users/send-email', 'ManageUsersController@showEmailAllForm')->name('users.email.all');
        Route::post('users/send-email', 'ManageUsersController@sendEmailAll')->name('users.email.send');
        Route::get('users/email-log/{id}', 'ManageUsersController@emailLog')->name('users.email.log');
        Route::get('users/email-details/{id}', 'ManageUsersController@emailDetails')->name('users.email.details');

        // Staff Login History
        Route::get('staffs/login/history/{id}', 'ManageStaffsController@staffLoginHistory')->name('staffs.login.history.single');
        Route::get('staffs/send-email', 'ManageStaffsController@showEmailAllForm')->name('staffs.email.all');
        Route::post('staffs/send-email', 'ManageStaffsController@sendEmailAll')->name('staffs.email.send');
        Route::get('staffs/email-log/{id}', 'ManageStaffsController@emailLog')->name('staffs.email.log');
        Route::get('staffs/email-details/{id}', 'ManageStaffsController@emailDetails')->name('staffs.email.details');



        // Deposit Gateway
        Route::name('gateway.')->prefix('gateway')->group(function(){
            // Automatic Gateway
            Route::get('automatic', 'GatewayController@index')->name('automatic.index');
            Route::get('automatic/edit/{alias}', 'GatewayController@edit')->name('automatic.edit');
            Route::post('automatic/update/{code}', 'GatewayController@update')->name('automatic.update');
            Route::post('automatic/remove/{code}', 'GatewayController@remove')->name('automatic.remove');
            Route::post('automatic/activate', 'GatewayController@activate')->name('automatic.activate');
            Route::post('automatic/deactivate', 'GatewayController@deactivate')->name('automatic.deactivate');

            // Manual Methods
            Route::get('manual', 'ManualGatewayController@index')->name('manual.index');
            Route::get('manual/new', 'ManualGatewayController@create')->name('manual.create');
            Route::post('manual/new', 'ManualGatewayController@store')->name('manual.store');
            Route::get('manual/edit/{alias}', 'ManualGatewayController@edit')->name('manual.edit');
            Route::post('manual/update/{id}', 'ManualGatewayController@update')->name('manual.update');
            Route::post('manual/activate', 'ManualGatewayController@activate')->name('manual.activate');
            Route::post('manual/deactivate', 'ManualGatewayController@deactivate')->name('manual.deactivate');
        });

        // DEPOSIT SYSTEM
        Route::name('deposit.')->prefix('deposit')->group(function(){
            Route::get('/', 'DepositController@deposit')->name('list');
            Route::get('pending', 'DepositController@pending')->name('pending');
            Route::get('rejected', 'DepositController@rejected')->name('rejected');
            Route::get('approved', 'DepositController@approved')->name('approved');
            Route::get('successful', 'DepositController@successful')->name('successful');
            Route::get('details/{id}', 'DepositController@details')->name('details');
            Route::post('reject', 'DepositController@reject')->name('reject');
            Route::post('approve', 'DepositController@approve')->name('approve');
            Route::get('via/{method}/{type?}', 'DepositController@depositViaMethod')->name('method');
            Route::get('/{scope}/search', 'DepositController@search')->name('search');
            Route::get('date-search/{scope}', 'DepositController@dateSearch')->name('dateSearch');
        });

        // Report
        Route::get('report/user/login/history', 'ReportController@userLoginHistory')->name('report.user.login.history');
        Route::get('report/user/login/ipHistory/{ip}', 'ReportController@userLoginIpHistory')->name('report.user.login.ipHistory');
        Route::get('report/staff/login/history', 'ReportController@staffLoginHistory')->name('report.staff.login.history');
        Route::get('report/staff/login/ipHistory/{ip}', 'ReportController@staffLoginIpHistory')->name('report.staff.login.ipHistory');
        Route::get('report/user/email/history', 'ReportController@userEmailHistory')->name('report.user.email.history');
        Route::get('report/staff/email/history', 'ReportController@staffEmailHistory')->name('report.staff.email.history');


        // Admin User Support
        Route::get('tickets/users', 'SupportTicketController@userTickets')->name('user.ticket');
        Route::get('tickets/users/pending', 'SupportTicketController@userTendingTicket')->name('user.ticket.pending');
        Route::get('tickets/users/closed', 'SupportTicketController@userClosedTicket')->name('user.ticket.closed');
        Route::get('tickets/users/answered', 'SupportTicketController@userAnsweredTicket')->name('user.ticket.answered');
        Route::get('tickets/users/view/{id}', 'SupportTicketController@userTicketReply')->name('user.ticket.view');
        Route::post('ticket/users/reply/{id}', 'SupportTicketController@userTicketReplySend')->name('user.ticket.reply');
        Route::get('ticket/users/download/{ticket}', 'SupportTicketController@userTicketDownload')->name('user.ticket.download');
        Route::post('ticket/users/delete', 'SupportTicketController@userTicketDelete')->name('user.ticket.delete');

        // Admin Staff Support
        Route::get('tickets/staffs', 'SupportTicketController@staffTickets')->name('staff.ticket');
        Route::get('tickets/staffs/pending', 'SupportTicketController@staffTendingTicket')->name('staff.ticket.pending');
        Route::get('tickets/staffs/closed', 'SupportTicketController@staffClosedTicket')->name('staff.ticket.closed');
        Route::get('tickets/staffs/answered', 'SupportTicketController@staffAnsweredTicket')->name('staff.ticket.answered');
        Route::get('tickets/staffs/view/{id}', 'SupportTicketController@staffTicketReply')->name('staff.ticket.view');
        Route::post('ticket/staffs/reply/{id}', 'SupportTicketController@staffTicketReplySend')->name('staff.ticket.reply');
        Route::get('ticket/staffs/download/{ticket}', 'SupportTicketController@staffTicketDownload')->name('staff.ticket.download');
        Route::post('ticket/staffs/delete', 'SupportTicketController@staffTicketDelete')->name('staff.ticket.delete');


        // General Setting
        Route::get('general-setting', 'GeneralSettingController@index')->name('setting.index');
        Route::post('general-setting', 'GeneralSettingController@update')->name('setting.update');
        Route::get('optimize', 'GeneralSettingController@optimize')->name('setting.optimize');

        // Logo-Icon
        Route::get('setting/logo-icon', 'GeneralSettingController@logoIcon')->name('setting.logo.icon');
        Route::post('setting/logo-icon', 'GeneralSettingController@logoIconUpdate')->name('setting.logo.icon');

        //Custom CSS
        Route::get('custom-css','GeneralSettingController@customCss')->name('setting.custom.css');
        Route::post('custom-css','GeneralSettingController@customCssSubmit');


        // Plugin
        Route::get('extensions', 'ExtensionController@index')->name('extensions.index');
        Route::post('extensions/update/{id}', 'ExtensionController@update')->name('extensions.update');
        Route::post('extensions/activate', 'ExtensionController@activate')->name('extensions.activate');
        Route::post('extensions/deactivate', 'ExtensionController@deactivate')->name('extensions.deactivate');


        // Email Setting
        Route::get('email-template/global', 'EmailTemplateController@emailTemplate')->name('email.template.global');
        Route::post('email-template/global', 'EmailTemplateController@emailTemplateUpdate')->name('email.template.global');
        Route::get('email-template/setting', 'EmailTemplateController@emailSetting')->name('email.template.setting');
        Route::post('email-template/setting', 'EmailTemplateController@emailSettingUpdate')->name('email.template.setting');
        Route::get('email-template/index', 'EmailTemplateController@index')->name('email.template.index');
        Route::get('email-template/{id}/edit', 'EmailTemplateController@edit')->name('email.template.edit');
        Route::post('email-template/{id}/update', 'EmailTemplateController@update')->name('email.template.update');
        Route::post('email-template/send-test-mail', 'EmailTemplateController@sendTestMail')->name('email.template.test.mail');


        // SMS Setting
        Route::get('sms-template/global', 'SmsTemplateController@smsTemplate')->name('sms.template.global');
        Route::post('sms-template/global', 'SmsTemplateController@smsTemplateUpdate')->name('sms.template.global');
        Route::get('sms-template/setting','SmsTemplateController@smsSetting')->name('sms.templates.setting');
        Route::post('sms-template/setting', 'SmsTemplateController@smsSettingUpdate')->name('sms.template.setting');
        Route::get('sms-template/index', 'SmsTemplateController@index')->name('sms.template.index');
        Route::get('sms-template/edit/{id}', 'SmsTemplateController@edit')->name('sms.template.edit');
        Route::post('sms-template/update/{id}', 'SmsTemplateController@update')->name('sms.template.update');
        Route::post('email-template/send-test-sms', 'SmsTemplateController@sendTestSMS')->name('sms.template.test.sms');

        // Frontend
        Route::name('frontend.')->prefix('frontend')->group(function () {
            Route::get('frontend-sections/{key}', 'FrontendController@frontendSections')->name('sections');
            Route::post('frontend-content/{key}', 'FrontendController@frontendContent')->name('sections.content');
            Route::get('frontend-element/{key}/{id?}', 'FrontendController@frontendElement')->name('sections.element');
            Route::post('remove', 'FrontendController@remove')->name('remove');
        });
    });
});


/*
|--------------------------------------------------------------------------
| Start Staff Area
|--------------------------------------------------------------------------
*/

Route::namespace('Staff')->prefix('staff')->name('staff.')->group(function(){
    Route::namespace('Auth')->group(function(){
        Route::get('/', 'LoginController@showLoginForm')->name('login');
        Route::post('/', 'LoginController@login')->name('login');
        Route::get('logout', 'LoginController@logout')->name('logout');

        Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
        Route::post('password/email', 'ForgotPasswordController@sendResetCodeEmail')->name('password.email');
        Route::get('password/code-verify', 'ForgotPasswordController@codeVerify')->name('password.code.verify');
        Route::post('password/reset', 'ResetPasswordController@reset')->name('password.update');
        Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
        Route::post('password/verify-code', 'ForgotPasswordController@verifyCode')->name('password.verify.code');
    });

    Route::middleware('staff')->group(function(){

        Route::get('authorization', 'AuthorizationController@authorizeForm')->name('authorization');
        Route::get('resend-verify', 'AuthorizationController@sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'AuthorizationController@emailVerification')->name('verify.email');
        Route::post('verify-sms', 'AuthorizationController@smsVerification')->name('verify.sms');
        Route::post('verify-g2fa', 'AuthorizationController@g2faVerification')->name('go2fa.verify');

        Route::middleware('staff.checkStatus')->group(function(){

            Route::get('dashboard', 'StaffController@dashboard')->name('dashboard');

            //Collection Controller
            Route::prefix('loan')->name('loan.')->group(function(){
                Route::get('plans', 'CollectionController@loanPlan')->name('plan');
                Route::get('apply/{plan}', 'CollectionController@loanApplyForm')->name('apply');
                Route::post('apply/{plan}', 'CollectionController@loanApply');
                Route::get('pending', 'CollectionController@loans')->name('pending');
                Route::get('active', 'CollectionController@loans')->name('active');
                Route::get('paid', 'CollectionController@loans')->name('paid');
                Route::get('closed', 'CollectionController@loans')->name('closed');
                Route::get('all', 'CollectionController@loans')->name('all');
                Route::post('installment', 'CollectionController@loanInstallment')->name('installment');
            });

            Route::prefix('savings')->name('savings.')->group(function(){
                Route::get('plans', 'CollectionController@savingsPlan')->name('plan');
                Route::get('apply/{plan}', 'CollectionController@savingsApplyForm')->name('apply');
                Route::post('apply/{plan}', 'CollectionController@savingsApply');
                Route::get('pending', 'CollectionController@savings')->name('pending');
                Route::get('active', 'CollectionController@savings')->name('active');
                Route::get('paid', 'CollectionController@savings')->name('paid');
                Route::get('closed', 'CollectionController@savings')->name('closed');
                Route::get('all', 'CollectionController@savings')->name('all');
                Route::post('installment', 'CollectionController@savingsInstallment')->name('installment');
            });

            Route::post('user-check', 'CollectionController@checkUser')->name('user.check');

            //Payment History
            Route::get('payment-history/all', 'StaffController@paymentHistory')->name('payment.history');
            Route::get('payment-history/loan/{filter?}/{adminReceive?}', 'StaffController@paymentHistory')->name('payment.loan.history');
            Route::get('payment-history/savings/{filter?}/{adminReceive?}', 'StaffController@paymentHistory')->name('payment.savings.history');

            //Collection
            Route::get('loan-collections/pending', 'CollectionController@dailyCollection')->name('collection.loan.pending');
            Route::get('savings-collections/pending', 'CollectionController@dailyCollection')->name('collection.savings.pending');
            Route::get('loan-collections/paid', 'CollectionController@dailyCollection')->name('collection.loan.paid');
            Route::get('savings-collections/paid', 'CollectionController@dailyCollection')->name('collection.savings.paid');

            Route::get('profile', 'StaffController@profile')->name('profile');
            Route::post('profile', 'StaffController@profileUpdate')->name('profile.update');
            Route::get('change-password', 'StaffController@changePassword')->name('change.password');
            Route::post('change-password', 'StaffController@submitPassword');

            //2FA
            Route::get('twofactor', 'StaffController@show2faForm')->name('twofactor');
            Route::post('twofactor/enable', 'StaffController@create2fa')->name('twofactor.enable');
            Route::post('twofactor/disable', 'StaffController@disable2fa')->name('twofactor.disable');


            // Staff Support Ticket
            Route::prefix('ticket')->group(function () {
                Route::get('/', 'TicketController@supportTicket')->name('ticket');
                Route::get('/new', 'TicketController@openSupportTicket')->name('ticket.open');
                Route::post('/create', 'TicketController@storeSupportTicket')->name('ticket.store');
                Route::get('/view/{ticket}', 'TicketController@viewTicket')->name('ticket.view');
                Route::post('/reply/{ticket}', 'TicketController@replyTicket')->name('ticket.reply');
                Route::get('/download/{ticket}', 'TicketController@ticketDownload')->name('ticket.download');
            });
        });

    });

});




/*
|--------------------------------------------------------------------------
| Start User Area
|--------------------------------------------------------------------------
*/


Route::name('user.')->group(function () {
    Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('/login', 'Auth\LoginController@login');
    Route::get('logout', 'Auth\LoginController@logout')->name('logout');

    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@register')->middleware('regStatus');
    Route::post('check-mail', 'Auth\RegisterController@checkUser')->name('checkUser');

    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetCodeEmail')->name('password.email');
    Route::get('password/code-verify', 'Auth\ForgotPasswordController@codeVerify')->name('password.code.verify');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/verify-code', 'Auth\ForgotPasswordController@verifyCode')->name('password.verify.code');
});

Route::name('user.')->prefix('user')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('authorization', 'AuthorizationController@authorizeForm')->name('authorization');
        Route::get('resend-verify', 'AuthorizationController@sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'AuthorizationController@emailVerification')->name('verify.email');
        Route::post('verify-sms', 'AuthorizationController@smsVerification')->name('verify.sms');
        Route::post('verify-g2fa', 'AuthorizationController@g2faVerification')->name('go2fa.verify');

        Route::middleware(['checkStatus'])->group(function () {
            Route::get('/dashboard', 'UserController@home')->name('dashboard');

            //LoanController
            Route::prefix('loan')->name('loan.')->group(function(){
                Route::get('plans', 'LoanController@plan')->name('plan');
                Route::get('apply/{plan}', 'LoanController@applyForm')->name('apply');
                Route::post('apply/{plan}', 'LoanController@apply');
                Route::get('/pending', 'LoanController@loans')->name('pending');
                Route::get('active', 'LoanController@loans')->name('active');
                Route::get('paid', 'LoanController@loans')->name('paid');
                Route::get('all', 'LoanController@loans')->name('all');
                Route::get('closed', 'LoanController@loans')->name('closed');
                Route::post('payment', 'LoanController@payment')->name('payment');
            });

            //SavingsController
            Route::prefix('savings')->name('savings.')->group(function(){
                Route::get('plans', 'SavingsController@plan')->name('plan');
                Route::get('apply/{plan}', 'SavingsController@applyForm')->name('apply');
                Route::post('apply/{plan}', 'SavingsController@apply');
                Route::get('pending', 'SavingsController@savings')->name('pending');
                Route::get('active', 'SavingsController@savings')->name('active');
                Route::get('paid', 'SavingsController@savings')->name('paid');
                Route::get('closed', 'SavingsController@savings')->name('closed');
                Route::get('all', 'SavingsController@savings')->name('all');
                Route::post('payment', 'SavingsController@payment')->name('payment');
            });



            //Payment History
            Route::get('payment-history/all', 'UserController@paymentHistory')->name('payment.history');
            Route::get('payment-history/loan', 'UserController@paymentHistory')->name('payment.loan.history');
            Route::get('payment-history/savings', 'UserController@paymentHistory')->name('payment.savings.history');

            Route::get('profile-setting', 'UserController@profile')->name('profile.setting');
            Route::post('profile-setting', 'UserController@submitProfile');
            Route::get('change-password', 'UserController@changePassword')->name('change.password');
            Route::post('change-password', 'UserController@submitPassword');

            //2FA
            Route::get('twofactor', 'UserController@show2faForm')->name('twofactor');
            Route::post('twofactor/enable', 'UserController@create2fa')->name('twofactor.enable');
            Route::post('twofactor/disable', 'UserController@disable2fa')->name('twofactor.disable');

            // Payment
            Route::any('/payment', 'Gateway\PaymentController@deposit')->name('deposit');
            Route::post('payment/insert', 'Gateway\PaymentController@depositInsert')->name('deposit.insert');
            Route::get('payment/preview', 'Gateway\PaymentController@depositPreview')->name('deposit.preview');
            Route::get('payment/confirm', 'Gateway\PaymentController@depositConfirm')->name('deposit.confirm');
            Route::get('payment/manual', 'Gateway\PaymentController@manualDepositConfirm')->name('deposit.manual.confirm');
            Route::post('payment/manual', 'Gateway\PaymentController@manualDepositUpdate')->name('deposit.manual.update');

        });
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/', 'UserController@home')->name('home');
});

Route::get('placeholder-image/{size}', 'SiteController@placeholderImage')->name('placeholder.image');
Route::get('page/{id}/{slug}', 'SiteController@policy')->name('policy');


