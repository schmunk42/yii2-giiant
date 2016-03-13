<?php

/**
 * @var yii\web\View
 * @var yii\widgets\ActiveForm            $form
 * @var yii\gii\generators\crud\Generator $generator
 */

/**
 * JS for listbox "Saved Form"
 * on chenging listbox, form fill with selected saved forma data
 * currently work with input text, input checkbox and select form fields
 */
$this->registerJs($generator->getSavedFormsJs(), yii\web\View::POS_END);
$this->registerJs('
    function fillForm(id){
        if (id=="0") return;

        var formData = JSON.parse(savedForms[id]);
        
        for (var filedName in formData) {
            var fieldId = "generator-" + filedName;
            if (jQuery("#" + fieldId).is("input") || jQuery("#" + fieldId).is("select")){
                jQuery("#" + fieldId).val(formData[filedName]["value"]);
                continue;
            }    
            
            var checkboxName = "[name=\'Generator["+formData[filedName]["name"]+"][]\']";
            if(jQuery(checkboxName).is(":checkbox")){
                $(checkboxName).each(function( index ) {
                    $(this).prop("checked", false);
                    var actualValue = new String($( this ).val());
                    actualValue = actualValue + "";
                    for (var i = 0; i < formData[filedName]["value"].length; i++) {
                        var formValue = new String(formData[filedName]["value"][i]);
                        if(actualValue == formValue){
                            $(this).prop("checked", true);
                            continue;
                        }
                    }
                });                
                continue;
            }
        }    
    }
        ', yii\web\View::POS_END);
echo $form->field($generator, 'savedForm')->dropDownList(
        $generator->getSavedFormsListbox(), ['onchange' => 'fillForm(this.value)']
);

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
