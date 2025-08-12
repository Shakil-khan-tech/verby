<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Auth;
use \Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen('routes.translation', function($locale, $attributes)
        {
            if (auth()->check()) {
                $user = Auth::user();
                $currentLocale = LaravelLocalization::getCurrentLocale();
    
                if ($currentLocale !== $user->language) {
                    $user->language = $currentLocale;
                    $user->save();
                }
            }
            
            return $attributes;
        });
    }
}
