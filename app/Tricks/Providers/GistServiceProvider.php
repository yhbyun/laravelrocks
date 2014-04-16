<?php namespace Tricks\Providers;

use Illuminate\Support\ServiceProvider;
use Tricks\Services\Markdown\GistEmbedFormatter;

class GistServiceProvider extends ServiceProvider {

    //protected $defer = true;

    public function register() {
        $this->app->singleton('gist', function($app) {
            return new GistEmbedFormatter;
        });
    }

}