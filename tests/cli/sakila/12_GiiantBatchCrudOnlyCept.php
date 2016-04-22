<?php

// @group sakila

$I = new CliTester($scenario);

// prepare output folders
$I->runShellCommand('mkdir -p /app/src/controllers/crud');
$I->runShellCommand('mkdir -p /app/src/models/crud/search');

// model & crud command
$batch = <<<'CMD'
/app/yii giiant-batch/cruds \
    --interactive=0 \
    --overwrite=1 \
    --modelNamespace=tests\\models \
    --crudControllerNamespace=app\\controllers\\crudonly \
    --crudSearchModelNamespace=app\\models\\crudonly\\search \
    --crudViewPath=@app/views/crudonly \
    --crudPathPrefix=crudonly/ \
    --crudSkipRelations=Variant,Variants \
    --crudProviders=schmunk42\\giiant\\generators\\crud\\providers\\optsProvider \
    --tables=country
CMD;

$I->runShellCommand($batch);

// assertions
$I->dontSeeInShellOutput('Please fix the following errors');
$I->dontSeeInShellOutput('ErrorException');
$I->seeInShellOutput('The following files will be generated');
$I->seeFileFound('/app/src/controllers/crudonly/CountryController.php');