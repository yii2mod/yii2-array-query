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
     * @return array
     */
    protected function getTestData()
    {
        return [
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
        ];
    }

    // Tests :

    public function testWhereCondition()
    {
        $query = new ArrayQuery();
        $query->from($this->getTestData());
        $query->where(['username' => 'admin']);

        $rows = $query->all();
        $this->assertEquals('admin', $rows[0]['username']);
    }

    public function testLikeCondition()
    {
        $query = new ArrayQuery();
        $query->from($this->getTestData());
        $query->where(['like', 'email', 'test']);

        $rows = $query->all();
        $this->assertEquals('test', $rows[0]['username']);
    }

    public function testApplyLimit()
    {
        $query = new ArrayQuery();
        $query->from($this->getTestData());
        $query->where(['like', 'email', 'example.com']);
        $query->limit(2);

        $rows = $query->all();
        $this->assertEquals(2, count($rows));
        $this->assertEquals('admin', $rows[0]['username']);
        $this->assertEquals('test', $rows[1]['username']);
    }

    public function testFetchFirstRow()
    {
        $query = new ArrayQuery();
        $query->from($this->getTestData());

        $row = $query->one();
        $this->assertEquals('admin', $row['username']);
    }

    public function testOrCondition()
    {
        $query = new ArrayQuery();
        $query->from($this->getTestData());
        $query->where(['or', ['username' => 'admin'], ['id' => 3]]);

        $rows = $query->all();
        $this->assertEquals(2, count($rows));
        $this->assertEquals('admin', $rows[0]['username']);
        $this->assertEquals(3, $rows[1]['id']);
    }

    public function testBetweenCondition()
    {
        $query = new ArrayQuery();
        $query->from($this->getTestData());
        $query->where(['between', 'id', 1, 2]);

        $rows = $query->all();
        $this->assertEquals(2, count($rows));
        $this->assertEquals('admin', $rows[0]['username']);
        $this->assertEquals('test', $rows[1]['username']);
    }

    public function testNotCondition()
    {
        $query = new ArrayQuery();
        $query->from($this->getTestData());
        $query->where(['not', ['username' => 'admin']]);

        $rows = $query->all();
        $this->assertEquals(2, count($rows));
        $this->assertEquals('test', $rows[0]['username']);
        $this->assertEquals('guest', $rows[1]['username']);
    }

    public function testInCondition()
    {
        $query = new ArrayQuery();
        $query->from($this->getTestData());
        $query->where(['in', 'id', [1, 3]]);

        $rows = $query->all();
        $this->assertEquals(2, count($rows));
        $this->assertEquals('admin', $rows[0]['username']);
        $this->assertEquals('guest', $rows[1]['username']);
    }

    public function testExistsCondition()
    {
        $query = new ArrayQuery();
        $query->from($this->getTestData());
        $query->where(['username' => 'admin']);

        $this->assertTrue($query->exists());
    }
}