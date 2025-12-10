<?php

namespace schmunk42\giiant\generators\config;

use Yii;
use yii\gii\CodeFile;

class Generator extends \yii\gii\Generator
{

    /**
     * Namespace in which the config should be generated
     */
    public $ns = 'project\modules\crud';

    /**
     * Path where the config file should be generated
     */
    public $savePath = '/app/project/modules/crud/config';

    /**
     * Path where the config file should be generated
     */
    public $filename = 'giiant.php';

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Giiant Config';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates a basic configuration file.';
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        return [
            new CodeFile($this->getFilePath(), $this->render('config.php', [
                'levels' => $this->getBaseConfigParentDirectoryCount()
            ]))
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [
            'ns',
            'filter',
            'filter' => static function ($value) {
                return trim($value, ' \\');
            },
            'skipOnArray' => true,
            'skipOnEmpty' => true
        ];
        $rules[] = [
            'savePath',
            'filter',
            'filter' => static function ($value) {
                return DIRECTORY_SEPARATOR . trim($value, ' ' . DIRECTORY_SEPARATOR);
            },
            'skipOnArray' => true,
            'skipOnEmpty' => true
        ];
        $rules[] = [
            'messageCategory',
            'filter',
            'filter' => static function ($value) {
                return trim($value);
            },
            'skipOnArray' => true,
            'skipOnEmpty' => true
        ];
        $rules[] = [
            ['ns', 'savePath', 'filename'],
            'required'
        ];
        $rules[] = [
            'ns',
            'validateNamespace'
        ];
        $rules[] = [
            'filename',
            'match',
            'pattern' => '/\.php$/',
            'message' => 'Only files with the ending .php are allowed.',
        ];
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $attributeLabels = parent::attributeLabels();
        $attributeLabels['ns'] = 'Namespace';
        $attributeLabels['savePath'] = 'Save Path';
        $attributeLabels['filename'] = 'Filename';
        $attributeLabels['messageCategory'] = 'Message Category';
        return $attributeLabels;
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        $hints = parent::hints();
        $hints['ns'] = 'This is the namespace of the config file to be generated, e.g., <code>app\models</code>';
        $hints['savePath'] = 'Path where the config file will be saved';
        $hints['filename'] = 'This is the filename of the php config file to be generated, e.g., <code>giiant.php</code>. Must end with .php';
        return $hints;
    }

    /**
     * @inheritdoc
     */
    public function stickyAttributes()
    {
        $stickyAttributes = parent::stickyAttributes();
        $stickyAttributes[] = 'ns';
        $stickyAttributes[] = 'savePath';
        $stickyAttributes[] = 'filename';
        return $stickyAttributes;
    }

    /**
     * @see \yii\gii\generators\model\Generator::validateNamespace()
     */
    public function validateNamespace($attribute)
    {
        $value = $this->$attribute;
        $value = ltrim($value, '\\');
        $path = Yii::getAlias('@' . str_replace('\\', '/', $value), false);
        if ($path === false) {
            $this->addError($attribute, 'Namespace must be associated with an existing directory.');
        }
    }

    protected function getFilePath()
    {
        return $this->savePath . DIRECTORY_SEPARATOR . $this->filename;
    }

    /**
     * The number of parent directories to go up. This must be an integer greater than 0.
     */
    protected function getBaseConfigParentDirectoryCount(): int
    {
        return count(explode(DIRECTORY_SEPARATOR, trim($this->savePath, DIRECTORY_SEPARATOR)));
    }
}
