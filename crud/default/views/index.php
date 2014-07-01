<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var yii\gii\generators\crud\Generator $generator
 */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
use <?= $generator->indexWidgetType === 'grid' ? "yii\\grid\\GridView" : "yii\\widgets\\ListView" ?>;

/**
* @var yii\web\View $this
* @var yii\data\ActiveDataProvider $dataProvider
* @var <?= ltrim($generator->searchModelClass, '\\') ?> $searchModel
*/

$this->title = '<?= Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">

    <?=
    "<?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>
    echo $this->render('_search', ['model' =>$searchModel]);
    ?>

    <div class="clearfix">
        <p class="pull-left">
            <?= "<?= " ?>Html::a('New', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <div class="pull-right">


            <?php foreach ($generator->getModelRelations() AS $relation): ?>
                <?php
                // ignore pivot tables
                $iconType = ($relation->multiple) ? 'arrow-right' : 'arrow-left';
                if (strstr($relation->modelClass, 'X')) { # TODO: pivot detection, move to getModelRelations
                    $iconType = 'random';
                }
                $controller = strtolower(
                    preg_replace(
                        '/([a-z])([A-Z])/',
                        '$1-$2',
                        $generator->pathPrefix . StringHelper::basename($relation->modelClass)
                    )
                );
                ?>

                <!--
                <?= "<?= " ?>Html::a('<i class="glyphicon glyphicon-<?= $iconType ?>"></i> <?=
                Inflector::camel2words(
                    StringHelper::basename($relation->modelClass)
                ) ?>', ['<?= $controller ?>/index'], ['class' => 'btn btn-default']) ?>
                -->

                <?php
                $label = Inflector::titleize(StringHelper::basename($relation->modelClass),true);
                $items[] = [
                    'label' => '<i class="glyphicon glyphicon-' . $iconType . '"> '.$label.'</i>',
                    'url' => [$controller.'/index']
                ]
                ?>
            <?php endforeach; ?>

            <?php
            echo \yii\bootstrap\ButtonDropdown::widget(
                [
                    'id'       => 'giiant-relations',
                    'label'    => 'Relations',
                    'dropdown' => [
                        'options' => [
                            'class' => 'dropdown-menu-right'
                        ],
                        'encodeLabels' => false,
                        'items'        => $items
                    ],
                ]
            );

            ?>
        </div>
    </div>

    <?php if ($generator->indexWidgetType === 'grid'): ?>

        <?= "<?php " ?>echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' =>"{summary}\n{pager}\n{items}\n{pager}",
        'columns' => [
        <?php
        $count = 0;
        $test = new \common\components\RelationProvider();
        $modelClasses = $test->columnAttributes;

        foreach ($generator->getTableSchema()->columns as $column) {
            $format     = $generator->generateColumnFormat($column);
            $relation   = $generator->getRelationByColumn($column);

            if (++$count < 6) {
                if($relation && array_key_exists($relation->modelClass,$modelClasses) ) {

//                    if ($relation)
//                        echo \Yii::$app->log->logger->log($relation->modelClass, 10, 'relation') . PHP_EOL;

                    echo $format . ",\n";
                }elseif($column->name === 'path' && !$relation){
                    echo '
                       "path" =>  [
                            "class" => yii\\grid\\DataColumn::className(),
                            "attribute" => "path",
                            "value" => function($model){
                                return yii\bootstrap\Button::widget(
                                    [
                                        "label"     => yii\helpers\Html::img(\Yii::getAlias("@web")."/".$model->path,["class" => "image-responsive col-lg-3"]),
                                        "encodeLabel" => false,
                                        "options"   => [
                                            "type"          =>"button",
                                            "class"         => "img-preview btn col-sm-6",
                                            "data-toggle"   => "popover",
                                            "data-content"  => yii\helpers\Html::img(\Yii::getAlias("@web")."/".$model->path),
                                            "data-html"     => "true",
                                            "data-placement"=> "bottom",
                                            "data-template" => "<div style=\"text-align:center;max-width:100%;width:auto !important;\" class=\"popover col-lg-12\" role=\"tooltip\"><div class=\"arrow\"></div><h3 class=\"popover-title\"></h3><div class=\"popover-content\"></div></div>"
                                        ]
                                    ]
                                );
                            },
                            "format" => "raw"
                        ]
                    '. ",\n";
                }else{
                    echo "\t\t\t'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
                }
            } else {
                if($relation && !array_key_exists($relation->modelClass,$modelClasses)){
                    echo "\t\t\t// '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
                }
            }
        }
        ?>

        ['class' => '<?= $generator->actionButtonClass ?>'],
        ],
        ]); ?>
    <?php else: ?>
        <?= "<?php " ?>echo ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
        return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
        },
        ]); ?>
    <?php endif; ?>

</div>
