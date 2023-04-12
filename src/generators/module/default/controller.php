<?php
/**
 * This is the template for generating a controller class within a module.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\module\Generator */

echo "<?php\n";
?>

namespace <?= $generator->getControllerNamespace() ?>;

use dmstr\helpers\Metadata;
use rmrevin\yii\fontawesome\FA;
use yii\data\ArrayDataProvider;
use yii\web\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        $controllers = Metadata::getModuleControllers($this->module->id);
        $favourites = [];

        $patterns = [
            '^.*$' => ['color' => 'green', 'icon' => FA::_CUBE],
        ];

        foreach ($patterns as $pattern => $options) {
            foreach ($controllers as $c => $item) {
                $controllers[$c]['label'] = $item['name'];
                if ($item['name'] !== $this->id && preg_match("/$pattern/", $item['name'])) {
                    $favourites[$c] = $item;
                    $favourites[$c]['head'] = ucfirst(substr($item['name'], 0, 2));
                    $favourites[$c]['label'] = $item['name'];
                    $favourites[$c]['color'] = $options['color'];
                    $favourites[$c]['icon'] = $options['icon'] ?? null;
                    unset($controllers[$c]);
                }
            }
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $favourites,
            'pagination' => [
                'pageSize' => 100
            ]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }
}
