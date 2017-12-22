<?php

// @group sakila

use tests\_pages\LoginPage;

$I = new AcceptanceTester($scenario);

$I->wantTo('ensure that crud works with access rules');
$I->comment('editor configured in test application');

$I->amOnPage('/sakila/store');
$I->dontSee('Store', 'h1');
$I->makeScreenshot('module-access-denied');

$I->wantTo('ensure that module works');
$I->amOnPage('/sakila');
$I->see('rental');
$I->makeScreenshot('module-index');
