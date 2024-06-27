<?php

namespace app\controllers;

use app\forms\LoginForm;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class AuthController extends Controller
{
    public function actionLogin(): Response
    {
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post())) {
            return $this->asJson($model->login());
        } else {
            return $this->asJson([

            ]);
        }
    }

}