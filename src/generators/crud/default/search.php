<?php
/**
 * This is the template for generating CRUD search class of the specified model.
 */

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View
 * @var $generator schmunk42\giiant\generators\crud\Generator
 */

$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $modelAlias = $modelClass.'Model';
}
$rules = $generator->generateSearchRules();
$labels = $generator->generateSearchLabels();
$searchAttributes = $generator->getSearchAttributes();
$searchConditions = $generator->generateSearchConditions();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->searchModelClass, '\\')) ?>;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use <?= ltrim($generator->modelClass, '\\').(isset($modelAlias) ? " as $modelAlias" : '') ?>;

/**
* <?= $searchModelClass ?> represents the model behind the search form about `<?= $generator->modelClass ?>`.
*/
class <?= $searchModelClass ?> extends <?= isset($modelAlias) ? $modelAlias : $modelClass ?>

{
/**
* @inheritdoc
*/
public function rules()
{
return [
<?= implode(",\n            ", $rules) ?>,
];
}

/**
* @inheritdoc
*/
public function scenarios()
{
// bypass scenarios() implementation in the parent class
return Model::scenarios();
}

/**
* Creates data provider instance with search query applied
*
* @param array $params
*
* @return ActiveDataProvider
*/
public function search($params)
{
$query = <?= $modelAlias ?? $modelClass ?>::find();

<?php if ($generator->getHasTranslationRelation()): ?>
    $query->leftJoin(<?= $generator->translationModelClass?>::tableName(),<?= $modelAlias ?? $modelClass ?>::tableName() . '.id = ' . <?= $generator->translationModelClass?>::tableName() . '.<?= mb_strtolower(Inflector::camel2id(basename($modelClass),'_'))?>_id');
<?php endif; ?>


$dataProvider = new ActiveDataProvider([
'query' => $query,
]);

$this->load($params);

if (!$this->validate()) {
// uncomment the following line if you do not want to any records when validation fails
// $query->where('0=1');
return $dataProvider;
}
<?php if ($generator->getHasTranslationRelation()): ?>
    $query->groupBy(<?= $modelAlias ?? $modelClass ?>::tableName() . '.id');
<?php endif; ?>

<?= implode("\n        ", $searchConditions) ?>

return $dataProvider;
}
}
