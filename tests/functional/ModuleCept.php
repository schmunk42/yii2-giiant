<?php

use schmunk42\giiant\tests\_pages\LoginPage;

$I = new FunctionalTester($scenario);

$I->wantTo('ensure that module works (WITHOUT access rules applied!)');
$I->amOnPage('/sakila');
$I->seeResponseCodeIs(200);
