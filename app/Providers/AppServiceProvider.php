<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Pusher\Pusher;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Configurar Pusher con SSL desactivado para desarrollo local en Windows
        $this->app->singleton('pusher', function ($app) {
            $config = config('broadcasting.connections.pusher');

            $guzzleClient = new \GuzzleHttp\Client([
                'verify' => false,  // ← desactiva verificación SSL
            ]);

            return new Pusher(
                $config['key'],
                $config['secret'],
                $config['app_id'],
                array_merge($config['options'], [
                    'http_client' => $guzzleClient,
                ])
            );
        });
    }
}