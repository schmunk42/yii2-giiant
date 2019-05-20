<?php

// @group sakila

use tests\_pages\LoginPage;

$I = new AcceptanceTester($scenario);

$I->wantTo('ensure that crud works with access rules');
$I->comment('editor configured in test application');

$I->amOnPage('/sakila/actor');

$I->expectTo('see view, edit and delete button');
$I->seeElementInDOM('[data-key="1"] .glyphicon-eye-open');
$I->seeElementInDOM('[data-key="2"] .glyphicon-pencil');
$I->seeElementInDOM('[data-key="3"] .glyphicon-trash');