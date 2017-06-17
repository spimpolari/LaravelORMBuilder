<?php

namespace spimpolari\LaravelORMBuilder;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;

/**
 * 
 */
class ORMBuilderHelper 
{
        /**
     * The filesystem handler.
     * @var object
     */
    protected $files;


    /**
     * Create a new instance.
     * @param Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
        //$this->composer = new Composer($files);
    }
    
    /**
     * 
     * Replace Stub file an make model
     * 
     * @param  string  $file
     * @param  string  $list
     * @param  string  $replace
     */
    public function replace($file, $list, $replace)
    {
        $content = $this->files->get(__DIR__.'/stub/EloquentModel.stub');
        $replacing = str_replace($list, $replace, $content);
        $this->files->put($file, $replacing);
    }
    
    /**
     * 
     * crate dir model if no exists
     * 
     * @param  string  $path
     * @return  bool
     */
    public function makeModelDir($path)
    {
        if (!is_dir($path)) {
            return mkdir($path, 0755, true);
        }
    }
    
    /**
     * 
     * scann all relation in table
     * 
     * @param type $allTable
     * @param type $singular
     * @return type
     */
    public function scanRelation($allTable, $singular = false)
    {
        $relation  = null;

        $platform = DB::getDoctrineConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');
        $platform->registerDoctrineTypeMapping('set', 'string');

        $tablesScanForRelation = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        foreach($tablesScanForRelation as $tableScan) {
            $allTableScan[$tableScan] = [];
            $columnsScan = DB::connection()->getDoctrineSchemaManager()->listTableColumns($tableScan);

            foreach($columnsScan as $key=>$columnScan) {
                $allTableScan[$tableScan][$columnScan->getName()] = $columnScan->getType();
            }
        }



        foreach($allTable as $table_config=>$col) {
            
            if(!$singular) {
                $model_name = ucfirst(str_singular($table_config));
                $table_id = lcfirst(str_singular($table_config).'_id');
                $table_call = str_singular($table_config);
                
            } else {
                $model_name = ucfirst($table_config);
                $table_id = $table_config.'_id';
                $table_call = $table_config;
            }
            
            foreach($allTable as $table_scan=>$columns) {
                
                
                $belongsTo = null;
                $hasMany = null;
                
                if(!$singular) {
                    $function_call = str_plural($table_scan);
                    $model_name_scan = ucfirst(str_singular($table_scan));
                } else {
                    $function_call = $table_scan;
                    $model_name_scan = ucfirst($table_scan);
                }
                
                
                
                if(array_key_exists ($table_id, $columns)) {
                   
                    if($columns[$table_id] == 'Integer' || $columns[$table_id] == 'BigInt' || $columns[$table_id] == 'TinyInt' || $columns[$table_id] == 'SmallInt' || $columns[$table_id] == 'MediumInt') {
                        $relation[$table_config][] = array('hasMany', $function_call, $model_name_scan, $table_id);
                        $relation[$table_scan][] = array('belongsTo', $table_call , $model_name, $table_id, 'id');
                    }
                }

            }
            
            
        }
        
        return $relation;
    }
}