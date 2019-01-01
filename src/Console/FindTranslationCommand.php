<?php
/**
 * Created by PhpStorm.
 * User: claudiopinto
 * Date: 2019-01-01
 * Time: 21:42
 */

namespace Ebookr\Client\Console;

use Barryvdh\TranslationManager\Manager;
use Illuminate\Console\Command;

class FindTranslationCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'e-bookr:find-translation';

    /**
     * @var string
     */
    protected $signature = 'e-bookr:find-translation {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'find translations in path';

    protected $manager = null;

    public function __construct(Manager $manager)
    {
        parent::__construct();
        $this->manager = $manager;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $counter = $this->manager->findTranslations(app_path() . '/../' . $this->argument('path'));
        $this->info('Done importing, processed ' . $counter . ' items!');
    }
}