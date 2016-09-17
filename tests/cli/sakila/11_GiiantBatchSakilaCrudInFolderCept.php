<?php

// @group sakila

$I = new CliTester($scenario);

// prepare output folders
$I->runShellCommand('mkdir -p /app/src/controllers/crud');
$I->runShellCommand('mkdir -p /app/src/models/crud/search');

// model & crud command
$batch = <<<'CMD'
/app/yii giiant-batch \
    --interactive=0 \
    --overwrite=1 \
    --modelDb=db \
    --modelBaseClass=yii\\db\\ActiveRecord \
    --modelNamespace=app\\\models \
    --crudControllerNamespace=app\\controllers\\crud \
    --crudSearchModelNamespace=app\\models\\crud\\search \
    --crudViewPath=@app/views/crud \
    --crudPathPrefix=crud/ \
    --crudSkipRelations=Variant,Variants \
    --crudProviders=schmunk42\\giiant\\generators\\crud\\providers\\extensions\\EditorProvider \
    --tables=actor,film,film_actor,language,film_category,category,inventory,store,rental,payment,customer,staff,address,city,country
CMD;

$I->runShellCommand($batch);

// assertions
$I->dontSeeInShellOutput('Please fix the following errors');
$I->dontSeeInShellOutput('ErrorException');
$I->seeInShellOutput('The following files will be generated');
$I->seeFileFound('/app/src/controllers/crud/CountryController.php');