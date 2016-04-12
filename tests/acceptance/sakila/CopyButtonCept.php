<?php

// @group sakila

use tests\_pages\LoginPage;

$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that copy button works');

LoginPage::openBy($I);
$loginPage = LoginPage::openBy($I);
$loginPage->login('admin', 'admin');

$I->amOnPage('/sakila/actor/view?actor_id=1');
$I->see('1', 'h1');
$I->makeScreenshot('detail-view');

$I->click('Copy');
$I->seeInField('#actor-first_name', 'PENELOPE');