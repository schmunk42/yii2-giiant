<?php

$I = new CliTester($scenario);

// prepare output folders
$I->runShellCommand('mkdir -p /app/src/modules/crud/controllers');
$I->runShellCommand('mkdir -p /app/src/modules/crud/models/search');

// model & crud command
$batch = <<<'CMD'
/app/yii giiant-batch \
    --interactive=0 \
    --overwrite=1 \
    --modelDb=db \
    --modelBaseClass=yii\\db\\ActiveRecord \
    --modelNamespace=app\\\models \
    --crudControllerNamespace=app\\modules\\crud\\controllers \
    --crudSearchModelNamespace=app\\modules\\crud\\models\\search \
    --crudViewPath=@app/modules/crud/views \
    --crudPathPrefix= \
    --crudSkipRelations=Variant,Variants \
    --crudProviders=schmunk42\\giiant\\crud\\providers\\optsProvider \
    --tables=actor,film,film_actor,language,film_category,category,inventory,store,rental,payment,customer,staff,address,city,country
CMD;

$I->runShellCommand($batch);

// assertions
$I->dontSeeInShellOutput('Please fix the following errors');
$I->dontSeeInShellOutput('ErrorException');
$I->seeInShellOutput('The following files will be generated');
$I->seeFileFound('/app/src/modules/crud/controllers/ActorController.php');