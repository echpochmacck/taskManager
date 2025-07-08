<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "status".
 *
 * @property int $id
 * @property string $title
 */
class Status extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
        ];
    }
    public static function getStatusId(string $task)
    {
        return self::findOne(['title' => $task])->id;
    }
    public static function getQuery($data = [])
    {
        $query = Status::find()
            ->select(['title', 'id']);
        if (isset($data['id'])) {
            $query->andFilterWhere(['status.id' => $data['id']]);
        };
        return $query;
    }
}
