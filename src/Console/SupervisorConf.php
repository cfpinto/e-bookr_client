<?php


namespace Ebookr\Client\Console;

use Illuminate\Console\Application;
use Illuminate\Console\Command;

class SupervisorConf extends Command
{
    protected $name = 'make:supervisor';

    protected $signature = 'make:supervisor 
        {name : The name of the queue supervisor is monitoring} 
        {--num_proc= : How many instances of the queue worker. Default 2}
        {--user= : User running the command. Default ubuntu}
        {--write : ask to write to destination. needs sudo}';

    protected $description = 'Create a supervisor conf file for the necessary workers';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle():void
    {
        $name = $this->argument('name');
        $numProcesses = $this->option('num_proc') ?? 2;
        $user = $this->option('user') ?? 'ubuntu';
        $write = $this->option('write');
        $artisan = $this->getLaravel()->basePath() . '/artisan';
        $path = realpath(__DIR__ . '/../../assets/supervisor/conf.d/conf.stub');
        $stub = file_get_contents($path);
        $logPath = storage_path('logs/' . $name . '.worker.log');
        $content = str_replace(
            ['%(queue_name)', '%(artisan_path)', '%(log_file_path)', '%(number_processes)', '%(user)'],
            [$name, $artisan, $logPath, $numProcesses, $user],
            $stub
        );

        if ($write && posix_getuid() === 0) {
            $target = '/etc/supervisor/conf.d/' . $name . '.conf';
            $this->info(sprintf("Writing to %s", $target));
            file_put_contents($target, $content);
            $this->info("done");
            $this->info("Please manually activate the new configurations");
        } elseif ($write && posix_getuid() !== 0) {
            $this->error('You need to be sudo to write file to target');
        } else {
            $this->line($content);
        }
    }
}
