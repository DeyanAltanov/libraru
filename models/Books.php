<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "Books".
 *
 * @property int $ID
 * @property string $Title
 * @property string $Author
 * @property string $ISBN
 * @property string|null $Images
 * @property int|null $TotalAmount
 * @property int|null $CurrentAmount
 * @property string|null $Description
 *
 * @property RentedBooks[] $rentedBooks
 * @property ReservedBooks[] $reservedBooks
 */
class Books extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Books';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Title', 'Author', 'ISBN'], 'required'],
            [['Author'] , 'match', 'pattern' => '/^[A-Za-z\.\s]+$/', 'message' => 'Only letters and dots allowed!'],
            [['ISBN'] , 'match', 'pattern' => '/^[0-9\-]+$/', 'message' => 'Enter valid ISBN!', 'skipOnEmpty' => false],
            [['TotalAmount', 'CurrentAmount'], 'integer'],
            [['Images'], 'file', 'maxFiles' => 15, 'extensions' => 'jpg, jpeg, png'],
            [['Description'], 'string'],
            [['Title', 'Author'], 'string', 'max' => 64],
            [['ISBN'], 'string', 'max' => 13],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Title' => 'Title',
            'Author' => 'Author',
            'ISBN' => 'Isbn',
            'Images' => 'Images',
            'TotalAmount' => 'Total Amount',
            'CurrentAmount' => 'Current Amount',
            'Description' => 'Description',
        ];
    }

    /**
     * Gets query for [[RentedBooks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRentedBooks()
    {
        return $this->hasMany(RentedBooks::class, ['BookID' => 'ID']);
    }

    /**
     * Gets query for [[ReservedBooks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReservedBooks()
    {
        return $this->hasMany(ReservedBooks::class, ['BookID' => 'ID']);
    }
}