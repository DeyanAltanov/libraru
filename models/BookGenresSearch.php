<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\BookGenres;

/**
 * BookGenresSearch represents the model behind the search form of `app\models\BookGenres`.
 */
class BookGenresSearch extends BookGenres
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ID', 'BookID', 'GenreID'], 'integer'],
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
        $query = BookGenres::find();

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
            'GenreID' => $this->GenreID,
        ]);

        return $dataProvider;
    }
}