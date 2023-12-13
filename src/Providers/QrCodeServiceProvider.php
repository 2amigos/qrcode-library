<?php

declare(strict_types=1);

namespace Da\QrCode\Providers;

use Da\QrCode\Component\QrCodeBladeComponent;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class QrCodeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        /** Load package views */
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', '2am-qrcode');
        /** Load package routes */
        $this->loadRoutesFrom(__DIR__ . '/../../routes/laravel.route.php');
        /** publishes package config */
        $this->publishes([
            __DIR__ . '/../../config/2am-qrcode.php' => config_path('2am-qrcode.php'),
        ], '2am-qrcode-config');
        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/2am-qrcode'),
        ], '2am-qrcode-views');
        /** merges config file with user's published version */
        $this->mergeConfigFrom(__DIR__ . '/../../config/2am-qrcode.php', '2am-qrcode');
        /** Declares package's components */
        Blade::component('qrcode', QrCodeBladeComponent::class, config('2am-qrcode.prefix'));
    }
}
