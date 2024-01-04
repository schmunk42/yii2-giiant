<?php
/**
 * Customizable controller class.
 */

use yii\helpers\StringHelper;

$modelClass = StringHelper::basename($generator->modelClass);
echo "<?php\n";
?>

namespace <?= $generator->controllerNs ?>\api;

/**
 * This is the class for REST controller "<?= $controllerClassName ?>".
 */

use <?= $generator->modelClass ?>;
<?php if ($generator->accessFilter): ?>
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
<?php endif; ?>
use yii\rest\ActiveController;
<?php if ($generator->accessFilter): ?>
use Yii;
<?php endif; ?>

class <?= $controllerClassName ?> extends ActiveController
{
    public $modelClass = <?= $modelClass ?>::class;
<?php if ($generator->accessFilter): ?>

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'matchCallback' => function ($rule, $action) {
                                return Yii::$app->user->can($this->module->id . '_' . $this->id . '_' . $action->id, ['route' => true]);
                            }
                        ]
                    ]
                ]
            ]
        );
    }
<?php endif; ?>
}
