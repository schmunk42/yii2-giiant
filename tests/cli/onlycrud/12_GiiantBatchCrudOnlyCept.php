<?php

// @group onlyCrud

$name = 'onlyCrud';

$I = new CliTester($scenario);

// prepare output folders
$I->runShellCommand("mkdir -p /app/src/controllers/{$name}");
$I->runShellCommand("mkdir -p /app/src/models/{$name}/search");

// model & crud command
$batch = <<<CMD
/app/yii giiant-batch/cruds \
    --interactive=0 \
    --overwrite=1 \
    --modelNamespace=tests\\\\models \
    --crudControllerNamespace=app\\\\controllers\\\\{$name} \
    --crudSearchModelNamespace=app\\\\models\\\\{$name}\\\\search \
    --crudViewPath=@app/views/{$name} \
    --crudPathPrefix={$name}/ \
    --crudSkipRelations=Variant,Variants \
    --crudProviders=schmunk42\\\\giiant\\\\generators\\\\crud\\\\providers\\\\extensions\\\\EditorProvider \
    --crudFixOutput=1 \
    --tables=country
CMD;

$I->runShellCommand($batch);

// assertions
$I->dontSeeInShellOutput('Please fix the following errors');
$I->dontSeeInShellOutput('ErrorException');
$I->seeInShellOutput('The following files will be generated');
$I->seeFileFound("/app/src/controllers/{$name}/CountryController.php");