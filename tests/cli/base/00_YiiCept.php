<?php

$I = new CliTester($scenario);

$I->runShellCommand('/app/yii');
$I->seeInShellOutput('This is Yii version 2.0.');
