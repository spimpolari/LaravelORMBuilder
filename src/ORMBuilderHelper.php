<?php

namespace spimpolari\LaravelORMBuilder;

use Illuminate\Filesystem\Filesystem;

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
     * @param type $file
     * @param type $list
     * @param type $replace
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
     * @param type $path
     * @return type
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
                
                echo $table_id;
                
                if(array_key_exists ($table_id, $columns)) {
                    if($columns[$table_id] == 'Integer') {
                        $relation[$table_config][] = array('hasMany', $function_call, $model_name_scan, $table_id);
                        $relation[$table_scan][] = array('belongsTo', $table_call , $model_name, $table_id, 'id');
                    }
                }

            }
            
            
        }
        
        return $relation;
    }
}