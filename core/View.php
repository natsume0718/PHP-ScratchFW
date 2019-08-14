<?php

namespace core;

class View
{
    protected $base_dir;
    protected $defaults;
    protected $layout_variables = [];

    /**
     *
     * @param string $base_dir
     * @param array $defaults
     */
    public function __construct(string $base_dir, array $defaults = [])
    {
        $this->base_dir = $base_dir;
        $this->defaults = $defaults;
    }

    /**
     * レイアウトに渡す変数を指定
     *
     * @param string $name
     * @param mixed $value
     */
    public function setLayoutVar(string $name, $value)
    {
        $this->layout_variables[$name] = $value;
    }

    /**
     * ビューファイルをレンダリング
     *
     * @param string $_path
     * @param array $_variables
     * @param mixed $_layout
     * @return string
     */
    public function render(string $_path, array $_variables = [], $_layout = false)
    {
        $_file = $this->base_dir . '/' . $_path . '.php';
        //配列毎に変数を作成
        extract(array_merge($this->defaults, $_variables));
        //バッファリング開始(以降でechoやrequireはバッファリングされる)
        ob_start();
        ob_implicit_flush(0);

        require $_file;

        //格納された文字列取得
        $content = ob_get_clean();

        if ($_layout) {
            $content = $this->render(
                $_layout,
                array_merge(
                    $this->layout_variables,
                    ['_content' => $content,]
                )
            );
        }

        return $content;
    }

    /**
     * 指定された値をHTMLエスケープする
     *
     * @param string $string
     * @return string
     */
    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}
