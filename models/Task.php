<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "Task".
 *
 * @property int $id
 * @property int $user_id
 * @property int $status_id
 * @property int $category_id
 * @property string $deadline
 * @property string $created_at
 * @property string $title
 * @property string $description
 */
class Task extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'category_id', 'deadline', 'title'], 'required'],
            ['deadline', 'date', 'format' => 'php:Y-m-d'],
            [['title', 'description'], 'string', 'max' => 255],
            [['user_id', 'status_id', 'category_id', 'description'], 'integer'],
            [['deadline', 'created_at'], 'safe'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Status::class, 'targetAttribute' => ['status_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id'], 'message' => "such user doesn't exist"],
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
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
                // if you're using datetime instead of UNIX timestamp:
                'value' => new Expression('NOW()'),
            ],
        ];
    }

}
