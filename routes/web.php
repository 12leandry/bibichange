<?php

use App\Http\Controllers\User\MailController;
use App\Lib\Router;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\EscrowController;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;


Route::get('/test-smtp', function () {
    // Create a Mailer instance
    $transport = Transport::fromDsn('smtp://b32376b5cfd976:69b170dbe12ae6@sandbox.smtp.mailtrap.io:2525?encryption=tls&auth_mode=login');
    $mailer = new Mailer($transport);

// Create a new Email instance
    $email = (new Email())
    ->from('nikeleandry@gmail.com') // Replace with your sender email
    ->to('recipient@example.com') // Replace with your recipient email
    ->subject('Your email subject')
    ->text('Plain text message') // Replace with your plain text message
    ->html('<p>HTML message</p>');

    // Send the test email
    $result = $mailer->send($email);

    if ($result) {
    return 'SMTP connection successful. Test email sent.';
    } else {
    return 'SMTP connection failed. Check your configuration.';
    }
});







Route::get('/clear', function(){
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

Route::get('/mail/send-test-email', [MailController::class, 'sendTestEmail']);

// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->group(function () {
    Route::get('/', 'supportTicket')->name('ticket');
    Route::get('/new', 'openSupportTicket')->name('ticket.open');
    Route::post('/create', 'storeSupportTicket')->name('ticket.store');
    Route::get('/view/{ticket}', 'viewTicket')->name('ticket.view');
    Route::post('/reply/{ticket}', 'replyTicket')->name('ticket.reply');
    Route::post('/close/{ticket}', 'closeTicket')->name('ticket.close');
    Route::get('/download/{ticket}', 'ticketDownload')->name('ticket.download');
});


Route::get('app/deposit/confirm/{hash}', 'Gateway\PaymentController@appDepositConfirm')->name('deposit.app.confirm');
Route::get('mail',[EscrowController::class, 'mail']);
Route::controller('SiteController')->group(function () {
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');

    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');

    Route::get('blog', 'blogs')->name('blogs');
    Route::get('blog/{slug}/{id}', 'blogDetails')->name('blog.details');

    Route::get('policy/{slug}/{id}', 'policyPages')->name('policy.pages');

    Route::get('placeholder-image/{size}', 'placeholderImage')->name('placeholder.image');
    // subscriber
    Route::post('/subscribe', 'subscribe')->name('subscribe');


    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');
});