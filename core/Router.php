<?php

namespace core;

class Router
{

    protected $routes;

    /**
     * __construct
     *
     * @param  mixed $definitions
     */
    public function __construct(array $definitions)
    {
        $this->routea = $definitions;
    }

    /**
     * compileRoutes
     *
     * @param  mixed $definitions
     *
     * @return array
     */
    public function compileRoutes(array $definitions)
    {
        $routes = [];

        foreach ($definitions as $url => $param) {
            //urlの最初から/を削除し、そのurlを/で区切り配列に格納
            $tokens = explode('/', ltrim($url, '/'));
            foreach ($tokens as $i => $token) {
                if (strpos($token, ':' === 0)) {
                    $name = substr($token, 1);
                    $token = '(?P<' . $name . '>[^/]+)';
                }
                $tokens[$i]  = $token;
            }
            unset($i, $token);
            $pattern = '/' . implode('/', $tokens);
            $routes[$pattern] = $param;
        }
        unset($url, $param);
        return $routes;
    }

    public function resolve(string $path_info)
    {
        //先頭にスラッシュがなかった場合追加
        if (substr($path_info, 0, 1) !== '/') {
            $path_info = '/'  . $path_info;
        }

        foreach ($this->routes as $pattern => $params) {
            if (preg_match('#^' . $pattern . '$#', $path_info, $matches)) {
                $params = array_merge($params, $matches);
                return $params;
            }
        }
        return false;
    }
}
