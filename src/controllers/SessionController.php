<?php

namespace digitalpulsebe\editorially\controllers;

use craft\web\Controller;
use digitalpulsebe\editorially\Editorially;

class SessionController extends Controller
{
    public function actionStart(): \yii\web\Response
    {
        Editorially::getInstance()->sessions->start();

        return $this->redirectToPostedUrl();
    }

    public function actionStop(): \yii\web\Response
    {
        Editorially::getInstance()->sessions->stop();

        return $this->redirectToPostedUrl();
    }
}