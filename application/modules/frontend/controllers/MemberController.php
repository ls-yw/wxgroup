<?php
namespace Modules\Frontend\Controllers;

use Basic\BasicController;

class memberController extends BasicController 
{
    public function initialize()
    {
        parent::initialize();
        
    }
    
    public function indexAction() {
        if(!$this->user){
            return $this->response->redirect($this->url->get('login/index'));
        }
        
        if(isset($this->user['email_status']) && $this->user['email_status'] == 0){
            return $this->response->redirect($this->url->get('login/email'));
        }
        $this->view->aaa = 'bbbb';
    }
    
    public function addWxGroupAction()
    {
        
    }
    
    public function doAddWxGroup()
    {
        $name      = $this->request->getPost('name', 'string', '');
        $qz_number = $this->request->getPost('qz_number', 'string', '');
        $desc      = $this->request->getPost('desc', 'string', '');
        $code      = $this->request->getPost('code', 'string', '');
        $qz_code   = $this->request->getPost('qz_code', 'string', '');
        $realname  = $this->request->getPost('realname', 'string', '');
        $mobile    = $this->request->getPost('mobile', 'int', '');
        $qq        = $this->request->getPost('qq', 'int', '');
        
        if(empty($name) || empty($qz_number) || empty($desc)){
            return $this->ajaxReturn(1, '必填项不能为空');
        }
        
        if(empty($code) || empty($qz_code)){
            return $this->ajaxReturn(2, '请上传二维码');
        }
        
        
    }
}