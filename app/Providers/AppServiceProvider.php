<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use NinjaMutex\Lock\MySqlLock;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \DB::listen(function($query) {
            \Log::info($query->sql);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            'locker',
            function () {
                $conf = config('database.connections.mysql');

                return new MySqlLock($conf['username'], $conf['password'], $conf['host']);
            }
        );
    }
}
