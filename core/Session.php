<?php

class Session
{
    protected static $session_started = false;
    protected static $session_id_regenerated = false;

    /**
     * セッションを自動スタートする
     *
     */
    public function __construct()
    {
        if (!self::$session_started) {
            session_start();
            self::$session_started = true;
        }
    }

    /**
     * セッションにセットする
     *
     * @param  string $name
     * @param  mixed $value
     *
     */
    public function set(string $name, mixed $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * 指定の名前のセッション情報取得
     * 存在しない場合デフォルト値を返却する
     *
     * @param  string $name
     * @param  mixed $default
     *
     */
    public function get(string $name, mixed $default = null)
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : $default;
    }

    /**
     * セッションから削除する
     *
     * @param  string $name
     *
     */
    public function remove(string $name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * セッションをクリアする
     *
     */
    public function clear()
    {
        $_SESSION = [];
    }

    /**
     * セッションIDを再生成する
     *
     * @param  bool $destory
     *
     */
    public function regenerate(bool $destory = true)
    {
        if (!self::$session_id_regenerated) {
            session_regenerate_id($destory);
            self::$session_id_regenerated = true;
        }
    }


    /**
     * 認証状態をセットする
     *
     * @param  bool $bool
     *
     */
    public function setAuthenticated(bool $bool)
    {
        $this->set('_authenticated', $bool);
        $this->regenerate();
    }

    /**
     * 認証状態を取得する
     *
     * @return bool
     */
    public function isAuthenticated()
    {
        return $this->get('_authenticated', false);
    }
}
