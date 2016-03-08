<?php

// @group sakila

use schmunk42\giiant\tests\_pages\LoginPage;

$I = new AcceptanceTester($scenario);

$I->wantTo('ensure that module works');
$I->amOnPage('/sakila');
$I->see('rental');
