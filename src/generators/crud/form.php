<?php
/**
 * @var yii\web\View
 * @var yii\widgets\ActiveForm            $form
 * @var yii\gii\generators\crud\Generator $generator
 */
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'searchModelClass');
echo $form->field($generator, 'controllerClass');
echo $form->field($generator, 'baseControllerClass');
echo $form->field($generator, 'viewPath');
echo $form->field($generator, 'pathPrefix');
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'singularEntities')->checkbox();
echo $form->field($generator, 'indexWidgetType')->dropDownList(
    [
        'grid' => 'GridView',
        'list' => 'ListView',
    ]
);
echo $form->field($generator, 'formLayout')->dropDownList(
    [
        /* Form Types */
        'vertical' => 'vertical',
        'horizontal' => 'horizontal',
        'inline' => 'inline',
    ]
);
echo $form->field($generator, 'actionButtonClass')->dropDownList(
    [
        'yii\\grid\\ActionColumn' => 'Default',
    ]
);
echo $form->field($generator, 'providerList')->checkboxList($generator->generateProviderCheckboxListData());
