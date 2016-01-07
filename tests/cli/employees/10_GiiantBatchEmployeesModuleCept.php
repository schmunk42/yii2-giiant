<?php

$I = new CliTester($scenario);

// prepare output folders
$I->runShellCommand('mkdir -p /app/src/modules/employees/controllers');
$I->runShellCommand('mkdir -p /app/src/modules/employees/models/search');

// model & crud command
$batch = <<<'CMD'
/app/yii giiant-batch \
    --interactive=0 \
    --overwrite=1 \
    --modelDb=db \
    --modelBaseClass=yii\\db\\ActiveRecord \
    --modelNamespace=app\\\models \
    --crudControllerNamespace=app\\modules\\employees\\controllers \
    --crudSearchModelNamespace=app\\modules\\employees\\models\\search \
    --crudViewPath=@app/modules/employees/views \
    --crudPathPrefix=
CMD;

$I->runShellCommand($batch);

// assertions
$I->dontSeeInShellOutput('Please fix the following errors');
$I->dontSeeInShellOutput('ErrorException');
$I->seeInShellOutput('The following files will be generated');
$I->seeFileFound('/app/src/modules/employees/controllers/DepartmentController.php');