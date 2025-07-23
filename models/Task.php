<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "task".
 *
 * @property int $id
 * @property int $status_id
 * @property int $category_id
 * @property string $deadline
 * @property string $created_at
 * @property string $title
 * @property string|null $description
 *
 * @property Category $category
 * @property Status $status
 * @property TaskUser[] $taskUsers
 * @property User $user
 */
class Task extends \yii\db\ActiveRecord
{

    public array $users = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'default', 'value' => null],
            [['category_id', 'deadline', 'title'], 'required'],
            [['status_id', 'category_id'], 'integer'],
            [['deadline', 'created_at'], 'safe'],
            [['title', 'description'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Status::class, 'targetAttribute' => ['status_id' => 'id']],
            [['users'], 'each', 'rule' => ['exist', 'skipOnError' => false, 'targetClass' => User::class, 'targetAttribute' => 'id', 'message' => 'такого пользователя не сущетвует'], 'on' => ['register']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'status_id' => 'Status ID',
            'category_id' => 'Category ID',
            'deadline' => 'Deadline',
            'created_at' => 'Created At',
            'title' => 'Title',
            'description' => 'Description',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(Status::class, ['id' => 'status_id']);
    }

    /**
     * Gets query for [[TaskUsers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskUsers()
    {
        return $this->hasMany(TaskUser::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    // получение всех тасков + пользователей без многочисл запросов к бд
    public static function getAll()
    {
        $tasks = Self::find()
            ->select([
                'task.*',
                'category.title',
                'status.title',
            ])
            ->innerJoin('category', 'category.id = task.category_id')
            ->innerJoin('status', 'status.id = task.status_id')
            ->asArray()
            ->all();
        $users = TaskUser::find()
            ->select([
                'email',
                'task_id',
            ])
            ->innerJoin('user', 'user.id = task_user.user_id')
            ->asArray()
            ->all();

        $user_arr = [];
        foreach ($users as $user) {
            $user_arr[$user['task_id']][] = $user;
        }
        // var_dump($user_arr[8]);die;
        return array_map(function ($val) use ($user_arr) {
            $val['users'] = $user_arr[$val['id']] ?? null;
            return $val;
        }, $tasks);
    }
    public static function getUsersAll($id)
    {


        // получение всех task_id где есть этот пользователь
        $userTaskIds = TaskUser::find()
            ->select([
                'task_id',
            ])
            ->innerJoin('user', 'user.id = task_user.user_id')
            ->where(['user_id' => $id])
            ->asArray()
            ->all();


            // все таски
        $tasks = Self::find()
            ->select([
                'task.*',
                'category.title',
                'status.title',
            ])
            ->innerJoin('category', 'category.id = task.category_id')
            ->innerJoin('status', 'status.id = task.status_id')
            ->asArray()
            ->all();
        $filter = [];

        // убираем лишние таски
        foreach ($tasks as $task) {
            foreach ($userTaskIds as $task_id) {
                if ($task['id'] == $task_id['task_id']) {
                    $filter[] = $task;
                }
            }
        }
        $users = TaskUser::find()
            ->select([
                'email',
                'task_id',
            ])
            ->innerJoin('user', 'user.id = task_user.user_id')
            ->asArray()
            ->all();

        $user_arr = [];
        foreach ($users as $user) {
            $user_arr[$user['task_id']][] = $user;
        }
        // var_dump($user_arr[8]);die;
        return array_map(function ($val) use ($user_arr) {
            $val['users'] = $user_arr[$val['id']] ?? null;
            return $val;
        }, $filter);
    }



    public static function getOne($id)
    {
        $task = Self::find()
            ->select([
                'task.*',
                'category.title',
                'status.title',
            ])
            ->innerJoin('category', 'category.id = task.category_id')
            ->innerJoin('status', 'status.id = task.status_id')
            ->where(['task.id' => $id])
            ->asArray()
            ->all();
        if (!$task) {
            return null;
        }
        $users = TaskUser::find()
            ->select([
                'email',
                'task_id',
            ])
            ->innerJoin('user', 'user.id = task_user.user_id')
            ->where(['task_id' => $id])
            ->asArray()
            ->all();

        $task['users'] = $users;
        return $task;
    }
}
