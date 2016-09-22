<?php
/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\module\Generator */
?>

<?= '<?php' ?>

use rmrevin\yii\fontawesome\FA;
use yii\helpers\Inflector;

/*
 * @var yii\web\View $this
 */
$controllers = \dmstr\helpers\Metadata::getModuleControllers($this->context->module->id);
$favourites  = [];

$patterns = [
    '^default$'  => ['color' => 'gray', 'icon' => FA::_CUBE],
    '^.*$'       => ['color' => 'green', 'icon' => FA::_CUBE],
];

foreach ($patterns AS $pattern => $options) {
    foreach ($controllers AS $c => $item) {
        $controllers[$c]['label'] = $item['name'];
        if (preg_match("/$pattern/", $item['name']) && $item['name'] !== 'default') {
            $favourites[$c]          = $item;
            $favourites[$c]['head']  = ucfirst(substr($item['name'],0,2));
            // ActiveRecord (model) counter
            #$model = \Yii::createObject('app\\modules\\sakila\\models\\'.Inflector::id2camel($item['name']));
            #$favourites[$c]['head']  .= ' <small class="label label-info pull-right">'.count($model->find()->all()).'</small>';
            $favourites[$c]['label'] = $item['name'];
            $favourites[$c]['color'] = $options['color'];
            $favourites[$c]['icon']  = isset($options['icon']) ? $options['icon'] : null;
            unset($controllers[$c]);
        }
    }
}

$dataProvider = new \yii\data\ArrayDataProvider(
    [
        'allModels'  => $favourites,
        'pagination' => [
            'pageSize' => 100
        ]
    ]
);

$listView = \yii\widgets\ListView::widget(
[
    'dataProvider' => $dataProvider,
    'layout' => "{items}\n{pager}",
    'itemView' => function ($data) {
        return '<div class="col-xs-6 col-sm-4 col-lg-3">'.insolita\wgadminlte\SmallBox::widget(
            [
                'head'        => $data['head'],
                'type'        => $data['color'],
                'text'        => $data['label'],
                'footer'      => 'Manage',
                'footer_link' => $data['route'],
                'icon'        => 'fa fa-' . $data['icon']
            ]
        );
    },
]
).'</div>';
?>

<div class="row">
    <?= '<?= $listView ?>' ?>
</div>

