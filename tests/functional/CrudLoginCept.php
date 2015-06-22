<?php

use schmunk42\giiant\tests\_pages\LoginPage;

$I = new FunctionalTester($scenario);

$I->wantTo('ensure that crud works with access rules');
$I->amOnPage('/crud/actor');
$I->dontSee('Actor', 'h2');

$I->see('Sign in', 'h3');
$I->amGoingTo('try to login with correct credentials');

// TODO: use LoginPage class
$username = 'admin';
$password = 'admin';
$I->fillField('input[name="login-form[login]"]', $username);
$I->fillField('input[name="login-form[password]"]', $password);
$I->click('Sign in');

$I->see('Actor', 'h2');
