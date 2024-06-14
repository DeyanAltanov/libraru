<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ReservedBooks".
 *
 * @property int $ID
 * @property int|null $BookID
 * @property int|null $ReaderID
 * @property string|null $Date
 * @property int|null $Amount
 *
 * @property Books $book
 * @property Readers $reader
 */
class ReservedBooks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ReservedBooks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['BookID', 'ReaderID', 'Amount'], 'integer'],
            [['Date'], 'safe'],
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
            'ID' => 'Order ID',
            'BookID' => 'Book ID',
            'ReaderID' => 'Reader ID',
            'Date' => 'Date',
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