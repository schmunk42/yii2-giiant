<?php

namespace tests\codeception\unit\models;

use Codeception\Specify;
use schmunk42\giiant\generators\model\Generator as ModelGenerator;
use Yii;
use yii\codeception\TestCase;

class BasicTest extends TestCase
{
    use Specify;

    public $appConfig = '@tests/_config/unit.php';

    protected function setUp()
    {
        $this->appConfig = Yii::getAlias($this->appConfig);
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testPrefixesGenerator()
    {
        $generator            = new ModelGenerator();
        $generator->template  = 'default';
        $generator->tableName = 'sakila.*';
        $files                = $generator->generate();
        if (version_compare(str_replace('-dev', '', Yii::getVersion()), '2.0.4', '<')) {
            $this->markTestSkipped('This feature is only available since Yii 2.0.4.');
        }

        # TODO: review created files
        #$this->assertEquals(51, count($files));
        #$this->assertEquals("Actor", basename($files[0]->path, '.php'));
        #$this->assertEquals("ActorInfo", basename($files[1]->path, '.php'));
    }

}
