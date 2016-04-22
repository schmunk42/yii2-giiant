<?php

// @group sakila

use tests\_pages\LoginPage;

$I = new AcceptanceTester($scenario);

$I->wantTo('ensure that crud tabs remember their state');

$I->amGoingTo('try to login with correct credentials');
LoginPage::openBy($I);
$loginPage = LoginPage::openBy($I);
$loginPage->login('admin', 'admin');

$I->amGoingTo('select a tab');
$I->amOnPage('/sakila/category/view?category_id=1');
$I->see('Category', 'h1');
$I->click('Films');
$I->wait(1);

$I->click('Edit');
$I->click('View');

$I->expect('previously selected tab to be active');
$I->see('Attach Film');
$I->makeScreenshot('tab-state-success');