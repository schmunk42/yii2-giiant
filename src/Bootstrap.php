<?php
/**
 * @link http://www.diemeisterei.de/
 *
 * @copyright Copyright (c) 2014 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */
namespace schmunk42\giiant;

use yii\base\Application;
use yii\base\BootstrapInterface;

/**
 * Class Bootstrap.
 *
 * @author Tobias Munk <tobias@diemeisterei.de>
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * Bootstrap method to be called during application bootstrap stage.
     *
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        if ($app->hasModule('gii')) {
            if (!isset($app->getModule('gii')->generators['giiant-model'])) {
                $app->getModule('gii')->generators['giiant-model'] = 'schmunk42\giiant\generators\model\Generator';
            }

            if (!isset($app->getModule('gii')->generators['giiant-extension'])) {
                $app->getModule('gii')->generators['giiant-extension'] = 'schmunk42\giiant\generators\extension\Generator';
            }

            if (!isset($app->getModule('gii')->generators['giiant-crud'])) {
                $app->getModule('gii')->generators['giiant-crud'] = [
                    'class' => 'schmunk42\giiant\generators\crud\Generator',
                    'templates' => [
                        'editable' => __DIR__.'/generators/crud/editable',
                    ],
                ];
            }

            if (!isset($app->getModule('gii')->generators['giiant-module'])) {
                $app->getModule('gii')->generators['giiant-module'] = 'schmunk42\giiant\generators\module\Generator';
            }

            if (!isset($app->getModule('gii')->generators['giiant-test'])) {
                $app->getModule('gii')->generators['giiant-test'] = 'schmunk42\giiant\generators\test\Generator';
            }

            if ($app instanceof \yii\console\Application) {
                $app->controllerMap['giiant-batch'] = 'schmunk42\giiant\commands\BatchController';
            }
        }
    }
}
