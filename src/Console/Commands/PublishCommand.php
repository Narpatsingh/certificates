<?php

declare(strict_types=1);

namespace Sushil\Makegui\Console\Commands;

use Rinvex\Auth\Console\Commands\PublishCommand as BasePublishCommand;

class PublishCommand extends BasePublishCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sushil:publish:makegui {--force : Overwrite any existing files.} {--R|resource=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish Sushil Makegui Resources.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        parent::handle();

        switch ($this->option('resource')) {
            case 'lang':
                $this->call('vendor:publish', ['--tag' => 'sushil-makegui-lang', '--force' => $this->option('force')]);
                break;
            case 'views':
                $this->call('vendor:publish', ['--tag' => 'sushil-makegui-views', '--force' => $this->option('force')]);
                break;
            case 'config':
                $this->call('vendor:publish', ['--tag' => 'sushil-makegui-config', '--force' => $this->option('force')]);
                break;
            case 'migrations':
                $this->call('vendor:publish', ['--tag' => 'sushil-makegui-migrations', '--force' => $this->option('force')]);
                break;
            default:
                $this->call('vendor:publish', ['--tag' => 'sushil-makegui-lang', '--force' => $this->option('force')]);
                $this->call('vendor:publish', ['--tag' => 'sushil-makegui-views', '--force' => $this->option('force')]);
                $this->call('vendor:publish', ['--tag' => 'sushil-makegui-config', '--force' => $this->option('force')]);
                $this->call('vendor:publish', ['--tag' => 'sushil-makegui-migrations', '--force' => $this->option('force')]);
                break;
        }

        $this->line('');
    }
}
