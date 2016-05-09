<?php

// @group onlyCrud

use tests\_pages\LoginPage;

$I = new AcceptanceTester($scenario);

$I->wantTo('ensure that crudOnly works (no access rules)');

$I->amOnPage('/onlyCrud/country');
$I->see('Countries', 'h1');
$I->makeScreenshot('crud-country');

$I->expectTo('see view, edit and delete button');
$I->seeElementInDOM('[data-key="1"] .glyphicon-eye-open');
$I->seeElementInDOM('[data-key="2"] .glyphicon-pencil');
$I->seeElementInDOM('[data-key="3"] .glyphicon-trash');

$I->amGoingTo('create a Country');
$I->click('New');

$I->see('Country', 'h1');
$I->see('Create', '.btn');

$I->fillField('#country-country', 'Xyzland');
$I->makeScreenshot('crud-pre-create-country');
$I->click('Create');
$I->wait(1);

$I->see('Xyzland', 'table');
$I->see('Country', 'table');
$I->makeScreenshot('crud-create-country');


$I->click('Edit');
$I->wait(1);

$I->seeInField('#country-country', 'Xyzland');
$I->see('Country', 'h1');
$I->fillField('#country-country', 'Abcstan');
$I->click('Save');
$I->wait(1);

$I->see('Abcstan', 'table');
$I->makeScreenshot('crud-edit-country');
