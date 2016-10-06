<?php

namespace schmunk42\giiant\helpers;

use yii\helpers\StringHelper;

class SaveForm
{
    public static $savedFormList = false;

    public static function hint()
    {
        return ['savedForm' => 'Choose saved form ad load it data to form.'];
    }

    /**
     * get form attributes values.
     */
    public static function getFormAttributesValues($generator, $attributes)
    {
        $values = [];
        foreach ($attributes as $name) {
            $values[strtolower($name)] = [
                'value' => $generator->$name,
                'name' => $name,
            ];
        }

        return $values;
    }

    /**
     * walk througt all modules gii directories and collect Giant crud generator saved forms.
     *
     * @return array
     */
    public static function loadSavedForms($generatorName)
    {
        $suffix = str_replace(' ', '', $generatorName);

        if (self::$savedFormList) {
            return self::$savedFormList;
        }

        /*
         * get all possible gii directories with out validation on existing
         */
        $giiDirs = [];
        $giiDirs[] = \Yii::getAlias('@app/gii');
        if ($commonGiiDir = \Yii::getAlias('@common/gii', false)) {
            $giiDirs[] = $commonGiiDir;
        }
        foreach (\Yii::$app->modules as $moduleId => $module) {

            /*
             * get module base path
             */
            if (method_exists($module, 'getBasePath')) {
                $basePath = $module->getBasePath();
            } else {
                $reflector = new \ReflectionClass($module['class']);
                $basePath = StringHelper::dirname($reflector->getFileName());
            }
            $basePath .= '/gii';

            $giiDirs[] = $basePath;
        }

        /*
         * from all gii directories collec forms
         */
        $forms = [];
        foreach ($giiDirs as $basePath) {
            /*
             * search in module gii directory all forms json files
             * with required suffix
             */
            if (!file_exists($basePath)) {
                continue;
            }

            $files = scandir($basePath);
            foreach ($files as $file) {
                if (!preg_match('#'.$suffix.'\.json$#', $file)) {
                    continue;
                }
                $name = preg_replace('#'.$suffix.'\.json$#', '', $file);
                $forms[$moduleId.$name] = [
                    'jsonData' => file_get_contents($basePath.'/'.$file),
                    'label' => $moduleId.' - '.$name,
                ];
            }
        }

        return self::$savedFormList = $forms;
    }

    /**
     * get array for form field "Saved form" data.
     *
     * @return array
     */
    public static function getSavedFormsListbox($generatorName)
    {
        $r = ['0' => ' - '];
        foreach (self::loadSavedForms($generatorName) as $k => $row) {
            $r[$k] = $row['label'];
        }

        return $r;
    }

    /**
     * creata js statement for seting to variable savedFormas array with all forms and it data in json format.
     *
     * @return string
     */
    public static function getSavedFormsJs($generatorName)
    {
        $js = [];

        foreach (self::loadSavedForms($generatorName) as $k => $row) {
            $js[] = $k.":'".$row['jsonData']."'";
        }

        return 'var savedForms = {'.str_replace('\\', '\\\\', implode(',', $js)).'};';
    }

    public static function jsFillForm()
    {
        return '
    function fillForm(id){
        if (id=="0") return;

        var formData = JSON.parse(savedForms[id]);
        
        for (var filedName in formData) {
        
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
            
            var checkboxName = "[name=\'Generator["+formData[filedName]["name"]+"]\']";
            if(jQuery(checkboxName).is(":checkbox")){
                jQuery(checkboxName).prop("checked", false);
                
                $(checkboxName).each(function( index ) {
                    $(checkboxName).prop("checked", false);
                    if(formData[filedName]["value"] == 1){
                        $(checkboxName).prop("checked", true);
                    }
                });                
                continue;
            }
            
            var fieldId = "generator-" + filedName;
            if (jQuery("#" + fieldId).is("input") || jQuery("#" + fieldId).is("select")){
                jQuery("#" + fieldId).val(formData[filedName]["value"]);
                continue;
            }    
            

        }    
    }
        ';
    }
}
