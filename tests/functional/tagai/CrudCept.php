<?php

use schmunk42\giiant\tests\_pages\LoginPage;

$I = new FunctionalTester($scenario);

$I->wantTo('ensure that crud works (WITHOUT access rules applied!)');
$I->amOnPage('/tagai/tagai');
$I->see('Tagais', 'h1');
