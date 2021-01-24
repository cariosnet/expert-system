<?php


namespace ExpertSystem\Providers;


use Illuminate\Support\ServiceProvider;
use ExpertSystem\Decision;
use ExpertSystem\ExpertSystem;
use ExpertSystem\Question;
use ExpertSystem\Topic;

class ExpertSystemServiceProvider extends ServiceProvider
{
    public function boot(){
        $path = realpath(__DIR__.'/../config/expert-system.php');

        $this->publishes([$path => config_path('expert-system.php')], 'config');

        $this->mergeConfigFrom($path,'expert-system');
        //$this->loadMigrationsFrom(__DIR__.'/../migrations');
        $this->publishes([
            __DIR__ . '/../migrations/' => database_path('migrations'),
        ], 'migrations');

    }

    public function register(){
        $this->registerAliases();
        $this->registerEs();
//        $this->registerTopic();
//        $this->registerQuestion();
//        $this->registerDecision();
    }

    public function registerAliases(){
        $this->app->alias('zlab.expert-system', ExpertSystem::class);
//        $this->app->alias('zlab.expert-system.question', Question::class);
//        $this->app->alias('zlab.expert-system.decision', Decision::class);
//        $this->app->alias('zlab.expert-system.topic', Topic::class);
    }

    public function registerEs(){
        $this->app->singleton('zlab.expert-system',function ($app){
            return new ExpertSystem(
                $this->config('MODEL_PATH')
            );
        });
    }

//    public function registerTopic(){
//        $this->app->singleton('mzf.danalib',function ($app){
//            return new Dana(
//                $this->config('CLIENT_ID'),
//                $this->config('CLIENT_SECRET'),
//                $this->config('MERCHANT_ID'),
//                $this->config('PRIVATE_KEY'),
//                $this->config('DANA_PUBLIC_KEY'),
//                $this->config('MOCK_API'),
//                $this->config('MOCK_SCENE'),
//                $this->config('DANA_REQUEST_AUDIT'),
//                $this->config('OAUTH_TERMINAL_TYPE')
//            );
//        });
//    }
//
//    public function registerQuestion(){
//        $this->app->singleton('mzf.danalib.util',function ($app){
//            return new Util(
//                $this->config('API_URL'),
//                $this->config('WEB_URL'),
//                $this->config('OAUTH_REDIRECT_URL'),
//                $this->app->get('mzf.danalib')
//            );
//        });
//    }

    protected function config($key, $default = null)
    {
        return config("expert-system.$key", $default);
    }

//    private function registerDecision()
//    {
//        $this->app->singleton('mzf.danalib.transaction',function ($app){
//            return new Transaction(
//                $this->config('API_URL'),
//                $this->config('WEB_URL'),
//                $this->config('OAUTH_REDIRECT_URL'),
//                $this->app->get('mzf.danalib')
//            );
//        });
//    }
}
