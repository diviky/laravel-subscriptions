<?php

declare(strict_types=1);

namespace Diviky\Subscriptions\Console\Commands;

use Illuminate\Console\Command;

class MigrateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diviky:migrate:subscriptions {--f|force : Force the operation to run when in production.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate Diviky Subscriptions Tables.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->alert($this->description);

        $path = config('diviky.subscriptions.autoload_migrations') ?
            'vendor/diviky/laravel-subscriptions/database/migrations' :
            'database/migrations/diviky/laravel-subscriptions';

        if (file_exists($path)) {
            $this->call('migrate', [
                '--step' => true,
                '--path' => $path,
                '--force' => $this->option('force'),
            ]);
        } else {
            $this->warn('No migrations found! Consider publish them first: <fg=green>php artisan diviky:publish:subscriptions</>');
        }

        $this->line('');
    }
}
