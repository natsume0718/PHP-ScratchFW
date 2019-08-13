<?php

namespace core;

abstract class DBRepository
{
    protected $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * sqlを実行する
     *
     * @param  string $sql
     * @param  array $params
     *
     * @return bool
     */
    public function execute(string $sql, array $params = [])
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    /**
     * 結果を一行取得する
     *
     * @param  string $sql
     * @param  array $params
     *
     * @return array
     */
    public function fetch(string $sql, array $params = [])
    {
        return $this->execute($sql, $params)->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 結果をすべて取得する
     *
     * @param  string $sql
     * @param  array $params
     *
     * @return array
     */
    public function fetchAll(string $sql, array $params = [])
    {
        return $this->execute($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
}
