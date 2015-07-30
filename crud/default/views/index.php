<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use loop8\l8actioncolumn\L8ActionColumn;

/**
 * @var yii\web\View $this
 * @var schmunk42\giiant\crud\Generator $generator
 */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

/** @var \yii\db\ActiveRecord $model */
$model = new $generator->modelClass;
$model->setScenario('crud');
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    /** @var \yii\db\ActiveRecord $model */
    $model = new $generator->modelClass;
    $safeAttributes = $model->safeAttributes();
    if (empty($safeAttributes)) {
        $safeAttributes = $model->getTableSchema()->columnNames;
    }
}

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\helpers\Url;
use <?= $generator->indexWidgetType === 'grid' ? "yii\\grid\\GridView" : "yii\\widgets\\ListView" ?>;

/**
* @var yii\web\View $this
* @var yii\data\ActiveDataProvider $dataProvider
* @var <?= ltrim($generator->searchModelClass, '\\') ?> $searchModel
*/

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="giiant-crud <?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-index">

    <?=
    "<?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>
    echo $this->render('_search', ['model' =>$searchModel]);
    ?>

    <div class="clearfix">
        <p class="pull-left">
            <?= "<?= " ?>Html::a('<span class="glyphicon glyphicon-plus"></span> ' . <?= $generator->generateString('New') ?>, ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <div class="pull-right">

            <?php
            $items = '';
            $model = new $generator->modelClass;
            ?>
            <?php foreach ($generator->getModelRelations($model) AS $relation): ?>
                <?php
                // relation dropdown links
                $iconType = ($relation->multiple) ? 'arrow-right' : 'arrow-left';
                if ($generator->isPivotRelation($relation)) {
                    $iconType = 'random';
                }
                $controller = $generator->pathPrefix . Inflector::camel2id(
                        StringHelper::basename($relation->modelClass),
                        '-',
                        true
                    );
                $route = $generator->createRelationRoute($relation,'index');
                $label      = Inflector::titleize(StringHelper::basename($relation->modelClass), '-', true);
                $items .= <<<PHP
            [
                'url' => ['{$route}'],
                'label' => '<i class="glyphicon glyphicon-arrow-right">&nbsp;' . {$generator->generateString($label)} . '</i>',
            ],
PHP;
                ?>
            <?php endforeach; ?>

            <?= "<?= \n" ?>
            \yii\bootstrap\ButtonDropdown::widget(
                [
                    'id'       => 'giiant-relations',
                    'encodeLabel' => false,
                    'label'    => '<span class="glyphicon glyphicon-paperclip"></span> ' . <?= $generator->generateString('Relations') ?>,
                    'dropdown' => [
                        'options'      => [
                            'class' => 'dropdown-menu-right'
                        ],
                        'encodeLabels' => false,
                        'items'        => [<?= $items ?>]
                    ],
                    'options' => [
                        'class' => 'btn-default'
                    ]
                ]
            );
            <?= "?>" ?>
        </div>
    </div>

    <?php if ($generator->indexWidgetType === 'grid'): ?>

        <?= "<?php \yii\widgets\Pjax::begin(['id' => 'pjax-" . Inflector::camel2id(StringHelper::basename($generator->modelClass)) . "-index', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-main ul.pagination a, th a', 'clientOptions' => ['pjax:success'=>'function(){alert(\"yo\")}']]) ?>\n"; ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h2>
                    <i><?= "<?= " . $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) . " ?>" ?></i>
                </h2>
            </div>

            <div class="panel-body">

                <div class="table-responsive">
                <?= "<?= " ?>GridView::widget([
                    'layout' => '{summary}{pager}{items}{pager}',
                    'dataProvider' => $dataProvider,
                    'pager' => [
                        'class' => yii\widgets\LinkPager::className(),
                        'firstPageLabel' => <?= $generator->generateString('First') ?>,
                        'lastPageLabel' => <?= $generator->generateString('Last') ?>
                    ],
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
                    'headerRowOptions' => ['class'=>'x'],
                    'columns' => [
                        //['class' => 'yii\grid\SerialColumn'],
                        <?php
                        $count = 0;
                        if (($tableSchema = $generator->getTableSchema()) === false) {
                            foreach ($generator->getColumnNames() as $name) {
                                echo "            '" . $name . "',\n";
                            }
                        } else {
                            foreach ($tableSchema->columns as $column) {
                                $format = $generator->generateColumnFormat($column);
                                switch ($column->type) {
                                    // TODO: internationalization
                                    case 'time':
                                        echo "            [\n";
                                        echo "                'attribute' => '" . $column->name . "',\n";
                                        echo "                'format' => [\n";
                                        echo "                    'date', 'php:H:i:s'\n";
                                        echo "                ],\n";
                                        echo "            ],\n";
                                        break;
                                    case 'date':
                                        echo "            [\n";
                                        echo "                'attribute' => '" . $column->name . "',\n";
                                        echo "                'format' => [\n";
                                        echo "                    'date', 'php:d.m.Y'\n";
                                        echo "                ],\n";
                                        echo "            ],\n";
                                        break;
                                    case 'datetime':
                                        echo "            [\n";
                                        echo "                'attribute' => '" . $column->name . "',\n";
                                        echo "                'format' => [\n";
                                        echo "                    'date', 'php:d.m.Y H:i:s'\n";
                                        echo "                ],\n";
                                        echo "            ],\n";
                                        break;
                                    default:
                                        echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
                                        break;
                                }
                            }
                        }
                        ?>

                                //['class' => 'yii\grid\ActionColumn'],
                                [
                                    'class' => L8ActionColumn::className(),
                                    'template' => '{view} {update} {delete}',
                                    'urlCreator' => function($action, $model, $key, $index) {
                                        // using the column name as key, not mapping to 'id' like the standard generator
                                        $params = is_array($key) ? $key : [$model->primaryKey()[0] => (string) $key];
                                        $params[0] = \Yii::$app->controller->id ? \Yii::$app->controller->id . '/' . $action : $action;
                                        return Url::toRoute($params);
                                    },
                                    'contentOptions' => ['nowrap'=>'nowrap'],
                                    'buttons' => [
                                        'view' => function($url, $model, $key) {
                                            return L8ActionColumn::viewButton($url, $model, $key, true);
                                        },
                                        'update' => function($url, $model, $key) {
                                            return L8ActionColumn::updateButton($url, $model, $key, true);
                                        },
                                        'delete' => function($url, $model, $key) {
                                            return L8ActionColumn::ajaxDeleteButton($url, $model, $key, true, ['data-name' => Html::encode('entry')]); // ADD HERE THE VALUE FOR CONFIRM BOX
                                        }
                                    ]
                                ],
                            ],
                        ]); ?>
                </div>

            </div>

        </div>

        <?= "<?php \yii\widgets\Pjax::end() ?>\n"; ?>

        <?= "<?php\n" ?>
        <?= "\$initScript = <<<EOF\n" ?>
\$(document).on('click', '.l8ajax-delete', function (event) {
    if(confirm('Are you sure you want to delete "' + \$(event.currentTarget).attr('data-name') + '"?')) {
        \$.ajax(\$(event.currentTarget).attr('data-url'), {
            dataType: "json",
            type: "post"
        }).done(function(data) {
            if(data.response = 'Ok') {
                \$.pjax.reload('#pjax-<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index', {'timeout': 5000});
            } else {
                alert('Error : ' + data.response);
            }
        });
    }
});
EOF;
        <?= "\$this->registerJs(\$initScript);\n" ?>
        <?= "?>\n" ?>

    <?php else: ?>

        <?= "<?= " ?> ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
        return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
        },
        ]); ?>

    <?php endif; ?>

</div>
