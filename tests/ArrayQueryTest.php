<?php

namespace yii2mod\query\tests;

use yii2mod\query\ArrayQuery;

/**
 * Class ArrayQueryTest
 * @package yii2mod\query\tests
 */
class ArrayQueryTest extends TestCase
{
    /**
     * @param array $config
     * @return ArrayQuery array query instance.
     */
    protected function createArrayQuery($config = [])
    {
        return (new ArrayQuery($config))->from([
            [
                'id' => 1,
                'username' => 'admin',
                'email' => 'admin@example.com',
            ],
            [
                'id' => 2,
                'username' => 'test',
                'email' => 'test@example.com'
            ],
            [
                'id' => 3,
                'username' => 'guest',
                'email' => 'guest@example.com'
            ],
        ]);
    }

    // Tests :

    public function testWhereCondition()
    {
        $query = $this->createArrayQuery();
        $query->where(['username' => 'admin']);
        $rows = $query->all();

        $this->assertEquals('admin', $rows[0]['username']);
    }

    public function testLikeCondition()
    {
        $query = $this->createArrayQuery();
        $query->where(['like', 'email', 'test']);
        $rows = $query->all();

        $this->assertEquals('test', $rows[0]['username']);
    }

    public function testNotLikeCondition()
    {
        $query = $this->createArrayQuery();
        $query->where(['not like', 'username', 'admin']);
        $rows = $query->all();

        $this->assertEquals('guest', $rows[1]['username']);
        $this->assertCount(2, $rows);
    }

    public function testApplyLimit()
    {
        $query = $this->createArrayQuery();
        $query->where(['like', 'email', 'example.com']);
        $query->limit(2);
        $rows = $query->all();

        $this->assertCount(2, $rows);
        $this->assertEquals('admin', $rows[0]['username']);
        $this->assertEquals('test', $rows[1]['username']);
    }

    public function testFetchFirstRow()
    {
        $query = $this->createArrayQuery();
        $row = $query->one();

        $this->assertEquals('admin', $row['username']);
    }

    public function testOrCondition()
    {
        $query = $this->createArrayQuery();
        $query->where(['or', ['username' => 'admin'], ['id' => 3]]);
        $rows = $query->all();

        $this->assertCount(2, $rows);
        $this->assertEquals('admin', $rows[0]['username']);
        $this->assertEquals(3, $rows[1]['id']);
    }

    public function testBetweenCondition()
    {
        $query = $this->createArrayQuery();
        $query->where(['between', 'id', 1, 2]);
        $rows = $query->all();

        $this->assertCount(2, $rows);
        $this->assertEquals('admin', $rows[0]['username']);
        $this->assertEquals('test', $rows[1]['username']);
    }

    public function testNotCondition()
    {
        $query = $this->createArrayQuery();
        $query->where(['not', ['username' => 'admin']]);
        $rows = $query->all();

        $this->assertCount(2, $rows);
        $this->assertEquals('test', $rows[0]['username']);
        $this->assertEquals('guest', $rows[1]['username']);
    }

    public function testInCondition()
    {
        $query = $this->createArrayQuery();
        $query->where(['in', 'id', [1, 3]]);
        $rows = $query->all();

        $this->assertCount(2, $rows);
        $this->assertEquals('admin', $rows[0]['username']);
        $this->assertEquals('guest', $rows[1]['username']);
    }

    public function testExistsCondition()
    {
        $query = $this->createArrayQuery();
        $query->where(['username' => 'admin']);

        $this->assertTrue($query->exists());
    }

    public function testOrderByASC()
    {
        $query = $this->createArrayQuery();
        $query->orderBy('email');
        $rows = $query->all();

        $this->assertEquals('admin', $rows[0]['username']);
    }

    public function testOrderByDESC()
    {
        $query = $this->createArrayQuery();
        $query->orderBy(['email' => SORT_DESC]);
        $rows = $query->all();

        $this->assertEquals('test', $rows[0]['username']);
    }

    public function testFilterWhereCondition()
    {
        $query = $this->createArrayQuery();
        $query->filterWhere(['username' => 'admin']);
        $rows = $query->all();

        $this->assertEquals('admin', $rows[0]['username']);
        $this->assertCount(1, $rows);
    }

    public function testFilterAndCondition()
    {
        $query = $this->createArrayQuery();
        $query->filterWhere(['username' => 'guest']);
        $query->andFilterWhere(['email' => 'guest@example.com']);
        $rows = $query->all();

        $this->assertEquals('guest', $rows[0]['username']);
        $this->assertCount(1, $rows);
    }

    public function testFilterOrCondition()
    {
        $query = $this->createArrayQuery();
        $query->filterWhere(['username' => 'guest']);
        $query->orFilterWhere(['username' => 'admin']);
        $rows = $query->all();

        $this->assertEquals('guest', $rows[0]['username']);
        $this->assertEquals('admin', $rows[1]['username']);
        $this->assertCount(2, $rows);
    }

    public function testFilterNotCondition()
    {
        $query = $this->createArrayQuery();
        $query->filterWhere(['not', ['username' => 'guest']]);
        $rows = $query->all();

        $this->assertEquals('admin', $rows[0]['username']);
        $this->assertEquals('test', $rows[1]['username']);
        $this->assertCount(2, $rows);
    }

    public function testFilterBetweenCondition()
    {
        $query = $this->createArrayQuery();
        $query->filterWhere(['between', 'id', 2, 3]);
        $rows = $query->all();

        $this->assertEquals('test', $rows[0]['username']);
        $this->assertEquals('guest', $rows[1]['username']);
        $this->assertCount(2, $rows);
    }

    public function testFilterInCondition()
    {
        $query = $this->createArrayQuery();
        $query->filterWhere(['in', 'id', [1, 2, 3]]);
        $rows = $query->all();

        $this->assertEquals('admin', $rows[0]['username']);
        $this->assertEquals('test', $rows[1]['username']);
        $this->assertEquals('guest', $rows[2]['username']);
        $this->assertCount(3, $rows);
    }

    public function testFilterLikeCondition()
    {
        $query = $this->createArrayQuery();
        $query->filterWhere(['like', 'username', 'gu']);
        $query->orFilterWhere(['like', 'username', 'ad']);
        $rows = $query->all();

        $this->assertEquals('guest', $rows[0]['username']);
        $this->assertEquals('admin', $rows[1]['username']);
        $this->assertCount(2, $rows);
    }

    public function testSetCustomPrimaryKey()
    {
        $query = $this->createArrayQuery(['primaryKeyName' => 'username']);
        $query->where(['not', ['username' => 'admin']]);
        $rows = $query->all();

        $this->assertCount(2, $rows);
        $this->assertEquals('username', $query->primaryKeyName);

    }
}