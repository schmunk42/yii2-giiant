<?php

namespace schmunk42\giiant\generators\module;

use yii\gii\CodeFile;
use yii\helpers\StringHelper;

/**
 * @link http://www.diemeisterei.de/
 *
 * @copyright Copyright (c) 2015 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */
class Generator extends \yii\gii\generators\module\Generator
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Giiant Module';
    }

    /**
     * {@inheritdoc}
     */
    public function requiredTemplates()
    {
        return ['module.php', 'controller.php', 'view.php'];
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $files = [];
        $modulePath = $this->getModulePath();
        $files[] = new CodeFile(
            $modulePath.'/'.StringHelper::basename($this->moduleClass).'.php',
            $this->render('module.php')
        );
        $files[] = new CodeFile(
            $modulePath.'/controllers/DefaultController.php',
            $this->render('controller.php')
        );
        $files[] = new CodeFile(
            $modulePath.'/views/default/index.php',
            $this->render('view.php')
        );
        $files[] = new CodeFile(
            $modulePath.'/traits/ActiveRecordDbConnectionTrait.php',
            $this->render('db-connection-trait.php')
        );

        return $files;
    }

    /**
     * @return string the controller namespace of the module
     */
    public function getTraitsNamespace()
    {
        return substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\')).'\traits';
    }
}
