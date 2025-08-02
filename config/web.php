<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'Q2VBCBX79JSmt07XDDnjL-U3Mr6u5P7l',
            'baseUrl' => '',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'multipart/form-data' => 'yii\web\MultipartFormDataParser'
            ]
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->statusCode == 404) {
                    $response->data = [
                        'message' => 'no found',
                    ];
                }
                if ($response->statusCode == 401) {
                    $response->data = [
                        'message' => 'login failed',
                    ];
                }
                if ($response->statusCode == 403) {
                    $response->data = [
                        'message' => 'forbidden for you',
                    ];
                }
            },
            // ...
            'formatters' => [
                \yii\web\Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG, // use "pretty" output in debug mode
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                    // ...
                ],
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'enableSession' => false
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                'POST api/register' => 'user/register',
                'OPTIONS api/register' => 'user/options',
                'POST api/login' => 'user/login',
                'OPTIONS api/login' => 'user/options',
                'GET api/logout' => 'user/logout',
                'OPTIONS api/logout' => 'user/options',
                'GET api/users' => 'user/get-users',
                'OPTIONS api/users' => 'user/options',
                // Task routes
                [
                    'class' => 'yii\rest\UrlRule',
                    'prefix' => 'api',
                    'controller' => 'task',
                    'pluralize' => true,
                    'extraPatterns' => [
                        'POST new' => 'create',
                        'OPTIONS new' => 'options',
                        'GET' => 'get-all',
                        'OPTIONS' => 'options',
                        'GET sub/<id>' => 'sub',
                        'OPTIONS sub/<id>' => 'options',
                        'GET user' => 'get-user-tasks',
                        'OPTIONS user' => 'options',

                        'PUT status/<id>' => 'change-status',
                        'OPTIONS status/<id>' => 'options',
                        'GET <id>' => 'get-task',
                        'OPTIONS <id>' => 'options',
                    ]
                ],
                // Statuses
                'GET api/statuses' => 'status/get-all',
                'OPTIONS api/statuses' => 'status/options'

            ],
        ]
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];
}

return $config;
