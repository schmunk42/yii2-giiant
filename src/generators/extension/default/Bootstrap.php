<?= "<?php\n" ?>

namespace <?= substr($generator->namespace, 0, -1) ?>;

use Yii;
use yii\base\BootstrapInterface;
use yii\i18n\PhpMessageSource;

class Bootstrap implements BootstrapInterface {

    /** @inheritdoc */
    public function bootstrap($app) {

<?php 
if($generator->enableI18N){
?>        
        if (!isset($app->get('i18n')->translations['<?= $generator->messageCategory ?>*'])) {
            $app->get('i18n')->translations['<?= $generator->messageCategory ?>*'] = [
                'class' => PhpMessageSource::className(),
                'basePath' => __DIR__ . '/messages',
                'sourceLanguage' => 'en-US'
            ];
        }
<?php
}
?>        
    }

}
