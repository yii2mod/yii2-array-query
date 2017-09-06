<?php

namespace yii2mod\query;

use Yii;
use yii\base\Component;
use yii\db\QueryTrait;

/**
 * Class ArrayQuery
 *
 * @package yii2mod\query
 */
class ArrayQuery extends Component
{
    use QueryTrait;

    /**
     * @var string name of the data key, which should be used as row unique id - primary key
     */
    public $primaryKeyName = 'id';

    /**
     * @var array the data to search, filter
     */
    public $from;

    /**
     * @var string the class for processing the queries
     */
    public $queryProcessorClass = 'yii2mod\query\QueryProcessor';

    /**
     * @var QueryProcessor
     */
    private $_queryProcessor;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->_queryProcessor = Yii::createObject($this->queryProcessorClass);
    }

    /**
     * Executes the query and returns all results as an array.
     *
     * @return array
     */
    public function all(): array
    {
        $rows = $this->fetchData();

        return $this->populate($rows);
    }

    /**
     * Executes the query and returns a single row of result.
     *
     * @return bool|mixed
     */
    public function one()
    {
        $rows = $this->fetchData();

        return empty($rows) ? false : reset($rows);
    }

    /**
     * Returns the number of records.
     *
     * @return int
     */
    public function count(): int
    {
        $data = $this->fetchData();

        return count($data);
    }

    /**
     * Returns a value indicating whether the query result contains any row of data.
     *
     * @return bool
     */
    public function exists(): bool
    {
        $data = $this->fetchData();

        return !empty($data);
    }

    /**
     * Sets data to be selected from.
     *
     * @param array $data
     *
     * @return $this
     */
    public function from(array $data)
    {
        $this->from = $data;

        return $this;
    }

    /**
     * Fetches data.
     *
     * @return array
     */
    protected function fetchData(): array
    {
        return $this->_queryProcessor->process($this);
    }

    /**
     * Converts the raw query results into the format as specified by this query.
     *
     * @param $rows
     *
     * @return array
     */
    public function populate($rows): array
    {
        $result = [];

        if ($this->indexBy === null) {
            return array_values($rows); // reset storage internal keys
        }

        foreach ($rows as $row) {
            if (is_string($this->indexBy)) {
                $key = $row[$this->indexBy];
            } else {
                $key = call_user_func($this->indexBy, $row);
            }
            $result[$key] = $row;
        }

        return $result;
    }
}
