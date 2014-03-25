<?php
/**
 * @link http://www.phundament.com
 * @copyright Copyright (c) 2014 herzog kommunikation GmbH
 * @license http://www.phundament.com/license/
 */

namespace schmunk42\giiant\model;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\db\Schema;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\base\NotSupportedException;

/**
 * This generator will generate one or multiple ActiveRecord classes for the specified database table.
 *
 * @author Tobias Munk <schmunk@usrbin.de>
 * @since 0.1
 */
class Generator extends \yii\gii\generators\model\Generator
{
    public $generateModelClass = false;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Giiant Model';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates an ActiveRecord class and base class for the specified database table.';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['generateModelClass'], 'boolean'],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'generateModelClass' => 'Generate Model Class',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(
            parent::hints(),
            [
                'generateModelClass' => 'This indicates whether the generator should generate the model class, this should usually be
              done only once. The model-base class is always generated.',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['model.php', 'model-extended.php'];
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files       = parent::generate();

        foreach($files AS $i => $file){
            $files[$i]->path = str_replace('.php', 'Base.php', $files[$i]->path);
            $files[$i]->id = md5($files[0]->path);
        }

        if ($this->generateModelClass) {
            foreach ($this->getTableNames() as $tableName) {
                $className   = $this->generateClassName($tableName);
                $params      = [
                    'tableName'   => $tableName,
                    'className'   => $className,
                ];
                $files[]     = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $className . '.php',
                    $this->render('model-extended.php', $params)
                );
            }
        }
        return $files;
    }
}
