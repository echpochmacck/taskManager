<?php

namespace app\controllers;

use app\models\Status;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;

class StatusController extends \yii\rest\ActiveController
{
    public function actionIndex()
    {
        return $this->render('index');
    }
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
                'get-all' => [
                    'Access-Control-Allow-Credentials' => true,
                ]
            ]
        ];

        $auth = [
            'class' => HttpBearerAuth::class,
            'optional' => ['get-all']
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

    public function actionGetAll()
    {
        return $this->asJson([
            'data' => [
                'statuses' => Status::getQuery()
                    ->asArray()
                    ->all(),
            ],
            'code' => 200,
            'message' => 'list of statuses'
        ]);
    }
}
