<?php

namespace core;

use Exception;

abstract class Application
{
    protected $debug = false;
    protected $request;
    protected $response;
    protected $session;
    protected $db_manager;
    protected $router;

    /**
     * デバッグの設定、初期化メソッドの呼び出し
     *
     * @param  bool $debug
     *
     */
    public function __construct(bool $debug = false)
    {
        $this->setDebugMode($debug);
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->db_manager = new DBManager();
        $this->router = new Router($this->registerRoutes());
        $this->configure();
    }


    /**
     * setDebugMode
     *
     * @param  bool $debug
     *
     */
    protected function setDebugMode(bool $debug)
    {
        if ($debug) {
            $this->debug = true;
            ini_set('display_errors', 1);
            error_reporting(-1);
        } else {
            $this->debug = false;
            ini_set('display_errors', 0);
        }
    }

    /**
     * 任意の設定を行う
     *
     */
    protected function configure()
    { }

    /**
     * Rootディレクトリを取得する
     *
     * @return void
     */
    abstract public function getRootDir();

    /**
     * ルーティングの登録
     *
     * @return array
     */
    abstract protected function registerRoutes(): array;

    /**
     * デバッグモードか取得
     *
     * @return bool
     */
    public function isDebugMode()
    {
        return $this->debug;
    }

    /**
     * リクエストインスタンス取得
     *
     * @return core\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * レスポンスインスタンス取得
     *
     * @return core\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * セッションインスタンス取得
     *
     * @return core\Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * 接続情報インスタンス取得
     *
     * @return core\DBManager
     */
    public function getDBManaget()
    {
        return $this->db_manager;
    }

    /**
     * コントローラーのディレクトリ
     *
     * @return string
     */
    public function getControllerDir()
    {
        return $this->getRootDir() . '/controllers';
    }

    /**
     * viewのディレクトリ
     *
     * @return string
     */
    public function getViewDir()
    {
        return $this->getRootDir() . '/views';
    }

    /**
     * モデルのディレクトリ
     *
     * @return string
     */
    public function getModelDir()
    {
        return $this->getRootDir() . '/models';
    }

    /**
     * webのディレクトリを取得する
     *
     * @return string
     */
    public function getWebDir()
    {
        return $this->getRootDir() . '/web';
    }

    /**
     * アプリケーションを実行する
     *
     */
    public function run()
    {
        try {
            $params = $this->router->resolve($this->request->getPathInfo());
            if ($params === false) {
                throw new HttpNotFoundException('No route found for ' . $this->request->getPathInfo());
            }

            $controller = $params['controller'];
            $action = $params['action'];

            $this->runAction($controller, $action, $params);
        } catch (HttpNotFoundException $e) {
            $this->render404Page($e);
        } catch (UnauthorizedActionException $e) {
            list($controller, $action) = $this->login_action;
            $this->runAction($controller, $action);
        }

        $this->response->send();
    }

    /**
     * 指定されたアクションを実行する
     *
     * @param  string $controller_name
     * @param  string $action
     * @param  array $params
     *
     */
    public function runcAction(string $controller_name, string $action, array $params = [])
    {
        $controller_class = ucfirst($controller_name) . 'Controller';
        $controller = $this->findController($controller_class);
        if ($controller === false) {
            //
        }

        $content = $controller->run($action, $params);
        $this->response->setContent($content);
    }

    /**
     * 指定されたコントローラ名から対応するControllerオブジェクトを取得
     *
     * @param string $controller_class
     * @return Controller
     */
    protected function findController(string $controller_class)
    {
        if (!class_exists($controller_class)) {
            $controller_file = $this->getControllerDir() . '/' . $controller_class . '.php';
            if (!is_readable($controller_file)) {
                return false;
            } else {
                require_once $controller_file;
                if (!class_exists($controller_class)) {
                    return false;
                }
            }
        }
        return new $controller_class($this);
    }

    /**
     * 404エラー画面を返す設定
     *
     * @param Exception $e
     */
    protected function render404Page(HttpNotFoundException $e)
    {
        $this->response->setStatusCode(404, 'Not Found');
        $message = $this->isDebugMode() ? $e->getMessage() : 'Page not found.';
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        $this->response->setContent(
            <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>404</title>
</head>
<body>
    {$message}
</body>
</html>
EOF
        );
    }
}
