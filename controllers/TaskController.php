<?php

namespace app\controllers;

use app\models\Status;
use app\models\Task;
use app\models\User;
use yii\filters\Cors;
use Yii;
use yii\filters\auth\HttpBearerAuth;

class TaskController extends \yii\rest\ActiveController
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
                'create' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
                'get-tasks' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
                'change-status' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
            ]
        ];

        $auth = [
            'class' => HttpBearerAuth::class,
            'optional' => ['get-tasks']
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
    public function actionCreate()
    {
        $model = new Task();
        $model->load(Yii::$app->request->post(), '');
        if ($model->validate()) {
            $model->status_id = Status::getStatusId('active');
            $model->save(false);
            return $this->asJson([
                'data' => [
                    'task' => [
                        'title' => $model->title,
                        'description' => $model->description,
                        'deadline' => $model->deadline,
                        'belongTo' => User::findOne($model->user_id)->email,
                    ],
                ],
                'code' => 201,
                'message' => 'task created'
            ]);
        } else {
            return $this->asJson([
                'error' => [
                    'errors' => $model->errors
                ],
                'code' => 422,
                'message' => 'validation error'
            ]);
        }
    }

    public function actionGetTasks()
    {
        $tasks = Task::find()
            ->select([
                'task.title as title',
                'description',
                'deadline',
                'user.email as belongsTo',
                'created_at',
                'status.title as status',
                'category.title as category',
            ])
            ->innerJoin('user', 'user.id = task.user_id')
            ->innerJoin('status', 'status.id = task.status_id')
            ->innerJoin('category', 'status.id = task.status_id')
            ->asArray()
            ->all();
        return $this->asJson([
            'data' => [
                'tasks' => $tasks
            ],
            'code' => 200,
            'message' => 'all tasks'
        ]);
    }

    public function actionChangeStatus($id, $status_id)
    {
        $model = Task::findOne($id);
        if ($model) {
            $model->status_id = $status_id;
            $model->save(false);
            return $this->asJson([
                'data' => [
                    'task' => [
                        'title' => $model->title,
                        'description' => $model->description,
                        'deadline' => $model->deadline,
                        'belongTo' => User::findOne($model->user_id)->email,
                        'status' => Status::findOne($status_id)->title
                    ],
                ],
                'code' => 200,
                'message' => 'status changed'
            ]);
        } else {
            Yii::$app->response->statusCode = 404;
            return '';
        }
    }
}
