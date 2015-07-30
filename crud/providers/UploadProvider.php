<?php

namespace schmunk42\giiant\crud\providers;

use yii\db\Schema;

class UploadProvider extends \schmunk42\giiant\base\Provider
{
    public function activeField($attribute)
    {
        // TODO: internationalization

        if (!isset($this->generator->getTableSchema()->columns[$attribute])) {
            return null;
        }

        $column = $this->generator->getTableSchema()->columns[$attribute];

        switch ($column->type) {
            case Schema::TYPE_STRING:
            case Schema::TYPE_TEXT:
                $fileFieldMatches = $this->generator->fileFieldMatches;

                if (!empty($fileFieldMatches)) {
                    $fileFieldMatches = explode(',', preg_replace('/\s+/', '', $fileFieldMatches));

                    if (!empty($fileFieldMatches)) {
                        $regex = "/(" . implode('|', $fileFieldMatches) . ")/mi";

                        if (preg_match($regex, $column->name)) {
                            $msg = '2amigos/yii2-file-upload-widget';

                            if (!in_array($msg, $this->generator->requires)) {
                                $this->generator->requires[] = $msg;
                            }

                            return "\$form->field(\$model, '{$column->name}')->widget(dosamigos\\fileupload\\FileUpload::className(), [
    'url' => [''],      // current requested URL. This will be passed as input to function yii\\helpers\\BaseUrl::to()
    'plus' => true,
    'options' => [
        // HTML attributes. Example:
        // 'accept' => 'image/*'
    ],
    'clientOptions' => [
        // options @ https://github.com/blueimp/jQuery-File-Upload/wiki/Options
        'maxFileSize' => 2000000    // 2 Mb
    ],
    //'clientEvents' => [
    //    // callbacks @ https://github.com/blueimp/jQuery-File-Upload/wiki/Options#callback-options
    //],
]);
";
                        }
                    }

                }




            default:
                return null;
        }
    }
}