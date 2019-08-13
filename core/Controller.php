<?php

namespace core;

abstract class Controller
{
    protected $controller_name;
    protected $action_name;
    protected $application;
    protected $request;
    protected $response;
    protected $session;
    protected $db_manager;

    public function __construct(Application $application)
    {
        //末尾からControllerを削除し、小文字にする
        $this->controller_name = strtolower(substr(get_class($this), 0, -10));
        $this->application = $application;
        $this->request     = $application->getRequest();
        $this->response    = $application->getResponse();
        $this->session     = $application->getSession();
        $this->db_manager  = $application->getDbManager();
    }

    /**
     * アクションを実行
     *
     * @param string $action
     * @param array $params
     * @return string レスポンスとして返すコンテンツ
     *
     * @throws UnauthorizedActionException 認証が必須なアクションに認証前にアクセスした場合
     */
    public function run(string $action, $params = [])
    {
        $this->action_name = $action;
        $action_mthod = $action . 'Action';

        if (!method_exists($this, $action_mthod)) {
            $this->forward404();
        }

        $content = $this->action_method($params);

        return $content;
    }
}
