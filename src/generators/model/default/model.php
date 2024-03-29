<?php
/**
 * This is the template for generating the model class of a specified table.
 * DO NOT EDIT THIS FILE! It may be regenerated with Gii.
*/
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var schmunk42\giiant\generators\model\Generator $generator
 * @var string $tableName full table name
 * @var string $className class name
 * @var string $ns class namespace
 * @var string $queryClassName  queryclass name
 * @var yii\db\TableSchema $tableSchema
 * @var string[] $labels list of attribute labels (name => label)
 * @var string[] $rules list of validation rules
 * @var array $relations list of relations (name => relation declaration)
 * @var array $translation
 * @var array $traits
 */

$activeRecordClass = '\\' . ltrim($generator->baseClass, '\\');
$translationExists = false;

echo "<?php\n";
?>
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace <?= $generator->ns ?>\base;

use Yii;
use yii\helpers\ArrayHelper;
<?php if (isset($translation)): ?>
use dosamigos\translateable\TranslateableBehavior;
<?php endif; ?>
<?php if (!empty($blameable)): ?>
use yii\behaviors\BlameableBehavior;
<?php endif; ?>
<?php if (!empty($timestamp)): ?>
use <?php echo $timestamp['timestampBehaviorClass']; ?>;
<?php endif; ?>
<?php if($queryClassName): ?>
use <?php echo ($generator->ns .'\\base' === $generator->queryNs ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName) ?>;
<?php endif; ?>

/**
 * This is the base-model class for table "<?= $tableName ?>".
 *
<?php foreach ($tableSchema->columns as $column): ?>
 * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
 *
<?php foreach ($relations as $name => $relation): ?>
 * @property \<?=$ns?>\<?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endforeach; ?>
<?php endif; ?>
 */
abstract class <?= $className ?> extends <?= $activeRecordClass . "\n" ?>
{
<?php
    if (!empty($traits)) {
        echo "\tuse " . implode(', ', $traits) . ';' . PHP_EOL;
    }
?>

<?php
if(!empty($enum)){
?>
    /**
    * ENUM field values
    */
<?php
    foreach($enum as $column_name => $column_data){
        foreach ($column_data['values'] as $enum_value){
            echo '    const ' . $enum_value['const_name'] . ' = \'' . $enum_value['value'] . '\';' . PHP_EOL;
        }
    }
}
?>
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '<?= $tableName ?>';
    }
<?php if ($generator->db !== 'db'): ?>

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     * @throws \yii\base\InvalidConfigException
     */
    public static function getDb()
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>
<?php if (isset($translation) || !empty($blameable) || !empty($timestamp)): ?>

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
    <?php if (!empty($blameable)): ?>
    $behaviors['blameable'] = [
            'class' => BlameableBehavior::class,
    <?php if ($blameable['createdByAttribute'] !== 'created_by'): ?>
        'createdByAttribute' => <?= $blameable['createdByAttribute'] ? "'" . $blameable['createdByAttribute'] . "'" : 'false' ?>,
    <?php endif; ?>
    <?php if ($blameable['updatedByAttribute'] !== 'updated_by'): ?>
    'updatedByAttribute' => <?= $blameable['updatedByAttribute'] ? "'" . $blameable['updatedByAttribute'] . "'" : 'false' ?>,
    <?php endif; ?>
];
    <?php endif; ?>
    <?php if (!empty($timestamp)): ?>
$behaviors['timestamp'] = [
            'class' => TimestampBehavior::class,
        <?php if (!empty($timestamp['value'])): ?>
    'value' => <?= $timestamp['value'] ?>,
        <?php endif; ?>
        <?php if ($timestamp['createdAtAttribute'] !== 'created_at'): ?>
            'createdAtAttribute' => <?= $timestamp['createdAtAttribute'] ? "'" . $timestamp['createdAtAttribute'] . "'" : 'false' ?>,
        <?php endif; ?>
        <?php if ($timestamp['updatedAtAttribute'] !== 'updated_at'): ?>
            'updatedAtAttribute' => <?= $timestamp['updatedAtAttribute'] ? "'" . $timestamp['updatedAtAttribute'] . "'" : 'false' ?>,
        <?php endif; ?>
];
    <?php endif; ?>
    <?php if (isset($translation)): ?>
<?php if (!empty($translation['fields'])): ?>
<?php $translationExists = true; ?>
$behaviors['translation'] = [
            'class' => TranslateableBehavior::class,
            // 'relation' => 'translations',
            <?php if ($generator->languageCodeColumn !== 'language'): ?>
                'languageField' => '<?= $generator->languageCodeColumn ?>',
            <?php endif; ?>
            'skipSavingDuplicateTranslation' => true,
            'translationAttributes' => [
            <?= "'" . implode("',\n                    '", $translation['fields']) . "'\n" ?>
            ],
            'deleteEvent' => <?= $activeRecordClass ?>::EVENT_BEFORE_DELETE,
            'restrictDeletion' => TranslateableBehavior::DELETE_LAST
        ];
        <?php endif; ?>
        <?php if (!empty($translation['additions'])): ?>
            <?php $translationExists = true; ?>
            <?php foreach ($translation['additions'] as $name => $values): ?>
                <?php if (!empty($values['fields'])): ?>
                    $behaviors['translation_<?= mb_strtolower($name) ?>'] = [
                    'class' => TranslateableBehavior::class,
                    'relation' => 'translation<?= ucfirst(mb_strtolower($name)) ?>s',
                    <?php if ($generator->languageCodeColumn !== 'language'): ?>
                        'languageField' => '<?= $generator->languageCodeColumn ?>',
                    <?php endif; ?>
                    <?php if ($name === 'meta'): ?>
                        // This is not a boolean parameter it only sets the fallbackLanguage to an invalid value!
                        'fallbackLanguage' => false,
                        'skipSavingDuplicateTranslation' => false,
                    <?php else: ?>
                        'skipSavingDuplicateTranslation' => true,
                    <?php endif; ?>
                    'translationAttributes' => [
                    <?= "'" . implode("',\n                    '", $values['fields']) . "'\n" ?>
                    ],
                    'deleteEvent' => <?=$activeRecordClass?>::EVENT_BEFORE_DELETE
                    ];

                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>

    return $behaviors;
    }
<?php endif; ?>

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $parentRules = parent::rules();
<?php if ($translationExists): ?>
        $parentRules += $this->importTranslationAttributeRules();
<?php endif; ?>
        return ArrayHelper::merge($parentRules, [<?php echo "\n            " . implode(",\n            ", $rules) . "\n        " ?>]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
<?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
        ]);
    }
<?php if (!empty($hints)): ?>

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return ArrayHelper::merge(parent::attributeHints(), [
<?php foreach ($hints as $name => $hint): ?>
            <?= "'$name' => " . $generator->generateString($hint) . ",\n" ?>
<?php endforeach; ?>
        ]);
    }
<?php endif; ?>
<?php foreach ($relations as $name => $relation):?>

    /**
     * @return \yii\db\ActiveQuery
     */
    public function get<?= $name ?>()
    {
        <?= $relation[0] . "\n" ?>
    }
<?php endforeach; ?>

<?php if (isset($translation)): ?>
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        <?= $translation['code'] . "\n"?>
    }

    <?php if (!empty($translation['additions'])): ?>
        <?php foreach ($translation['additions'] as $name => $values): ?>
            <?php if (isset($values['code'])): ?>
            /**
             * @return \yii\db\ActiveQuery
             */
            public function getTranslation<?= ucfirst(mb_strtolower($name)) ?>s()
            {
            <?= $values['code'] . "\n"?>
            }
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

<?php endif; ?>
<?php if ($queryClassName): ?>
    /**
     * @inheritdoc
     * @return <?= $queryClassName ?> the active query used by this AR class.
     */
    public static function find()
    {
        return new <?= $queryClassName ?>(static::class);
    }
<?php endif; ?>
<?php
    foreach($enum as $column_name => $column_data):
?>

    /**
     * get column <?php echo $column_name?> enum value label
     * @param string $value
     * @return string
     */
    public static function <?php echo $column_data['func_get_label_name']?>($value){
        $labels = self::<?php echo $column_data['func_opts_name']?>();
        if(isset($labels[$value])){
            return $labels[$value];
        }
        return $value;
    }

    /**
     * column <?php echo $column_name?> ENUM value labels
     * @return array
     */
    public static function <?php echo $column_data['func_opts_name']?>()
    {
        return [
<?php
        foreach($column_data['values'] as $k => $value){
            echo '            '.'self::' . $value['const_name'] . ' => ' . $generator->generateString($value['label']) . ",\n";
        }
?>
        ];
    }
<?php endforeach; ?>
}
