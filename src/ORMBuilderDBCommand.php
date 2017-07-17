<?php

namespace spimpolari\LaravelORMBuilder;

use Illuminate\Support\Facades\DB;
use spimpolari\LaravelORMBuilder\ORMBuilderHelper;
use spimpolari\LaravelORMBuilder\ORMBuilderModel;
use Illuminate\Console\Command;
 

class ORMBuilderDBCommand extends Command
{

    protected $signature = 'orm:db '
        . '{path=app/Models : Path of Model File.}'
        . '{namespace=App\Models : Namespace of Model.}'
        . '{--F|force : Force Overwrite model instead of confirm!}'
        . '{--O|only= : List of reverse only Table table1,table2,table3 if set, exclude will be ignored.}'
        . '{--E|exclude= : List of Excluded Table table1,table2,table3}'
        . '{--T|disable-timestamps : Disable all Timestamp, created_at, updated_at, deleted_at option will be ignored}.'
        . '{--D|date_format=U : Set Time Format in created_at, updated_at, deleted_at field}'
        . '{--C|created_at=created_at : Set constant "CREATED_AT" column if exists.}'
        . '{--U|updated_at=updated_at : Set constant "UPDATED_AT" column if exists.}'
        . '{--deleted_at=deleted_at : Set delete property field name if exists, to work -S is mandatory.}'
        . '{--disable-fillable : Disable write all Row except primary key is fillable, commented by default.}'
        . '{--G|disable-guarded : Disable write all Row guarded property, commented by default.}'
        . '{--disable-primary : Disable write primary key property.}'
        . '{--disable-property : Disable write comment PHPDoc property format for suppot in IDE.}'
        . '{--disable-table : Disable write table property.}'
        . '{--R|disable-relation : Disable relation name scan based on field name_id.}'
        . '{--P|disable-plural-table : Disable auto convert plural name table to single name model, use on non english table.}'
        . '{--S|enable-softdelete : Enable Soft Delete.}';
    

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reverse all table into orm Eloquent Model.';

    /**
     * Packager helper class.
     * @var ORMBuilderHelper
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
        
        /** @var $em \Doctrine\ORM\EntityManager */
        $platform = DB::getDoctrineConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');
        $platform->registerDoctrineTypeMapping('set', 'string');
        
        /**
         * Get all Table
         */
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        
        /**
         * get path of eloquent model
         */
        $path = $this->argument('path');
        
        /**
         * Get only table
         */
        if(! is_null(($only = $this->option('only')))) {   
            $this->info('Table Reverse only:'.$only);
            $only_tables = preg_split('#,#', $only);
            if(count($only_tables) == 0) {
                $only_tables[] = $only;
            }
            $exclude_tables = [];
        } else {
            $only_tables = null;
            /**
             * Get exclude table
             */
            if(! is_null(($exclude = $this->option('exclude')))) {   
                $this->info('Table Reverse excluded:'.$exclude);
                $exclude_tables = preg_split('#,#', $exclude);
                if(count ($exclude_tables) == 0) {
                    $exclude_tables[] = $exclude; 
                }
            } else {
                $this->info('All Table Reverse');
                $exclude_tables = [];
            }
        }
        
        /**
         * Verify if only and exclude table exists
         */
        if(count($only_tables) > 0) {
            foreach($only_tables as $check_only_table_exists) {
                if(!in_array ($check_only_table_exists, $tables)) {
                    if(!$this->confirm('The table '.$check_only_table_exists.' specified in only option does not exist! Continue?')) {
                        $this->info('Exit by prompt');
                        exit();
                    }
                }
            }
        }
        
        if(count($exclude_tables) > 0) {
            foreach($exclude_tables as $check_exclude_table_exists) {
                if(!in_array ($check_exclude_table_exists, $tables)) {
                    if(!$this->confirm('The table '.$check_exclude_table_exists.' specified in exclude option does not exist! Continue?')) {
                        $this->info('Exit by prompt');
                        exit();
                    }
                }
            }
        }
        
        /**
         * Make dir of Model if no exists
         */
        if(!$this->helper->makeModelDir(base_path().'/'.$this->argument('path'))) {
            if (is_dir(base_path().'/'.$this->argument('path'))) {
                $this->warn('Directory '.base_path().'/'.$this->argument('path').' Exists');
            } else {
                $this->error('Failed to create directory '.base_path().'/'.$this->argument('path'));
                die();
            }
        } else {
            $this->info('Directory '.base_path().'/'.$this->argument('path').' created');
        }
            
        
        /**
         * Generate Table and column for reverse
         */
        foreach($tables as $table) {
            if(is_null ($only_tables)) {
                if(!in_array ($table, $exclude_tables)) {
                    $reverse_table[$table] = [];
                    $reverse_column[$table] = [];
                    $columns = DB::connection()->getDoctrineSchemaManager()->listTableColumns($table);
            
                    foreach($columns as $key=>$column) {
                        $reverse_table[$table][] = $column->getName();
                        $reverse_column[$table][$column->getName()] = $column->getType();
                    }
                }
            } else {
                if(in_array ($table, $only_tables)) {
                    $reverse_table[$table] = [];
                    $reverse_column[$table] = [];
                    $columns = DB::connection()->getDoctrineSchemaManager()->listTableColumns($table);
            
                    foreach($columns as $key=>$column) {
                        $reverse_table[$table][] = $column->getName();
                        $reverse_column[$table][$column->getName()] = $column->getType();
                    }
                }
            }
        }


        
        if(!$this->option ('disable-relation')) {
            $reverse_relation = $this->helper->scanRelation($reverse_column, $this->option ('disable-plural-table'));
        }
        
        $this->info('All table and column read');
        
        
        /**
         * Generate ORM Model
         */
        foreach($reverse_table as $key=>$all_column) {
            if(!$this->option ('disable-plural-table')) {
                $fullPath = base_path().'/'.$this->argument('path').'/'. str_singular(ucfirst ($key)).'.php';
            } else {
                $fullPath = base_path().'/'.$this->argument('path').'/'. ucfirst ($key).'.php';
            }

            /**
             * Setup and render model
             */
            $model = new ORMBuilderModel($fullPath);
            
            if(!$this->option ('disable-plural-table')) {
                $ModelName = str_singular($key);
            } else {
                $ModelName = $key;
            }
            
            $model->setModelName($ModelName);
            $model->setNamespace($this->argument('namespace'));
            
            if(!$this->option ('disable-table')) {
                $model->setTable($key);
            }
            
            if(!$this->option ('disable-primary')) {
                $model->setPrimaryKey('id');
            }

            $model->setDateFormat($this->option('date_format'));
            
            $exists = false;
            
            if(in_array ($this->option ('created_at'), $all_column) && (!$this->option ('disable-timestamps'))) {
                $model->setCreatedAt($this->option ('created_at'));
                $exists = true;
            }
            
            if(in_array ($this->option ('updated_at'), $all_column) && (!$this->option ('disable-timestamps'))) {
                $model->setUpdatedAt($this->option ('updated_at'));
                $exists = true;
            }
            
            if(in_array ($this->option ('deleted_at'), $all_column) && (!$this->option ('disable-timestamps')) && $this->option ('enable-softdelete')) {
                $model->setDeletedAt($this->option ('deleted_at'));
                $exists = true;
            }
            
            if($exists === false || $this->option ('disable-timestamps')) {
                $model->setTimestamps();
            }
            
            if(!$this->option ('disable-fillable')) {
                $model->setFillable($all_column, ['id']);
            }
            
            if(!$this->option ('disable-guarded')) {
                $model->setGuarded($all_column);
            }
            
            if(!$this->option ('disable-property')) {
                $model->setPropertyComment($reverse_column[$key]);
            }

            if(!$this->option ('disable-property')) {
                $model->setMethodComment($this->argument('namespace'), $ModelName);
            }

            if(!$this->option('disable-relation')) {
                if(isset($reverse_relation[$key])) {
                    $model->setRelation($reverse_relation[$key], $this->argument('namespace'));
                }
            }


            
            if(file_exists ($model->fullPath) && !$this->option ('force')) {
                if($this->confirm('Model file '.ucfirst($key).' Exists, Overwrite?')) {
                    $this->helper->replace($model->fullPath, $model->list, $model->replace);
                    $this->info('Model '.ucfirst($key).' Write');
                } else {
                    $this->warn('Model '.ucfirst($key).' not Write');
                }
            } else {
                $this->helper->replace($model->fullPath, $model->list, $model->replace);
                $this->info('Model '.ucfirst($key).' Write');
            }
        }
    }
}