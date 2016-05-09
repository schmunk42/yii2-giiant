<?php

// @group sakila

use tests\_pages\LoginPage;

$I = new AcceptanceTester($scenario);

$I->wantTo('ensure that crud works with access rules');
$I->amOnPage('/sakila');
$I->dontSee('Actors', 'h1');
$I->makeScreenshot('module-login');

$I->see('Sign in', 'h3');
$I->amGoingTo('try to login with correct credentials');
LoginPage::openBy($I);
$loginPage = LoginPage::openBy($I);
$loginPage->login('admin', 'admin');


$I->wantTo('ensure that module works');
$I->amOnPage('/sakila');
$I->see('rental');
$I->makeScreenshot('module-index');
