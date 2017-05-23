<?php

namespace spimpolari\LaravelORMBuilder;

use Illuminate\Support\Facades\DB;
use spimpolari\LaravelORMBuilder\ORMBuilderHelper;
use Illuminate\Console\Command;
 
/**
 * Get an existing package from a remote Github repository.
 *
 * @package Packager
 * @author JeroenG
 * 
 **/
class ORMBuilderTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orm:table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Model From Specific Table.';

    /**
     * Packager helper class.
     * @var object
     */
    protected $helper;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ORMBuilderHelper $helper)
    {
        parent::__construct();
        $this->helper = $helper;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $headers = ['Name'];

        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        
        foreach($tables as $table) {
            $row[]['Name'] = $table;
        }
        
        
        
        
        
        
        
        
        $this->table($headers, $row);

    }
    
}