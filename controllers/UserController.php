<?php

namespace app\controllers;

use app\base\BaseController;
use app\forms\UserForm;
use Yii;

class UserController extends BaseController
{

    public function actionDelete()
    {
        if (Yii::$app->request->isDelete) {
            $model = new UserForm();
            if ($model->load(Yii::$app->request->post())) {
                return $this->asJson($model->deleteUser());
            }
        }
        return $this->asJson([
            'success' => false,
            'code' => 401,
            'message' => '操作失败'
        ]);
    }

    public function actionList()
    {
        if (Yii::$app->request->isGet) {
            $pagination = Yii::$app->request->post();
            $model = new UserForm();
            return $this->asJson($model->list($pagination));
        }
        return $this->asJson([
            'success' => false,
            'code' => 401,
            'message' => '操作失败'
        ]);
    }
}