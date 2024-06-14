<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ReservedBooks;

/**
 * ReservedBooksSearch represents the model behind the search form of `app\models\ReservedBooks`.
 */
class ReservedBooksSearch extends ReservedBooks
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ID', 'BookID', 'ReaderID', 'Amount'], 'integer'],
            [['Date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ReservedBooks::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ID' => $this->ID,
            'BookID' => $this->BookID,
            'ReaderID' => $this->ReaderID,
            'Date' => $this->Date,
            'Amount' => $this->Amount,
        ]);

        return $dataProvider;
    }
}