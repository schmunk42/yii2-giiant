<?php

// @group sakila

$I = new CliTester($scenario);

// TODO: it should not be required to prepare output folders
$I->runShellCommand('mkdir -p /app/src/modules/sakila/controllers');
$I->runShellCommand('mkdir -p /app/src/modules/sakila/models/search');

$namespace = 'app\\\\modules\\\\sakilaEditable';

$moduleCmd = <<<CMD
yii gii/giiant-module \
    --interactive=0 \
    --overwrite=1 \
    --moduleID=sakilaEditable \
    --moduleClass={$namespace}\\\\Module
CMD;
$I->runShellCommand($moduleCmd);
$I->seeFileFound('/app/src/modules/sakilaEditable/Module.php');

// model & crud command
$batch = <<<CMD
/app/yii giiant-batch \
    --interactive=0 \
    --overwrite=1 \
    --modelDb=db \
    --modelBaseClass=yii\\\\db\\\\ActiveRecord \
    --modelNamespace={$namespace}\\\\models \
    --modelQueryNamespace={$namespace}\\\\models\\\\query \
    --crudAccessFilter=1 \
    --crudControllerNamespace={$namespace}\\\\controllers \
    --crudSearchModelNamespace={$namespace}\\\\models\\\\search \
    --crudViewPath=@app/modules/sakila/views \
    --crudPathPrefix= \
    --crudTemplate=editable \
    --tables=actor,film,film_actor,language,film_category,category,inventory,store,rental,payment,customer,staff,address,city,country
CMD;

$I->runShellCommand($batch);

// assertions
$I->dontSeeInShellOutput('Please fix the following errors');
$I->dontSeeInShellOutput('ErrorException');
$I->seeInShellOutput('The following files will be generated');
$I->seeFileFound('/app/src/modules/sakilaEditable/controllers/ActorController.php');
$I->seeFileFound('/app/src/modules/sakilaEditable/models/Actor.php');