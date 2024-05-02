<?php

declare(strict_types=1);

namespace Diviky\Subscriptions\Providers;

use Diviky\Subscriptions\Console\Commands\MigrateCommand;
use Diviky\Subscriptions\Console\Commands\PublishCommand;
use Diviky\Subscriptions\Console\Commands\RollbackCommand;
use Diviky\Subscriptions\Models\Plan;
use Diviky\Subscriptions\Models\PlanFeature;
use Diviky\Subscriptions\Models\PlanSubscription;
use Diviky\Subscriptions\Models\PlanSubscriptionUsage;
use Diviky\Bright\Support\ServiceProvider;
use Rinvex\Support\Traits\ConsoleTools;

class SubscriptionsServiceProvider extends ServiceProvider
{
    use ConsoleTools;

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/config.php'), 'diviky.subscriptions');

        // Bind eloquent models to IoC container
        $this->registerModels([
            'diviky.subscriptions.plan' => Plan::class,
            'diviky.subscriptions.plan_feature' => PlanFeature::class,
            'diviky.subscriptions.plan_subscription' => PlanSubscription::class,
            'diviky.subscriptions.plan_subscription_usage' => PlanSubscriptionUsage::class,
        ]);
    }

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->console();
        }

        // Publish Resources
    }

    protected function path(): string
    {
        return __DIR__ . '/../..';
    }

    protected function console(): void
    {
        $this->publishes([
            $this->path() . '/config/config.php' => config_path('diviky.subscriptions.php'),
        ], 'subscriptions-config');

        $this->loadMigrationsFrom($this->path().'/database/migrations');

        $this->commands([
            MigrateCommand::class,
            PublishCommand::class,
            RollbackCommand::class
        ]);
    }
}
