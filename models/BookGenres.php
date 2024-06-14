<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "BookGenres".
 *
 * @property int $ID
 * @property int|null $BookID
 * @property int|null $GenreID
 *
 * @property Books $book
 * @property Genres $genre
 */
class BookGenres extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'BookGenres';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['BookID', 'GenreID'], 'integer'],
            [['BookID'], 'exist', 'skipOnError' => true, 'targetClass' => Books::class, 'targetAttribute' => ['BookID' => 'ID']],
            [['GenreID'], 'exist', 'skipOnError' => true, 'targetClass' => Genres::class, 'targetAttribute' => ['GenreID' => 'ID']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'BookID' => 'Book ID',
            'GenreID' => 'Genre ID',
        ];
    }

    /**
     * Gets query for [[Book]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBook()
    {
        return $this->hasOne(Books::class, ['ID' => 'BookID']);
    }

    /**
     * Gets query for [[Genre]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGenre()
    {
        return $this->hasOne(Genres::class, ['ID' => 'GenreID']);
    }
}