<?php

namespace app\base;

use app\forms\LoginForm;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class BaseController extends Controller
{
    public array $allowActions = [
        'auth/login',
    ];

    public function setAllow($actions): void
    {
        $class = get_class($this);
        $newActions = array_map(function ($action) use ($class) {
            return $class.'/'.$action;
        }, $actions);
        $this->allowActions = array_merge($this->allowActions, $newActions);
    }

    /**
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        $requestUrl = Yii::$app->request->url;
        if (!in_array($requestUrl, $this->allowActions)) {
            $accessToken = Yii::$app->request->headers->get('x-access-token');
            if (isset($accessToken) and !empty($accessToken)) {
                $model = new LoginForm();
                $model->accessToken = $accessToken;
                $model->loginToken();
            } else {
                Yii::$app->response->statusCode = 200;
                Yii::$app->response->statusText = 'ok';
                Yii::$app->response->data = json_encode([
                    'success' => false,
                    'code' => 401,
                    'message' => 'Access denied',
                    'data' => []
                ]);
                Yii::$app->response->send();
            }
        }
        return parent::beforeAction($action);
    }

}