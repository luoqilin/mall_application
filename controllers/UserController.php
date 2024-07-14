<?php

namespace app\controllers;

use app\base\BaseController;
use app\forms\UserForm;
use Yii;
use yii\web\Response;

class UserController extends BaseController
{

    /**
     * 删除用户
     * @api
     * @return Response
     */
    public function actionDelete(): Response
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

    /**
     * 用户列表
     * @api
     * @return Response
     */
    public function actionList(): Response
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