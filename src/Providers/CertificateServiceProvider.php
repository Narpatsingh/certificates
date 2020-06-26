<?php

declare(strict_types=1);

namespace Sushil\Certificate\Providers;

use Illuminate\Routing\Router;
use Sushil\Certificate\Models\Category;
use Illuminate\Support\ServiceProvider;
use Rinvex\Support\Traits\ConsoleTools;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Relations\Relation;
use Sushil\Certificate\Console\Commands\PublishCommand;


class CertificateServiceProvider extends ServiceProvider
{
    use ConsoleTools;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        PublishCommand::class => 'command.sushil.makegui.publish',
    ];

    /**
     * Register any application services.
     *
     * This service provider is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     *
     * @return void
     */
    public function register(): void
    {
        // Bind eloquent models to IoC container
        $this->app['config']['rinvex.makegui.models.category'] === Category::class
        || $this->app->alias('rinvex.makegui.category', Category::class);

        // Register console commands
        ! $this->app->runningInConsole() || $this->registerCommands();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Router $router, Dispatcher $dispatcher): void
    {
        // Bind route models and constrains
        $router->pattern('category', '[a-zA-Z0-9-]+');
        $router->model('category', config('rinvex.makegui.models.category'));

        // Map relations
        Relation::morphMap([
            'category' => config('rinvex.makegui.models.category'),
        ]);

        // Load resources
        $this->loadRoutesFrom(__DIR__.'/../../routes/web/adminarea.php');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'sushil/makegui');
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'sushil/makegui');

        $this->app->runningInConsole() || $dispatcher->listen('accessarea.ready', function ($accessarea) {
            ! file_exists($menus = __DIR__."/../../routes/menus/{$accessarea}.php") || require $menus;
            ! file_exists($breadcrumbs = __DIR__."/../../routes/breadcrumbs/{$accessarea}.php") || require $breadcrumbs;
        });

        // Publish Resources
        ! $this->app->runningInConsole() || $this->publishesLang('sushil/makegui', true);
        ! $this->app->runningInConsole() || $this->publishesViews('sushil/makegui', true);
        //! $this->app->runningInConsole() || $this->publishesMigrations('sushil/makegui', true);
    }
}
