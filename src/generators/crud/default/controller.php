<?php

use yii\helpers\StringHelper;

/**
 * This is the template for generating a CRUD controller class file.
 *
 * @var yii\web\View $this
 * @var schmunk42\giiant\generators\crud\Generator $generator
 * @var array $accessDefinitions
 */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
$searchModelClassName = $searchModelClass;
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass.'Search';
    $searchModelClassName = $searchModelAlias;
}

// TODO: improve detetction of NOSQL primary keys
if ($generator->getTableSchema()) {
    $pks = $generator->getTableSchema()->primaryKey;
} else {
    $pks = ['_id'];
}

$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();
echo "<?php\n";
?>
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>\base;

use <?= ltrim($generator->modelClass, '\\') ?>;
<?php if ($searchModelClass !== ''): ?>
use <?= ltrim(
        $generator->searchModelClass,
        '\\'
    ) ?><?php if (isset($searchModelAlias)): ?> as <?= $searchModelAlias ?><?php endif ?>;
<?php endif; ?>
<?php if ($generator->accessFilter): ?>
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
<?php endif; ?>
use yii\base\InvalidConfigException;
use yii\helpers\Url;
use <?= ltrim($generator->baseControllerClass, '\\') ?>;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use Yii;

/**
 * <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
 *
 * @property-read Request $request
 */
class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass)."\n" ?>
{
<?php
$traits = $generator->baseTraits;
if ($traits) {
    echo "use {$traits};";
}

?>

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
                <?php foreach($accessDefinitions['roles'] as $roleName => $actions): ?>
        [
                            'allow' => true,
                            'actions' => ['<?=implode("', '",$actions)?>'],
                            'roles' => ['<?=$roleName?>']
                        ],
                <?php endforeach; ?>
    ]
                ]
            ]);
    }
<?php endif; ?>

    /**
     * Lists all <?= $modelClass ?> models.
     *
     * @throws InvalidConfigException
     * @return string
     */
    public function actionIndex()
    {
    <?php if ($searchModelClass !== '') {
        ?>
    $searchModel = Yii::createObject(<?php echo $searchModelClassName ?>::class);
        $dataProvider = $searchModel->search($this->request->get());
    <?php
    } else {
        ?>
        $dataProvider = new ActiveDataProvider([
        'query' => <?= $modelClass ?>::find(),
        ]);
    <?php
    } ?>

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        <?php if ($searchModelClass !== ''): ?>
    'searchModel' => $searchModel,
        <?php endif; ?>
]);
    }

    /**
     * Displays a single <?= $modelClass ?> model.
     *
     * <?= implode("\n\t * ", $actionParamComments)."\n" ?>
     *
     * @throws NotFoundHttpException
     * @return string
     */
    public function actionView(<?= $actionParams ?>)
    {
        return $this->render('view', ['model' => $this->findModel(<?= $actionParams ?>)]);

    }

    /**
     * Creates a new <?= $modelClass ?> model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @throws yii\base\InvalidConfigException
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = Yii::createObject(<?php echo $modelClass ?>::class);
        try {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', <?= $urlParams ?>]);
            }
            if (!Yii::$app->request->isPost) {
                $model->load($this->request->get());
            }
        } catch (\Exception $e) {
            $model->addError('_exception', $e->errorInfo[2] ?? $e->getMessage());
        }
        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing <?= $modelClass ?> model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * <?= implode("\n\t * ", $actionParamComments)."\n" ?>
     *
     * @throws NotFoundHttpException
     * @return string|Response
     */
    public function actionUpdate(<?= $actionParams ?>)
    {
        $model = $this->findModel(<?= $actionParams ?>);
        if ($model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', <?= $urlParams ?>]);
        }
        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes an existing <?= $modelClass ?> model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * <?= implode("\n\t * ", $actionParamComments)."\n" ?>
     *
     * @throws \Throwable
     * @return Response
     */
    public function actionDelete(<?= $actionParams ?>)
    {
        try {
            $this->findModel(<?= $actionParams ?>)->delete();
        } catch (\Exception $e) {
            Yii::$app->getSession()->addFlash('error', $e->errorInfo[2] ?? $e->getMessage());
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the <?= $modelClass ?> model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * <?= implode("\n\t * ", $actionParamComments)."\n" ?>
     *
     * @throws NotFoundHttpException if the model cannot be found
     * @return <?= $modelClass ?> the loaded model
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
        $condition = '['.implode(', ', $condition).']';
    }
    ?>
    $model = <?= $modelClass ?>::findOne(<?= $condition ?>);
        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException(<?= $generator->generateString('The requested page does not exist.')?>);
    }
}
