<?php namespace Tricks\Providers;

use Illuminate\Support\ServiceProvider;
use Tricks\Services\Markdown\HtmlMarkdownConvertor;

class MarkdownServiceProvider extends ServiceProvider {

    //protected $defer = true;

    public function register() {
        $this->app->singleton('markdown', function($app) {
            return new HtmlMarkdownConvertor;
        });
    }

}