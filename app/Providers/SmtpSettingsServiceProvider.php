<?php

namespace App\Providers;

use App\Models\SmtpSetting;
use App\Services\MailSettingsService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Throwable;

class SmtpSettingsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (!Schema::hasTable('smtp_settings')) {
            return;
        }

        try {
            $s = SmtpSetting::getActive();
            if (!$s) {
                return;
            }

            MailSettingsService::apply($s->toArray());
        } catch (Throwable) {
            // Silently fallback to .env config — never crash
        }
    }
}
