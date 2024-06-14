<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "RentedBooks".
 *
 * @property int $ID
 * @property int|null $BookID
 * @property int|null $ReaderID
 * @property string|null $TakenDate
 * @property string|null $ReturnDate
 * @property string|null $ReturnedDate
 * @property int|null $Amount
 *
 * @property Books $book
 * @property Readers $reader
 */
class RentedBooks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'RentedBooks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['BookID', 'ReaderID', 'Amount'], 'integer'],
            [['TakenDate', 'ReturnDate', 'ReturnedDate'], 'safe'],
            [['BookID'], 'exist', 'skipOnError' => true, 'targetClass' => Books::class, 'targetAttribute' => ['BookID' => 'ID']],
            [['ReaderID'], 'exist', 'skipOnError' => true, 'targetClass' => Readers::class, 'targetAttribute' => ['ReaderID' => 'ID']],
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
            'ReaderID' => 'Reader ID',
            'TakenDate' => 'Taken Date',
            'ReturnDate' => 'Return Date',
            'ReturnedDate' => 'Returned Date',
            'Amount' => 'Amount',
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
     * Gets query for [[Reader]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReader()
    {
        return $this->hasOne(Readers::class, ['ID' => 'ReaderID']);
    }
}