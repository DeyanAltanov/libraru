<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Genres".
 *
 * @property int $ID
 * @property string $Genre
 *
 * @property Books[] $books
 */
class Genres extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Genres';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Genre'], 'required'],
            [['Genre'], 'string', 'max' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'Genre' => 'Genre',
        ];
    }

    /**
     * Gets query for [[Books]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBooks()
    {
        return $this->hasMany(Books::class, ['Genre' => 'ID']);
    }
}