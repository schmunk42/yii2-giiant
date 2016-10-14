<?php

// @group sakila

use tests\_pages\LoginPage;

$I = new AcceptanceTester($scenario);

$I->wantTo('ensure that crud works with access rules');
$I->amOnPage('/sakila/actor');
$I->dontSee('Actors', 'h1');
$I->makeScreenshot('crud-actor-login');

$I->see('Sign in', 'h3');
$I->amGoingTo('try to login with correct credentials');

LoginPage::openBy($I);
$loginPage = LoginPage::openBy($I);
$loginPage->login('admin', 'admin');

$I->see('Actors', 'h1');
$I->makeScreenshot('crud-actor');

$I->expectTo('see view, edit and delete button');
$I->seeElementInDOM('[data-key="1"] .glyphicon-file');
$I->seeElementInDOM('[data-key="2"] .glyphicon-pencil');
$I->seeElementInDOM('[data-key="3"] .glyphicon-trash');