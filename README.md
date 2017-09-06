<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii2 ArrayQuery Component</h1>
    <br>
</p>

Allows searching/filtering of an array. This component is very useful when displaying array data in GridViews with an
ArrayDataProvider.

[![Latest Stable Version](https://poser.pugx.org/yii2mod/yii2-array-query/v/stable)](https://packagist.org/packages/yii2mod/yii2-array-query)
[![Total Downloads](https://poser.pugx.org/yii2mod/yii2-array-query/downloads)](https://packagist.org/packages/yii2mod/yii2-array-query)
[![License](https://poser.pugx.org/yii2mod/yii2-array-query/license)](https://packagist.org/packages/yii2mod/yii2-array-query)
[![Build Status](https://travis-ci.org/yii2mod/yii2-array-query.svg?branch=master)](https://travis-ci.org/yii2mod/yii2-array-query)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yii2mod/yii2-array-query "*"
```

or add

```
"yii2mod/yii2-array-query": "*"
```

to the require section of your composer.json.

Querying Data
-------------

You may execute complex query on the array data using [[\yii2mod\query\ArrayQuery]] class. This class works similar to regular [[\yii\db\Query]] and uses same syntax. For example:

```php
$data = [
    [
        'id' => 1,
        'username' => 'admin',
        'email' => 'admin@example.com'
    ],
    [
        'id' => 2,
        'username' => 'test',
        'email' => 'test@example.com'
    ],
];

$query = new ArrayQuery();
$query->from($data);
$query->where(['username' => 'admin']);

$rows = $query->all();
```

Using with ArrayDataProvider
----------------------------

You may perform filtering using [[\yii2mod\query\ArrayQuery]] class. For example:
```php
<?php

// Some search model
    
/**
 * Setup search function for filtering and sorting.
 * @param $params
 * @return ArrayDataProvider
 */
public function search($params)
{
    $models = User::find()->asArray()->all();

    $query = new ArrayQuery();
    $query->from($models);

    // load the search form data and validate
    if ($this->load($params) && $this->validate()) {
        // adjust the query by adding the filters
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['status' => $this->status]);
        $query->andFilterWhere(['like', 'username', $this->username]);
        $query->andFilterWhere(['like', 'email', $this->email]);
    }
    
    // prepare the ArrayDataProvider
    return new ArrayDataProvider([
        'allModels' => $query->indexBy('id')->all(),
        'sort' => [
            'attributes' => ['id', 'username', 'email', 'status'],
        ],
        'pagination' => [
            'pageSize' => 10
        ],
    ]);
}

```
