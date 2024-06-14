<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "Readers".
 *
 * @property int $ID
 * @property string|null $Type
 * @property string $FirstName
 * @property string $LastName
 * @property string $Password
 * @property string $Email
 * @property string $Phone
 * @property string|null $Address
 * @property string|null $Comments
 * @property int|null $Active
 * @property string|null $ProfilePicture
 *
 * @property RentedBooks[] $rentedBooks
 * @property ReservedBooks[] $reservedBooks
 */
class Readers extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * {@inheritdoc}
     */

    public $OldPassword;
    public $NewPassword;
    public $RepeatNewPassword;
    public $RepeatPassword;

    public static function tableName()
    {
        return 'Readers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['FirstName', 'LastName', 'Password', 'RepeatPassword', 'Email', 'Phone'], 'required'],
            [['FirstName', 'LastName'] , 'match', 'pattern' => '/^[A-Za-z]+$/', 'message' => 'Only letters allowed!'],
            [['Password', 'NewPassword'], 'match', 'pattern' => '/.*(?=.*\d)(?=.*[a-zA-Z])(?=.*[!@#$%^&*]).{6,}/', 'message' => 'Password should be at least 6 characters long and should contain at least one number, capital letter, small letter and special character!'],
            [['Email'] , 'match', 'pattern' => '/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/', 'message' => 'Please enter a valid email!'],
            [['Phone'] , 'match', 'pattern' => '/^(\+\d{1,2}\s?)?1?\-?\.?\s?\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4}$/', 'message' => 'Please enter a valid phone number!'],
            [['OldPassword', 'NewPassword', 'RepeatNewPassword'], 'required'],
            [['Comments', 'ProfilePicture'], 'string'],
            [['Active'], 'integer'],
            [['Type'], 'string', 'max' => 15],
            [['FirstName', 'LastName', 'Phone'], 'string', 'max' => 16],
            [['Password', 'Email'], 'string', 'max' => 255],
            [['RepeatNewPassword'], 'compare', 'compareAttribute' => 'NewPassword', 'message' => 'Passwords do not match!'],
            [['RepeatPassword'], 'compare', 'compareAttribute' => 'Password', 'message' => 'Passwords do not match!'],
            [['Address'], 'string', 'max' => 255],
        ];
    }

    /**
     * Validates the current password.
     * {@inheritdoc}
     */
    public function validateCurrentPassword($user)
	{
		if (!Yii::$app->getSecurity()->validatePassword($this->OldPassword, $user->Password)){
            Yii::$app->session->setFlash('error', 'Current password is incorrect!');
            return False;
        }
        return True;
	}

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Type' => 'Type',
            'FirstName' => 'First Name',
            'LastName' => 'Last Name',
            'Password' => 'Password',
            'RepeatPassword' => 'Repeat Password',
            'OldPassword' => 'Current password',
            'NewPassword' => 'New password',
            'RepeatNewPassword' => 'Repeat new password',
            'Email' => 'Email',
            'Phone' => 'Phone',
            'Address' => 'Address',
            'Comments' => 'Comments',
            'Active' => 'Active',
            'ProfilePicture' => 'Profile Picture',
        ];
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['Email' => $email]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['ID' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user = static::findOne(['accessToken']);
        if ($user['accessToken'] === $token) {
            return new static($user);
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->ID;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Gets query for [[RentedBooks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRentedBooks()
    {
        return $this->hasMany(RentedBooks::class, ['ReaderID' => 'ID']);
    }

    /**
     * Gets query for [[ReservedBooks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReservedBooks()
    {
        return $this->hasMany(ReservedBooks::class, ['ReaderID' => 'ID']);
    }


    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->Password);
    }
}