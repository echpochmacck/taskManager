<?php

namespace app\controllers;

use app\models\User;
use yii\filters\Cors;
use Yii;
use yii\filters\auth\HttpBearerAuth;

class UserController extends \yii\rest\ActiveController
{
    public $modelClass = '';
    public $enableCsrfValidation = false;
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // remove authentication filter
        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => [isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'http://' . $_SERVER['REMOTE_ADDR']],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
            ],
            'actions' => [
                'logout' => [
                    'Access-Control-Allow-Credentials' => true,
                ]
            ]
        ];

        $auth = [
            'class' => HttpBearerAuth::class,
            'only' => ['logout']
        ];
        // re-add authentication filter
        $behaviors['authenticator'] = $auth;

        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ['options'];

        return $behaviors;
    }
    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['create']);

        // customize the data provider preparation with the "prepareDataProvider()" method
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }
    public function actionRegister()
    {
        $model = new User();
        $model->load(Yii::$app->request->post(), '');
        $model->scenario = 'register';
        if ($model->validate()) {
            $model->register();
            Yii::$app->response->statusCode = 201;
            return $this->asJson([
                'message' => 'user created'
            ]);
        } else {
            Yii::$app->response->statusCode = 422;
            return $this->asJson([
                'error' => [
                    'errors' => $model->errors
                ],
                'code' => 422,
                'message' => 'validation error'
            ]);
        }
    }

    public function actionLogin()
    {
        $model = new User();
        $model->load(Yii::$app->request->post(), '');
        if ($model->validate()) {
            $user = User::findOne(['email' => $model->email]);
            if ($user && $user->validatePassword($model->password)) {
                $user->setToken();
                return $this->asJson([
                    'data' => [
                        'token' => $user->token,
                        'role' => $user->getRole()->one()->title
                    ],
                    'code' => 200
                ]);
            } else {
                Yii::$app->response->statusCode = 401;
                return "";
            }
        } else {
            Yii::$app->response->statusCode = 422;
            return $this->asJson([
                'error' => [
                    'errors' => $model->errors
                ],
                'code' => 422,
                'message' => 'validation error'
            ]);
        }
    }
    public function actionLogout()
    {
        $user = User::findOne(Yii::$app->user->id);
        $user->token = null;
        $user->save(false);
        Yii::$app->response->statusCode = 204;
        return '';
    }
    public function actionGetUsers()
    {
        return $this->asJson([
            'data' => [
                'users' => User::find()
                            ->select(['email', 'id'])
                            ->asArray()
                            ->all()

            ],
            'code' => 200,
            'message' => 'list of users'
        ]);
    }
}
