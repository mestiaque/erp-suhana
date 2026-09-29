<?php

namespace App\Providers;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    // public function boot(): void
    // {
    //     //
    //     \Config::set("services.facebook.client_id", general()->fb_app_id);
    //     \Config::set("services.facebook.client_secret", general()->fb_app_secret);
    //     \Config::set("services.facebook.redirect", general()->fb_app_secret);

    //     \Config::set("services.google.client_id", general()->google_client_id);
    //     \Config::set("services.google.client_secret", general()->google_client_secret);
    //     \Config::set("services.google.redirect", general()->google_client_redirect_url);

    //     \Config::set("mail.mailers.smtp.transport", general()->mail_driver);
    //     \Config::set("mail.mailers.smtp.host", general()->mail_host);
    //     \Config::set("mail.mailers.smtp.port", general()->mail_port);
    //     \Config::set("mail.mailers.smtp.encryption", general()->mail_encryption);
    //     \Config::set("mail.mailers.smtp.username", general()->mail_username);
    //     \Config::set("mail.mailers.smtp.password", general()->mail_password);
    // }

    public function boot(): void
    {
        $this->mergePackageSidebar();
        $this->redirectAllMailInTestMode();

        $general = general(); // helper function

        if ($general) { // Only proceed if general() returns a valid object
            // Facebook
            \Config::set("services.facebook.client_id", $general->fb_app_id);
            \Config::set("services.facebook.client_secret", $general->fb_app_secret);
            \Config::set("services.facebook.redirect", $general->fb_redirect_url ?? null);

            // Google
            \Config::set("services.google.client_id", $general->google_client_id);
            \Config::set("services.google.client_secret", $general->google_client_secret);
            \Config::set("services.google.redirect", $general->google_client_redirect_url ?? null);

            // Mail
            \Config::set("mail.mailers.smtp.transport", $general->mail_driver);
            \Config::set("mail.mailers.smtp.host", $general->mail_host);
            \Config::set("mail.mailers.smtp.port", $general->mail_port);
            \Config::set("mail.mailers.smtp.encryption", $general->mail_encryption);
            \Config::set("mail.mailers.smtp.username", $general->mail_username);
            \Config::set("mail.mailers.smtp.password", $general->mail_password);

            // Without this the .env MAIL_MAILER (e.g. "log") stays the default mailer
            // and the SMTP settings above are never used.
            if ($general->mail_host) {
                \Config::set("mail.default", "smtp");
            }
            if ($general->mail_from_address) {
                \Config::set("mail.from.address", $general->mail_from_address);
                \Config::set("mail.from.name", $general->mail_from_name ?: config('app.name'));
            }

            // observers
        }
    }

    /**
     * Test mode safety net: while APPROVAL_TEST_RECIPIENTS is set, EVERY email
     * the app sends — approval or not, any module, any mailer, queued or not —
     * is re-addressed to those test addresses only (Cc/Bcc dropped) right
     * before it leaves. The real recipients are kept in an X-Original-To
     * header so the test mail shows who it would have gone to.
     */
    private function redirectAllMailInTestMode(): void
    {
        $testRecipients = config('approval.test_recipients', []);

        if (! $testRecipients) {
            return;
        }

        Event::listen(MessageSending::class, function (MessageSending $event) use ($testRecipients) {
            $message = $event->message;
            $headers = $message->getHeaders();

            $original = collect([...$message->getTo(), ...$message->getCc(), ...$message->getBcc()])
                ->map(fn ($address) => $address->getAddress())->unique()->implode(', ');

            $headers->remove('Cc');
            $headers->remove('Bcc');
            $message->to(...$testRecipients);

            if ($original !== '') {
                $headers->remove('X-Original-To');
                $headers->addTextHeader('X-Original-To', $original);
            }
        });
    }

    /**
     * Merge HR package sidebar config into main sidebar config.
     */
    private function mergePackageSidebar(): void
    {
        $mainSidebar = config('sidebar', []);
        $hrSidebar = config('hr-sidebar', []);

        if (!is_array($mainSidebar)) {
            return;
        }

        $merged = $mainSidebar;

        if (is_array($hrSidebar) && !empty($hrSidebar)) {
            foreach ($hrSidebar as $hrGroup) {
                // Prevent exact duplicate groups when both configs already contain the same section.
                if (!in_array($hrGroup, $merged, true)) {
                    $merged[] = $hrGroup;
                }
            }
        }

        config(['sidebar' => $merged]);
    }

}
