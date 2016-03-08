<?php

// @group sakila

use tests\_pages\LoginPage;

#new schmunk42\giiant\tests\_pages\LoginPage;

$I = new AcceptanceTester($scenario);

$I->wantTo('ensure that crud works with access rules');
$I->amOnPage('/sakila/actor');
$I->dontSee('Actors', 'h1');
$I->makeScreenshot('crud-actor-login');

$I->see('Sign in', 'h3');
$I->amGoingTo('try to login with correct credentials');

// TODO: use LoginPage
$username = 'admin';
$password = 'admin';
$I->fillField('input[name="login-form[login]"]', $username);
$I->fillField('input[name="login-form[password]"]', $password);
$I->click('Sign in');
$I->waitForElement('.user-footer .pull-right a', 5);
#LoginPage::openBy($I);
#$loginPage = LoginPage::openBy($I);
#$loginPage->login('admin', 'admin');

$I->see('Actors', 'h1');
$I->makeScreenshot('crud-actor');