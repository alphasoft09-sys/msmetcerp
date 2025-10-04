<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Session;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Add notification methods to the response factory
        $this->app->afterResolving('Illuminate\Contracts\Routing\ResponseFactory', function ($factory) {
            $factory->macro('withSuccess', function ($message) use ($factory) {
                Session::flash('notification', [
                    'type' => 'success',
                    'message' => $message
                ]);
                return $factory;
            });

            $factory->macro('withError', function ($message) use ($factory) {
                Session::flash('notification', [
                    'type' => 'error',
                    'message' => $message
                ]);
                return $factory;
            });

            $factory->macro('withInfo', function ($message) use ($factory) {
                Session::flash('notification', [
                    'type' => 'info',
                    'message' => $message
                ]);
                return $factory;
            });

            $factory->macro('withWarning', function ($message) use ($factory) {
                Session::flash('notification', [
                    'type' => 'warning',
                    'message' => $message
                ]);
                return $factory;
            });
        });

        // Add notification methods to the redirector
        $this->app->afterResolving('Illuminate\Routing\Redirector', function ($redirector) {
            $redirector->macro('withSuccess', function ($message) use ($redirector) {
                Session::flash('notification', [
                    'type' => 'success',
                    'message' => $message
                ]);
                return $redirector;
            });

            $redirector->macro('withError', function ($message) use ($redirector) {
                Session::flash('notification', [
                    'type' => 'error',
                    'message' => $message
                ]);
                return $redirector;
            });

            $redirector->macro('withInfo', function ($message) use ($redirector) {
                Session::flash('notification', [
                    'type' => 'info',
                    'message' => $message
                ]);
                return $redirector;
            });

            $redirector->macro('withWarning', function ($message) use ($redirector) {
                Session::flash('notification', [
                    'type' => 'warning',
                    'message' => $message
                ]);
                return $redirector;
            });
        });
    }
}
