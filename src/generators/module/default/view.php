<?php
/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\module\Generator */
?>

<?= '<?php' ?>

/**
 * @var ArrayDataProvider $dataProvider
 */

use insolita\wgadminlte\SmallBox;
use yii\data\ArrayDataProvider;
use yii\widgets\ListView;

echo ListView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "{items}\n{pager}",
    'options' => ['class' => 'row'],
    'itemOptions' => ['class' => 'col-xs-col-xs-6 col-sm-4 col-lg-3'],
    'itemView' => function ($data) {
        return SmallBox::widget([
            'head' => $data['head'],
            'type' => $data['color'],
            'text' => $data['label'],
            'footer' => 'Manage',
            'footer_link' => $data['route'],
            'icon' => 'fa fa-' . $data['icon']
        ]);
    },
    'emptyTextOptions' => ['class' => 'col-xs-12']
]);
