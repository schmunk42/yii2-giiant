<?php

// @group sakila

use schmunk42\giiant\tests\_pages\LoginPage;

$I = new FunctionalTester($scenario);

$I->wantTo('ensure that crud works (WITHOUT access rules applied!)');
$I->amOnPage('/en/crud/film');
$I->see('Films', 'h1');
