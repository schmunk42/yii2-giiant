<?php

// @group sakila

$I = new CliTester($scenario);

// TODO: it should not be required to prepare output folders
$I->runShellCommand('mkdir -p /app/src/modules/sakila/controllers');
$I->runShellCommand('mkdir -p /app/src/modules/sakila/models/search');

$moduleCmd = <<<'CMD'
yii gii/giiant-module \
    --interactive=0 \
    --overwrite=1 \
    --moduleID=sakila \
    --moduleClass=app\\modules\\sakila\\Module
CMD;
$I->runShellCommand($moduleCmd);
$I->seeFileFound('/app/src/modules/sakila/Module.php');

// model & crud command
$batch = <<<'CMD'
/app/yii giiant-batch \
    --interactive=0 \
    --overwrite=1 \
    --modelDb=db \
    --modelBaseClass=yii\\db\\ActiveRecord \
    --modelNamespace=app\\modules\\sakila\\models \
    --modelQueryNamespace=app\\modules\\sakila\\models\\query \
    --crudAccessFilter=1 \
    --crudControllerNamespace=app\\modules\\sakila\\controllers \
    --crudSearchModelNamespace=app\\modules\\sakila\\models\\search \
    --crudViewPath=@app/modules/sakila/views \
    --crudPathPrefix= \
    --crudSkipRelations=Variant,Variants \
    --crudProviders=schmunk42\\giiant\\generators\\crud\\providers\\extensions\\EditorProvider \
    --tables=actor,film,film_actor,language,film_category,category,inventory,store,rental,payment,customer,staff,address,city,country
CMD;

$I->runShellCommand($batch);

// assertions
$I->dontSeeInShellOutput('Please fix the following errors');
$I->dontSeeInShellOutput('ErrorException');
$I->seeInShellOutput('The following files will be generated');
$I->seeFileFound('/app/src/modules/sakila/controllers/ActorController.php');
$I->seeFileFound('/app/src/modules/sakila/models/Actor.php');