<?php

use yii\helpers\StringHelper;

/**
 * This is the template for generating a CRUD controller class file.
 *
 * @var yii\web\View $this
 * @var schmunk42\giiant\crud\Generator $generator
 */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
	$searchModelAlias = $searchModelClass.'Search';
}

$pks = $generator->getTableSchema()->primaryKey;
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use <?= ltrim($generator->modelClass, '\\') ?>;
use <?= ltrim($generator->searchModelClass, '\\') ?><?php if (isset($searchModelAlias)):?> as <?= $searchModelAlias ?><?php endif ?>;
use <?= ltrim($generator->baseControllerClass, '\\') ?>;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;
use dmstr\bootstrap\Tabs;

/**
 * <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
 */
class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
{
    /**
     * @var boolean whether to enable CSRF validation for the actions in this controller.
     * CSRF validation is enabled only when both this property and [[Request::enableCsrfValidation]] are true.
     */
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            Tabs::registerAssets();
            return true;
        } else {
            return false;
        }
    }

	/**
	 * Lists all <?= $modelClass ?> models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel  = new <?= isset($searchModelAlias) ? $searchModelAlias : $searchModelClass ?>;
		$dataProvider = $searchModel->search($_GET);

        Url::remember();

        // clear all parent route information in cookies
        Tabs::clearParentRelationRoute($this->id);

		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'searchModel' => $searchModel,
		]);
	}

	/**
	 * Displays a single <?= $modelClass ?> model.
	 * <?= implode("\n\t * ", $actionParamComments) . "\n" ?>
     * @param null $returnUrl
     *
	 * @return mixed
	 */
	public function actionView(<?= $actionParams ?>, $returnUrl = null)
	{
        Tabs::rememberActiveTab(\Yii::$app->request->url, $this->id);

        if ($returnUrl === null) {
			$returnUrl = ($this->module->id)
				? $this->module->id . '/' . $this->id . '/' . $this->action->id
				: $this->id . '/' . $this->action->id;
            $returnUrl = \Yii::$app->urlManager->createUrl([$returnUrl, '<?= str_replace('$', '',$actionParams) ?>' => <?= $actionParams ?>]);
        }
        Url::remember($returnUrl);

        return $this->render('view', [
			'model' => $this->findModel(<?= $actionParams ?>),
		]);
	}

	/**
	 * Creates a new <?= $modelClass ?> model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate()
	{
		$model = new <?= $modelClass ?>;

		try {
            if ($model->load($_POST) && $model->save()) {
                return $this->redirect(Url::previous());
            } elseif (!\Yii::$app->request->isPost) {
                $model->load($_GET);
            }
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2]))?$e->errorInfo[2]:$e->getMessage();
            $model->addError('_exception', $msg);
		}
        return $this->render('create', ['model' => $model]);
	}

	/**
	 * Updates an existing <?= $modelClass ?> model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * <?= implode("\n\t * ", $actionParamComments) . "\n" ?>
	 * @param null $returnUrl
	 * @return mixed
	 */
	public function actionUpdate(<?= $actionParams ?>, $returnUrl = null)
	{
		$model = $this->findModel(<?= $actionParams ?>);

        if ($returnUrl === null)
        {
            $returnUrl = ($this->module->id)
                ? $this->module->id . '/' . $this->id . '/view'
                : $this->id . '/view';
			$returnUrl = \Yii::$app->urlManager->createUrl([$returnUrl, '<?= str_replace('$', '', $actionParams) ?>' => <?= $actionParams ?>]);
        }

		if ($model->load($_POST) && $model->save()) {
            return $this->redirect($returnUrl);
		} else {
			return $this->render('update', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Deletes an existing <?= $modelClass ?> model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * <?= implode("\n\t * ", $actionParamComments) . "\n" ?>
     * @param null $returnUrl
	 * @return mixed
	 */
	public function actionDelete(<?= $actionParams ?>, $returnUrl = null)
	{
        try {
            $this->findModel(<?= $actionParams ?>)->delete();
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2]))?$e->errorInfo[2]:$e->getMessage();
            \Yii::$app->getSession()->setFlash('error', $msg);
            return $this->redirect(Url::previous());
        }
        if ($returnUrl === null)
        {
            $returnUrl = ($this->module->id) ? $this->module->id . '/' . $this->id : $this->id;
            return $this->redirect(\Yii::$app->urlManager->createUrl($returnUrl));
        }
		return $this->redirect(Url::previous());
	}

	/**
	 * Finds the <?= $modelClass ?> model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * <?= implode("\n\t * ", $actionParamComments) . "\n" ?>
	 * @return <?= $modelClass ?> the loaded model
	 * @throws HttpException if the model cannot be found
	 */
	protected function findModel(<?= $actionParams ?>)
	{
<?php
if (count($pks) === 1) {
	$condition = '$'.$pks[0];
} else {
	$condition = [];
	foreach ($pks as $pk) {
		$condition[] = "'$pk' => \$$pk";
	}
	$condition = '[' . implode(', ', $condition) . ']';
}
?>
		if (($model = <?= $modelClass ?>::findOne(<?= $condition ?>)) !== null) {
			return $model;
		} else {
			throw new HttpException(404, 'The requested page does not exist.');
		}
	}
}
