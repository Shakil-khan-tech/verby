<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\Traits\EmailTrait;
use Illuminate\Database\Eloquent\Builder;

class AppServiceProvider extends ServiceProvider
{
    use EmailTrait;
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        date_default_timezone_set('Europe/Zurich');

        //email notifications
        $notifications = collect();
        $user = '';
        $uapi = $this->list_emails();
        if ( $uapi->status == "failed" ) {
            // return response()->json(['message' => __('Error occured while getting emails'), 'errors' => [$uapi->errors] ], 500);
            View::share('emp', 'Error occured while getting emails');

        }
        try {
            $data = collect($uapi->data)->sortBy('email')->values()->all();
            foreach ($data as $key => $user) {
                if ( $user->diskusedpercent > 80 ) {
                    $notifications->push([
                        'email' => $user->email,
                        'used' => $user->diskusedpercent
                    ]);
                }
            }
        } catch (\Throwable $th) {
            //
        }
        

        // Sharing is caring
        // View::share('emp', $data);
        View::share('email_notifications', $notifications);
        // View::share('emp', 'value');


        // WhereHas() and With()
        // https://dev.to/othmane_nemli/laravel-wherehas-and-with-550o
        Builder::macro( 'withWhereHas', function ($relation, $constraint) {
            return $this
                ->whereHas($relation, $constraint)
                ->with($relation, $constraint);
        });
    }
}
