<?php

Namespace Asantos88\TeamUpLaravel;

use Illuminate\Support\ServiceProvider;

class TeamUpServiceProvider extends ServiceProvider
{
    public function boot()
    {
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/team-up.php', 'team-up'
        );

        $this->publishes([
            __DIR__.'/config/team-up.php' => config_path('team-up.php')
        ], 'team-up-config');
    }
}
