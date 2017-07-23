<?php
namespace spimpolari\LaravelORMBuilder;

/**
 * 
 */
class ORMBuilderModel 
{
    /**
     *
     * @var string full patch of model
     */
    public $fullPath = null;
    
    /**
     *
     * @var array list of stub element in stub file
     */
    public $list = [
        'stub_namespace', 'stub_model_name', 'stub_extends_class', 'stub_comment_property', 'stub_comment_method', 'stub_property_table', 'stub_primary_key', 'stub_timestamp', 'stub_date_format', 'stub_created_at', 'stub_updated_at', 'stub_use_softdelete', 'stub_trait_softdelete',  'stub_deleted_at', 'stub_fillable', 'stub_guarded', 'stub_relation'
    ];
    
    /**
     *
     * @var array list of default stub element value
     */
    public $replace = [
        'stub_namespace' => '',
        'stub_model_name'=>'',
        'stub_extends_class' => 'Model',
        'stub_comment_property' => '',
        'stub_comment_method' => '',
        'stub_property_table'=>'',
        'stub_primary_key'=>'//protected $primaryKey = \'id\';',
        'stub_timestamp'=>'//public $timestamps = false;',
        'stub_date_format'=>'//protected $dateFormat = \'U\';',
        'stub_created_at'=>'//const CREATED_AT = \'creation_date\';',
        'stub_updated_at'=>'//const CREATED_AT = \'creation_date\';',
        'stub_use_softdelete'=>'//use Illuminate\Database\Eloquent\SoftDeletes;',
        'stub_trait_softdelete'=>'//use SoftDeletes;',
        'stub_deleted_at'=>'//protected $dates = [\'deleted_at\'];',
        'stub_fillable'=>'//protected $fillable = [];',
        'stub_guarded'=>'//protected $guarded = [];',
        'stub_relation' =>'//'
    ];    
    
    /**
     * 
     * @param type $fullPath
     */
    public function __construct ($fullPath) 
    {
        $this->fullPath = $fullPath;
    }

    /**
     * 
     * set the namespace stub value
     * 
     * @param type $namespace
     */
    public function setNamespace($namespace)
    {
        $this->replace['stub_namespace'] = 'namespace '.$namespace.';';
    }
    
    /**
     * 
     * set de model name stub value
     * 
     * @param type $modelName
     */
    public function setModelName($modelName)
    {
        $this->replace['stub_model_name'] = ucfirst($modelName);
    }
    
    /**
     * 
     * set table name stub value
     * 
     * @param type $table
     */
    public function setTable($table)
    {
        $this->replace['stub_property_table'] = 'protected $table = \''.$table.'\';';
    }

    /**
     * 
     * set primarykey stub value
     * 
     * @param type $primaryKey
     */
    public function setPrimaryKey($primaryKey)
    {
        $this->replace['stub_primary_key'] = 'protected $primaryKey = \''.$primaryKey.'\';';
    }
    
    /**
     * 
     * set timestamp stub value
     * 
     */
    public function setTimestamps()
    {
        $this->replace['stub_timestamp'] = 'protected $timestamps = false;';
    }
    
    /**
     * 
     * set date format stub value
     * 
     * @param type $format
     */
    public function setDateFormat($format)
    {
        $this->replace['stub_date_fomat'] = 'protected $dateFormat = \''.$format.'\';';
    }

    /**
     * 
     * set create at stub value
     * 
     * @param type $field
     */
    public function setCreatedAt($field)
    {
        $this->replace['stub_created_at'] = 'const CREATED_AT = \''.$field.'\';';
    }
    
    /**
     * 
     * @param type $field
     */
    public function setUpdatedAt($field)
    {
        $this->replace['stub_updated_at'] = 'const UPDATED_AT = \''.$field.'\';';
    }
    
    /**
     * 
     * @param type $field
     */
    public function setDeletedAt($field)
    {
        $this->replace['stub_use_softdelete'] = 'use Illuminate\Database\Eloquent\SoftDeletes;';
        $this->replace['stub_trait_softdelete'] = 'use SoftDeletes;';
        $this->replace['stub_deleted_at'] = 'protected $dates = [\''.$field.'\'];';
    }
    
    /**
     * 
     * @param type $column
     * @param type $limit
     */
    public function setFillable($column, $limit = [])
    {
        $array = array_diff($column, $limit);
        $this->replace['stub_fillable'] =  '//protected $fillable = '.$this->generateStringArray($array).';';
    }
    
    /**
     * 
     * @param type $column
     * @param type $limit
     */
    public function setGuarded($column, $limit = [])
    {
        $array = array_diff($column, $limit);
        $this->replace['stub_guarded'] =  '//protected $guarded = '.$this->generateStringArray($array).';';
    }

    /**
     *
     * @param type $columns
     */
    public function setPropertyComment($columns)
    {
        $property_comment = '';

        foreach($columns as $column=>$type) {

            $property_comment .= ' * @property '.$type.' $'.$column.' commento'."\n";
        }

        $this->replace['stub_comment_property'] .= $property_comment;
    }

    /**
     *
     * @param type $columns
     */
    public function setMethodComment($namespace, $modelName)
    {
        $method_comment = ' * @method \\'.$namespace.'\\'.ucfirst($modelName).'[] get()'."\n";
        $method_comment .= ' * @method static \\'.$namespace.'\\'.ucfirst($modelName).'[] all()'."\n";
        $method_comment .= ' * @method static \\'.$namespace.'\\'.ucfirst($modelName).' find(integer|array $integer)'."\n";
        $method_comment .= ' * @method static \\'.$namespace.'\\'.ucfirst($modelName).' first(integer $integer)'."\n";
        $method_comment .= ' * @method static \\'.$namespace.'\\'.ucfirst($modelName).' where(string $column, string $value)'."\n";
        $method_comment .= ' * @method static \\'.$namespace.'\\'.ucfirst($modelName).' orderBy(string $column, string $order)'."\n";
        $method_comment .= ' * @method static \\'.$namespace.'\\'.ucfirst($modelName).' findOrFail(integer $integer)'."\n";
        $method_comment .= ' * @method static \\'.$namespace.'\\'.ucfirst($modelName).' firstOrFail()'."\n";
        $method_comment .= ' * @method integer count()'."\n";
        $method_comment .= ' * @method save()'."\n";
        $method_comment .= ' * @method update()'."\n";
        $method_comment .= ' * @method delete()'."\n";
        $method_comment .= ' * @method destroy(integer $integer|array $integer)'."\n";
        $method_comment .= ' * @method static \\'.$namespace.'\\'.ucfirst($modelName).' firstOrCreate(array $column_update)'."\n";
        $method_comment .= ' * @method static \\'.$namespace.'\\'.ucfirst($modelName).' firstOrNew(array $column_update)'."\n";
        $method_comment .= ' * @method static \\'.$namespace.'\\'.ucfirst($modelName).' updateOrCreate(array $column_update)'."\n";
        $method_comment .= ' * @method static \\'.$namespace.'\\'.ucfirst($modelName).' create(array $column_update)';
        $this->replace['stub_comment_property'] .= $method_comment;
    }

    public function setIntegrateIdeHelper()
    {
        $this->replace['stub_extends_class'] = '\Eloquent';
    }

    /**
     * 
     * @param type $table_relation
     * @param type $namespace
     */
    public function setRelation($table_relation, $namespace)
    {
        $relation = '';
        
        foreach($table_relation as $option_key) {
            $relation .= "\t".'public function '.lcfirst($option_key[1]).'() { return $this->'.$option_key[0].'(\''.$namespace.'\\'.ucfirst($option_key[2]).'\', \''.$option_key[3].'\'); }'."\n\n";
        }

        
        $this->replace['stub_relation'] = $relation;
    }
    
    /**
     * 
     * @param type $array
     * @return type
     */
    protected function generateStringArray($array)
    {
        $seq = implode('\', \'', $array);
        return '[\''.$seq.'\']';
    }
    

}