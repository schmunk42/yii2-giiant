<?php
namespace schmunk42\giiant\components\grid;

/**
 * @link http://www.diemeisterei.de/
 * @copyright Copyright (c) 2015 diemeisterei GmbH, Stuttgart
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use dmstr\helpers\Html;
use Yii;

/**
 * Class ActionColumn
 * @author Sergej Kunz <s.kunz@herzogkommunikation.de>
 */
class ActionColumn extends \yii\grid\ActionColumn
{
	/**
	 * Initializes the default button rendering callbacks.
	 */
	protected function initDefaultButtons()
	{
		if (!isset($this->buttons['view'])) {
			$this->buttons['view'] = function ($url, $model, $key) {
				$options = array_merge([
					'title' => Yii::t('yii', 'View'),
					'aria-label' => Yii::t('yii', 'View'),
					'data-pjax' => '0',
				], $this->buttonOptions);

				return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, $options);
			};
		}

		if (!isset($this->buttons['update'])) {
			$this->buttons['update'] = function ($url, $model, $key) {
				$options = array_merge([
					'title' => Yii::t('yii', 'Update'),
					'aria-label' => Yii::t('yii', 'Update'),
					'data-pjax' => '0',
				], $this->buttonOptions);

				return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
			};
		}

		if (!isset($this->buttons['delete'])) {
			$this->buttons['delete'] = function ($url, $model, $key) {
				$options = array_merge([
					'title' => Yii::t('yii', 'Delete'),
					'aria-label' => Yii::t('yii', 'Delete'),
					'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
					'data-method' => 'post',
					'data-pjax' => '0',
				], $this->buttonOptions);

				return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
			};
		}
	}
}