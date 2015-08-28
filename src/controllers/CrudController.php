<?php
namespace schmunk42\giiant\controllers;

/**
 * @link http://www.diemeisterei.de/
 * @copyright Copyright (c) 2015 diemeisterei GmbH, Stuttgart
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Class CrudController
 * @author Sergej Kunz <s.kunz@herzogkommunikation.de>
 */
class CrudController extends Controller {
	/**
	 * Restrict access permissions to admin user and users with auth-item 'module-controller'
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' 	=> true,
						'actions'   => ['index', 'view', 'create', 'update', 'delete'],
						'matchCallback' => function($rule, $action) {
							return
								\Yii::$app->user->can(strtr($this->module->id, ['/' => '_', '-' => '_'])) ||
								\Yii::$app->user->can(strtr($this->module->id . '/' . $this->id, ['/' => '_', '-' => '_'])) ||
								\Yii::$app->user->can(strtr($this->module->id . '/' . $this->id . '/' . $action->id, ['/' => '_', '-' => '_'])) ||
								(\Yii::$app->user->identity && \Yii::$app->user->identity->isAdmin);
						},
					]
				]
			]
		];
	}
}