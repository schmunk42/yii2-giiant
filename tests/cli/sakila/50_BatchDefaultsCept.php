<?php

// @group sakila

$I = new CliTester($scenario);

// TODO: it should not be required to prepare output folders
$I->runShellCommand('mkdir -p /app/src/modules/backend/controllers/crud');
$I->runShellCommand('mkdir -p /app/src/common/backend/models/search');

$moduleCmd = <<<'CMD'
yii gii/giiant-module \
    --interactive=0 \
    --overwrite=1 \
    --moduleID=backend2 \
    --moduleClass=app\\modules\\backend\\Module
CMD;
$I->runShellCommand($moduleCmd);
$I->seeFileFound('/app/src/modules/sakila/Module.php');

// model & crud command
$batch = <<<'CMD'
/app/yii giiant-batch \
    --interactive=0 \
    --overwrite=1 \
    --modelQueryNamespace=app\\modules\\backend\\models\\query \
    --tables=actor,film,film_actor,language,film_category,category,inventory,store,rental,payment,customer,staff,address,city,country
CMD;

$I->runShellCommand($batch);

// assertions
$I->dontSeeInShellOutput('Please fix the following errors');
$I->dontSeeInShellOutput('ErrorException');
$I->seeInShellOutput('The following files will be generated');
$I->seeFileFound('/app/src/modules/sakila/controllers/ActorController.php');
$I->seeFileFound('/app/src/modules/sakila/models/Actor.php');