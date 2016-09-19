<?php
/**
 * This is the template for generating the model static class of a specified table.
 *
 * @var yii\web\View $this
 * @var yii\gii\generators\model\Generator $generator
 * @var string $tableName full table name
 * @var string $className class name
 * @var yii\db\TableSchema $tableSchema
 * @var string[] $labels list of attribute labels (name => label)
 * @var string[] $rules list of validation rules
 * @var array $relations list of relations (name => relation declaration)
 */

$forHideColumns = ['hidded' => 0, 'deleted' => 0, 'show' => 1];
$hideColumnName = false;
$hideColumnValue = false;
foreach ($tableSchema->columns as $column) {
    foreach($forHideColumns as $hName => $hValue){
        if(strtolower($column->name) == $hName){
            $hideColumnName = $column->name;
            $hideColumnValue = $hValue;
        }
    }
}

echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the static model class for table "<?= $tableName ?>".
 */
class <?= $className ?>Static
{

    public static $listData;

    /**
     * create array for listbox
     * @param boolean $forValue list hidded elements
     * @return type
     */
    public static function getListData($forValue = false)
    {
<?php if($hideColumnName): ?>


        if (self::$listData && isset(self::$listData[$forValue])){
            return self::$listData[$forValue];
        }        
<?php else: ?>

        if (self::$listData ){
            return self::$listData;
        }                
<?php 
endif;
if (isset($translation)): 
?>
        $query = <?= $className ?>::find()
                ->select('`<?=$tableName?>`.`<?=$tableSchema->primaryKey[0]?>` `id`, `lang`.`<?=$translation['fields'][0]?>`  `name`')
                ->leftJoin('`<?=$translation['language_table']?>`  lang ', '<?=$tableName?>.`<?=$tableSchema->primaryKey[0]?>` = lang.<?=$tableName?>_id and lang.language = "'.Yii::$app->language.'" ')                
                ->orderBy('`lang`.`<?=$translation['fields'][0]?>`');

<?php else: ?>
        
        $query = <?= $className ?>::find()
                ->select('`<?=$tableSchema->primaryKey[0]?>` `id`, `name`')
                ->orderBy('`name`');
<?php 
endif; 
if($hideColumnName): 
?>
        if (!$forValue) {
            $query->where(['<?=$hideColumnName?>' => '<?=$hideColumnValue?>']);
        }
        $rows = $query->asArray()->all();
        return self::$listData[$forValue] = ArrayHelper::map($rows,'id', 'name');                

<?php else: ?>
        
        $rows = $query->asArray()->all();
        return self::$listData = ArrayHelper::map($rows,'id', 'name');                
        
<?php endif; ?>
    }        

    /**
     * get label for record
     * @param int $id
     * @return string
     */    
    public static function getLabel($id)
    {
<?php if($hideColumnName): ?>    
        if (!self::$listData || !isset(self::$listData[true])){
            self::getListData(true);
        }
                return isset(self::$listData[true][$id])?self::$listData[true][$id]:'';
<?php else: ?>    
        if (!self::$listData){
            self::getListData();
        }
        
        return isset(self::$listData[$id])?self::$listData[$id]:'';
<?php endif; ?>    
    }
}
