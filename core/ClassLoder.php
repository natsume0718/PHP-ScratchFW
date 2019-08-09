<?php

namespace core;

class ClassLoder
{

    protected $dir;

    /**
     * ロードクラスを呼び出して、登録されているクラスファイルを読み込む
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * ディレクトリの登録を行う
     */
    public function registerDir(string $dir)
    {
        $this->dir[] = $dir;
    }

    public function loadClass($class)
    {
        foreach ($this->dirs as $dir) {
            $file = $dir . '/' .  $class . '.php';
            if (is_readable($file)) {
                require $file;
                return;
            }
        }
    }
}
