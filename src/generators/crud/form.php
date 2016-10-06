<?php

use schmunk42\giiant\helpers\SaveForm;

/*
 * @var yii\web\View
 * @var yii\bootstrap\ActiveForm                   $form
 * @var schmunk42\giiant\generators\crud\Generator $generator
 */

/*
 * JS for listbox "Saved Form"
 * on chenging listbox, form fill with selected saved forma data
 * currently work with input text, input checkbox and select form fields
 */
$this->registerJs(SaveForm::getSavedFormsJs($generator->getName()), yii\web\View::POS_END);
$this->registerJs(SaveForm::jsFillForm(), yii\web\View::POS_END);
echo $form->field($generator, 'savedForm')->dropDownList(
        SaveForm::getSavedFormsListbox($generator->getName()), ['onchange' => 'fillForm(this.value)']
);

echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'searchModelClass');
echo $form->field($generator, 'controllerClass');
echo $form->field($generator, 'baseControllerClass');
echo $form->field($generator, 'viewPath');
echo $form->field($generator, 'pathPrefix');
echo $form->field($generator, 'accessFilter')->checkbox();
echo $form->field($generator, 'generateAccessFilterMigrations')->checkbox();
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
echo $form->field($generator, 'modelMessageCategory');
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
            'default' => 'full-width',
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
