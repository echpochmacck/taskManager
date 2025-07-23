<?php

namespace app\controllers;

use app\models\Status;
use app\models\Task;
use app\models\TaskUser;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use Yii;

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
                'sub' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
                'get-user-tasks' => [
                    'Access-Control-Allow-Credentials' => true,

                ]
            ]
        ];

        $auth = [
            'class' => HttpBearerAuth::class,
            'only' => ['create', 'sub', 'get-user-tasks'],
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
    // create new task
    public function actionCreate()
    {
        if (Yii::$app->user->identity->isAdmin) {

            $model = new Task();
            $model->scenario = 'register';
            $model->load(Yii::$app->request->post(), '');
            if ($model->validate()) {
                $model->status_id = Status::getStatusId('active');
                $model->save(false);
                foreach ($model->users as $user) {
                    $TaskUser = new TaskUser();
                    $TaskUser->user_id = $user;
                    $TaskUser->task_id = $model->id;
                    $TaskUser->save(false);
                }
                Yii::$app->response->statusCode = 201;
                return $this->asJson([
                    'data' => [
                        'task' => [
                            'title' => $model->title,
                            'description' => $model->description,
                            'deadLine' => $model->deadline,
                            'category' => $model->getCategory()->one()->title,
                            'status' => $model->getStatus()->one()->title,
                            'users' => TaskUser::find()
                                ->select('email')
                                ->innerJoin('user', 'user.id = task_user.user_id')
                                ->where(['task_id' => $model->id])
                                ->asArray()
                                ->all()
                        ]
                    ]
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
        } else {
            Yii::$app->response->statusCode = 403;
            return '';
        }
    }

    public function actionGetAll()
    {
        return $this->asJson([
            'data' => [
                'tasks' => Task::getAll()
            ],
            'code' => 200,
            'message' => 'list of tasks'
        ]);
    }
    public function actionSub($id)
    {
        $task = Task::findOne($id);
        if ($task) {
            if (TaskUser::findOne(['task_id' => $task->id, 'user_id' => Yii::$app->user->id])) {
                Yii::$app->response->statusCode = 403;
                return '';
            }
            $model = new TaskUser();
            $model->task_id = $task->id;
            $model->user_id = Yii::$app->user->id;
            $model->save(false);
            Yii::$app->response->statusCode = 204;
            return '';
        } else {
            Yii::$app->response->statusCode = 404;
            return '';
        }
    }

    public function actionGetTask($id)
    {
        $task = Task::getOne($id);
        if ($task) {
            return $this->asJson([
                'data' => [
                    'task' => $task

                ]
            ]);
        } else {
            Yii::$app->response->statusCode = 404;
            return '';
        }
    }
    public function actionGetUserTasks()
    {
        $tasks = Task::getUsersAll(Yii::$app->user->id);
        return $this->asJson([
            'data' => [
                'tasks' => [
                    !empty($tasks) ? $tasks : null
                ]
            ],
            'code' => 200,
            'message' => 'list of user tasks'
        ]);
    }
}
