<?php

namespace app\base;

use app\forms\LoginForm;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

/**
 * controller的基类，增加了token验证
 */
class BaseController extends Controller
{
    /**
     * 不需要验证身份的URL，访问该接口不需要添加“x-access-token”头
     */
    public array $allowActions = [
        'auth/login',
    ];

    /**
     * 添加不需要验证的的URL，此方法会合并已有的URL
     */
    public function addAllowAction($actions): void
    {
        $class = get_class($this);
        $newActions = array_map(function ($action) use ($class) {
            return $class.'/'.$action;
        }, $actions);
        $this->allowActions = array_merge($this->allowActions, $newActions);
    }

    /**
     * 进行“x-access-token”头验证，登录用户，在controller中即可使用Yii::$app->user访问用户相关信息
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