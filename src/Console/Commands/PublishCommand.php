<?php

declare(strict_types=1);

namespace Diviky\Subscriptions\Console\Commands;

use Illuminate\Console\Command;

class PublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diviky:publish:subscriptions {--f|force : Overwrite any existing files.} {--r|resource=* : Specify which resources to publish.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish Diviky Subscriptions Resources.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->alert($this->description);

        collect($this->option('resource') ?: ['config', 'migrations'])->each(function ($resource) {
            $this->call('vendor:publish', ['--tag' => "diviky/subscriptions::{$resource}", '--force' => $this->option('force')]);
        });

        $this->line('');
    }
}
