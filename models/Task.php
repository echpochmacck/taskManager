<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "task".
 *
 * @property int $id
 * @property int $user_id
 * @property int $status_id
 * @property int $category_id
 * @property string $deadline
 * @property string $created_at
 */
class Task extends \yii\db\ActiveRecord
{


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
            [['user_id', 'status_id', 'category_id', 'deadline', 'created_at'], 'required'],
            [['user_id', 'status_id', 'category_id'], 'integer'],
            [['deadline', 'created_at'], 'safe'],
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

}
