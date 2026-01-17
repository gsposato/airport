<?php

namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;
use Yii;

class HomeCest
{
    public function checkOpen(FunctionalTester $I)
    {
        $I->amOnRoute(\Yii::$app->homeUrl);
        $I->see(Yii::$app->name);
        $I->seeLink('About');
        $I->click('About');
        $I->see('About');
    }
}
