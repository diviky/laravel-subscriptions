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
use Illuminate\Support\ServiceProvider;
use Rinvex\Support\Traits\ConsoleTools;

class SubscriptionsServiceProvider extends ServiceProvider
{
    use ConsoleTools;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        MigrateCommand::class => 'command.diviky.subscriptions.migrate',
        PublishCommand::class => 'command.diviky.subscriptions.publish',
        RollbackCommand::class => 'command.diviky.subscriptions.rollback',
    ];

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

        // Register console commands
        $this->registerCommands($this->commands);
    }

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        // Publish Resources
        $this->publishesConfig('diviky/laravel-subscriptions');
        $this->publishesMigrations('diviky/laravel-subscriptions');
        ! $this->autoloadMigrations('diviky/laravel-subscriptions') || $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }
}
