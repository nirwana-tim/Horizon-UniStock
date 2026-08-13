<?php

use App\Providers\AppServiceProvider;
use App\Providers\SmtpSettingsServiceProvider;

return [
    AppServiceProvider::class,
    SmtpSettingsServiceProvider::class,
    Mews\Captcha\CaptchaServiceProvider::class,
];
