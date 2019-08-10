<?php

namespace core;

class Request
{

    /**
     * isPost
     *
     * @return bool
     */
    public function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] ? true : false;
    }

    /**
     * getGet
     *
     * @param  mixed $name
     * @param  mixed $default
     *
     * @return mixed
     */
    public function getGet($name, $default = null)
    {
        return filter_input(INPUT_GET, (string) $name) ?? $default;
    }

    /**
     * getPost
     *
     * @param  mixed $name
     * @param  mixed $default
     *
     * @return mixed
     */
    public function getPost($name, $default = null)
    {
        return filter_input(INPUT_POST, (string) $name) ?? $default;
    }

    /**
     * サーバーのホスト名取得
     *
     * @return string
     */
    public function getHost()
    {
        return $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'];
    }

    /**
     * gerRequestUrl
     *
     * @return string
     */
    public function getRequestUrl()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * HTTPSでアクセスされたか
     *
     * @return bool
     */
    public function isSsl()
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            return true;
        }

        return false;
    }

    /**
     * アクセス集約されたindex.phpの前方のurl取得
     *
     * @return void
     */
    public function getBaseUrl()
    {
        $script_name = $_SERVER['SCRIPT_NAME'];
        $request_url = $this->getRequestUrl();

        //urlにindex.phpの記述があるか
        if (strpos($request_url, $script_name) === 0) {
            return $script_name;
        } //省略されている場合
        else if (strpos($request_url, dirname($script_name)) === 0) {
            return rtrim(dirname($script_name), '/');
        }
        return '';
    }

    /**
     * アクセス集約されたindex.phpの後方のurl取得
     *
     * @return string
     */
    public function getPathInfo()
    {
        $base_url = $this->getBaseUrl();
        $request_url = $this->getRequestUrl();

        //getパラメータがついている場合
        $pos = strpos($request_url, '?');
        if ($pos !== false) {
            //getパラメータである?より前を切り出す
            $request_url = substr($request_url, 0, $pos);
        }
        //getパラメータを削除したurlから、index.phpまでの前方urlを除く
        return  (string) substr($request_url, strlen($base_url));
    }
}
