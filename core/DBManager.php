<?php

class DBManager
{
    protected $connections = [];

    protected $repository_connection_map = [];

    protected $repositories = [];

    /**
     * 接続情報からPDOインスタンス生成を行う
     *
     * @param  string $name
     * @param  array $params
     *
     */
    public function connect(string $name, array $params)
    {
        $params = array_merge([
            'dsn' => null,
            'user' => '',
            'password' => '',
            'options' => [],
        ], $params);

        $connect = new PDO(
            $params['dsn'],
            $params['uer'],
            $params['password'],
            $params['options']
        );

        $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connections[$name] = $connect;
    }

    /**
     * 接続したconnectionを取得する
     *
     * @param  string $name
     *
     * @return array
     */
    public function getConnection(string $name = null)
    {
        return is_null($name) ? current($this->connections) : $this->connections[$name];
    }

    /**
     * Repositoryに接続情報をセットする
     *
     * @param  string $repository_name
     * @param  string $name
     *
     */
    public function setRepositoryConnection(string $repository_name, string $name)
    {
        $this->repository_connection_map[$repository_name] = $name;
    }

    /**
     * リポジトリに対応する接続情報を取得する
     *
     * @param  string $repository_name
     *
     */
    public function getRepositoryConnection(string $repository_name)
    {
        $connection = [];
        if (isset($this->repository_connection_map[$repository_name])) {
            $name = $this->repository_connection_map[$repository_name];
            $connection = $this->getConnection($name);
        } else {
            $connection = $this->getConnection();
        }
        return $connection;
    }

    /**
     * Repositoryのインスタンスを取得する
     *
     * @param  string $repository_name
     *
     */
    public function get(string $repository_name)
    {
        if (!isset($this->repository_connection_map[$repository_name])) {
            $repository_class = $repository_name . 'Repository';
            $connect = $this->getRepositoryConnection($repository_name);

            $repository = new $repository_class($connect);
            $this->repositories[$repository_name] = $repository_class;
        }
        return $this->repositories['$repository_name'];
    }

    /**
     * 接続情報の破棄
     * 
     */
    public function __destruct()
    {
        foreach ($this->repositories as $repository) {
            unset($repository);
        }

        foreach ($this->connections as $connection) {
            unset($connection);
        }
    }
}
