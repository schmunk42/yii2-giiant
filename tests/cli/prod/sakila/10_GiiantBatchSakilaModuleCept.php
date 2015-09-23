<?php

$I = new CliTester($scenario);

// prepare output folders
$I->runShellCommand('mkdir -p /app/src/modules/sakila/controllers');
$I->runShellCommand('mkdir -p /app/src/modules/sakila/models/search');

// model & crud command
$batch = <<<'CMD'
/app/yii giiant-batch \
    --interactive=0 \
    --overwrite=1 \
    --modelDb=db \
    --modelBaseClass=yii\\db\\ActiveRecord \
    --modelNamespace=app\\\models \
    --crudControllerNamespace=app\\modules\\sakila\\controllers \
    --crudSearchModelNamespace=app\\modules\\sakila\\models\\search \
    --crudViewPath=@app/modules/sakila/views \
    --crudPathPrefix= \
    --tables=actor,film,film_actor,language,film_category,category,inventory,store,rental,payment,customer,staff,address,city,country
CMD;

$I->runShellCommand($batch);

// assertions
$I->dontSeeInShellOutput('Please fix the following errors');
$I->dontSeeInShellOutput('ErrorException');
$I->seeInShellOutput('The following files will be generated');
$I->seeFileFound('/app/src/modules/sakila/controllers/ActorController.php');