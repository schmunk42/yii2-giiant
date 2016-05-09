<?php

// @group onlyCrud

use tests\_pages\LoginPage;

$I = new AcceptanceTester($scenario);

$I->wantTo('ensure that crudOnly works with access rules');
$I->amOnPage('/onlyCrud/country');
$I->dontSee('Actors', 'h1');
$I->makeScreenshot('crud-country-login');

$I->see('Sign in', 'h3');
$I->amGoingTo('try to login with correct credentials');

LoginPage::openBy($I);
$loginPage = LoginPage::openBy($I);
$loginPage->login('admin', 'admin');

$I->see('Actors', 'h1');
$I->makeScreenshot('crud-country');

$I->expectTo('see view, edit and delete button');
$I->seeElementInDOM('[data-key="1"] .glyphicon-eye-open');
$I->seeElementInDOM('[data-key="2"] .glyphicon-pencil');
$I->seeElementInDOM('[data-key="3"] .glyphicon-trash');