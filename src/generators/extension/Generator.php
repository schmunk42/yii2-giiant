<?php

namespace yii\gii\generators\extension;

namespace schmunk42\giiant\generators\extension;

use Yii;
use yii\gii\CodeFile;

class Generator extends \yii\gii\generators\extension\Generator
{
    /**
     * @var string the message category used by `Yii::t()` when `$enableI18N` is `true`.
     *             Defaults to `app`
     */
    public $messageCategory = 'app';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Giiant Extension';
    }

    public function rules()
    {
        return array_merge(
                parent::rules(), [
            [['messageCategory'], 'required'],
            [['enableI18N'], 'boolean'],
            [['messageCategory'], 'validateMessageCategory', 'skipOnEmpty' => false],
                ]
        );
    }

    public function attributeLabels()
    {
        return array_merge(
                parent::attributeLabels(), [
            'messageCategory' => 'Message Category',
                ]
        );
    }

    public function hints()
    {
        return array_merge(
                parent::hints(), [
            'messageCategory' => 'This is the category used by <code>Yii::t()</code> in case you enable I18N.',
                ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function stickyAttributes()
    {
        return ['vendorName', 'outputPath', 'authorName', 'authorEmail', 'enableI18N'];
    }
    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $files = parent::generate();

        $modulePath = $this->getOutputPath();

        $packagePath = $modulePath.'/'.$this->packageName;
        if ($this->enableI18N) {
            $files[] = new CodeFile(
                    $modulePath.'/'.$this->packageName.'/messages_config.php', $this->render('messages_config.php', ['packagePath' => $packagePath])
            );
        }

        $files[] = new CodeFile(
                $modulePath.'/'.$this->packageName.'/Bootstrap.php', $this->render('Bootstrap.php')
        );

        return $files;
    }
}
