<?php

namespace core;

class Response
{
    protected $content;
    protected $status_code = 200;
    protected $status_text = 'OK';
    protected $http_header = [];

    /**
     * HTTPレスポンスの送信を行う
     *
     * @return void
     */
    public function send()
    {
        header($_SERVER['SERVER_PROTOCOL'] . $this->status_code . ' ' . $this->status_text);

        foreach ($this->http_header as $name => $value) {
            header($name . ': ' . $value);
        }

        echo $this->content;
    }

    /**
     * HTMLなどをセット
     *
     * @param  string $content
     *
     * @return void
     */
    public function setContent(string $content)
    {
        $this->content = $content;
    }


    /**
     * ステータスコードとテキストをセット
     *
     * @param  int $status_code
     * @param  string $status_text
     *
     */
    public function setStatusCode(int $status_code, string $status_text = '')
    {
        $this->status_code = $status_code;
        $this->status_text = $status_text;
    }
}
