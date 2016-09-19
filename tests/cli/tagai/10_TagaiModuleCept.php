<?php

$I = new CliTester($scenario);

$moduleCmd = <<<'CMD'
yii gii/giiant-module \
    --interactive=0 \
    --overwrite=1 \
    --moduleID=tagai \
    --moduleClass=app\\modules\\tagai\\Module
CMD;
$I->runShellCommand($moduleCmd);

$I->seeFileFound('/app/src/modules/tagai/Module.php');

// prepare output folders
$I->runShellCommand('mkdir -p /app/src/modules/tagai/controllers');
$I->runShellCommand('mkdir -p /app/src/modules/tagai/models/search');

// model & crud command
$batch = <<<'CMD'
/app/yii giiant-batch \
    --interactive=0 \
    --overwrite=1 \
    --modelDb=db \
    --modelBaseClass=yii\\db\\ActiveRecord \
    --modelNamespace=app\\modules\\tagai\\models \
    --modelRemoveDuplicateRelations=1 \
    --crudTidyOutput=1 \
    --crudAccessFilter=1 \
    --crudControllerNamespace=app\\modules\\tagai\\controllers \
    --crudSearchModelNamespace=app\\modules\\tagai\\models\\search \
    --crudViewPath=@app/modules/tagai/views \
    --crudPathPrefix= \
    --crudSkipRelations=Variant,Variants \
    --crudProviders=schmunk42\\giiant\\generators\\crud\\providers\\extensions\\EditorProvider \
    --tables=klientai,tagai
CMD;

$I->runShellCommand($batch);

// assertions
$I->dontSeeInShellOutput('Please fix the following errors');
$I->dontSeeInShellOutput('ErrorException');
$I->seeInShellOutput('The following files will be generated');
$I->seeFileFound('/app/src/modules/tagai/controllers/KlientaiController.php');