<?php
namespace Basic;

use Phalcon\Mvc\Controller;
use Logics\ConfigLogic;

class BasicController extends Controller 
{
    protected $user = false;
    protected $systemConfig = null;
    protected $_errorMsg = false;
    protected $_successMsg = false;
    
    public function initialize()
    {
        $user= $this->session->get('user');
        if(!empty($user)){
            $this->user = $user;
            $this->view->userName = $user['user_name'];
            $this->view->userId = $user['id'];
        }
        
        $this->getSystemConfig();
        $this->view->seoTitle    = $this->systemConfig['seoTitle'];
        $this->view->seoKeywords = $this->systemConfig['seoKeywords'];
        $this->view->seoDesc     = $this->systemConfig['seoDesc'];
    }
    
    /**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param int $code
     * @param string $msg
     * @param String $type AJAX返回数据格式
     */
    protected function ajaxReturn($code, $msg, $data='', $type='json')
    {
        $returnMsg = ['code'=>$code, 'msg'=>$msg];
        if(!empty($data))$returnMsg['data'] 	= $data;
        $data = $returnMsg;
    
        switch (strtoupper($type)){
            case 'JSON' :
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($data,JSON_UNESCAPED_UNICODE) ); // php 5.11
        }
    }
    
    private function getSystemConfig()
    {
        $config = (new ConfigLogic())->getConfigs('system');
        if($config){
            $this->systemConfig = $config;
            $this->view->systemConfig = $config;
        }
    }
}