<?php
/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var yii\gii\generators\crud\Generator $generator
 */

echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'searchModelClass');
echo $form->field($generator, 'controllerClass');
echo $form->field($generator, 'baseControllerClass');
echo $form->field($generator, 'viewPath');
echo $form->field($generator, 'pathPrefix');
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'indexWidgetType')->dropDownList(
    [
        'grid' => 'GridView',
        'list' => 'ListView',
    ]
);
echo $form->field($generator, 'formLayout')->dropDownList(
    [
        /* Form Types */
        'vertical'   => 'vertical',
        'horizontal' => 'horizontal',
        'inline'     => 'inline'
    ]
);
echo $form->field($generator, 'actionButtonClass')->dropDownList(
    [
        'yii\\grid\\ActionColumn'       => 'Default',
        'common\\helpers\\ActionColumn' => 'App Class',
    ]
);
echo $form->field($generator, 'providerList')->checkboxList($generator->generateProviderCheckboxListData());

?>

<div class="panel panel-default">
    <div class="panel-heading">DateTimeProvider Options</div>
    <div class="panel-body">
    <?php
    echo $form->field($generator, 'dateFormat');
    echo $form->field($generator, 'timeFormat');
    echo $form->field($generator, 'weekStart')->dropDownList(
        [
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday'
        ]
    );
    ?>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">UploadProvider Options</div>
    <div class="panel-body">
        <?php

        echo $form->field($generator, 'fileFieldMatches');

        ?>
    </div>
</div>
