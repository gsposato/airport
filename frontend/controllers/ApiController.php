<?php

namespace frontend\controllers;

/**
 * Site controller
 */
class ApiController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return self::parent();
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return self::parent();
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        echo "this is the api controller.";die;
    }
}
