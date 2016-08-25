<?php
/**
 * This is the template for generating the model class of a specified table.
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

echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use Yii;
use \<?= $generator->ns ?>\base\<?= $className ?> as Base<?= $className ?>;

/**
 * This is the model class for table "<?= $tableName ?>".
 */
class <?= $className ?> extends Base<?= $className . "\n" ?>
{

    public static $listBoxData;

<?php if (isset($translation)): ?>
    /**
     * create array for listbox
     * @param boolean $forValue list hidded elements
     * @return type
     */
    public static function forListbox($forValue = false)
    {

        if (self::$listBoxData){
            return self::$listBoxData;
        }    
    
        $query = self::find()
                ->select('`<?=$tableName?>`.`<?=$tableSchema->primaryKey[0]?>` `id`, `lang`.`<?=$translation['fields'][0]?>`  `name`')
                ->leftJoin('`<?=$translation['language_table']?>`  lang ', '<?=$tableName?>.`<?=$tableSchema->primaryKey[0]?>` = lang.<?=$tableName?>_id and lang.language = "'.Yii::$app->language.'" ')                
                ->orderBy('`lang`.`<?=$translation['fields'][0]?>`');
                
//        if (!$forValue) {
//            $query->where(['hidded' => 0]);
//        }
        
        $rows = $query->asArray()->all();

        return self::$listBoxData = \yii\helpers\ArrayHelper::map($rows,'id', 'name');        
    }        
<?php endif; ?>

<?php if (!isset($translation)): ?>
    /**
     * create array for listbox
     * @param boolean $forValue list hidded elements
     * @return type
     */    
    public static function forListbox($forValue = false)
    {
    
        if (self::$listBoxData){
            return self::$listBoxData;
        }
        
        $rows = self::find()
                ->select('`<?=$tableSchema->primaryKey[0]?>` `id`, `name`')
                //->where(['hidded' => 0])
                ->orderBy('`name`');

//        if (!$forValue) {
//            $query->where(['hidded' => 0]);
//        }                
                
        $rows = $query->asArray()->all();
                
        return self::$listBoxData = \yii\helpers\ArrayHelper::map($rows,'id', 'name');        
    }        
<?php endif; ?>
}
