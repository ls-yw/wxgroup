<?php
namespace Modules\Frontend\Controllers;

use Basic\BasicController;
use Logics\WxgroupLogic;
use Library\Redis;

class memberController extends BasicController 
{
    public function initialize()
    {
        parent::initialize();
    }
    
    public function indexAction() {
        $this->_errorMsg = Redis::getInstance()->get($this->user['id'].'_error');
        $this->_successMsg = Redis::getInstance()->get($this->user['id'].'_success');
        if(!$this->user){
            return $this->response->redirect($this->url->get('login/index'));
        }
        
        if(isset($this->user['email_status']) && $this->user['email_status'] == 0){
            return $this->response->redirect($this->url->get('login/email'));
        }
        
        $page = $this->request->getQuery('page', 'int', 1);
        
        $this->view->data = (new WxgroupLogic())->getList($this->user['id'], $page);
        $this->view->errorMsg = $this->_errorMsg;
        $this->view->successMsg = $this->_successMsg;
        Redis::getInstance()->del($this->user['id'].'_error');
        Redis::getInstance()->del($this->user['id'].'_success');
    }
    
    public function addWxGroupAction()
    {
        if(!$this->user){
            return $this->response->redirect($this->url->get('login/index'));
        }
        
        if(isset($this->user['email_status']) && $this->user['email_status'] == 0){
            return $this->response->redirect($this->url->get('login/email'));
        }
        
        $this->view->category = (new WxgroupLogic())->getCategory();
    }
    
    public function doAddWxGroupAction()
    {
        if(!$this->user){
            return $this->ajaxReturn(201, '未登录');
        }
        
        $name      = $this->request->getPost('name', 'string', '');
        $qz_number = $this->request->getPost('qz_number', 'string', '');
        $categoryId= $this->request->getPost('category_id', 'int', 0);
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
        
        $data = [];
        $data['uid']         = $this->user['id'];
        $data['name']        = $name;
        $data['qz_number']   = $qz_number;
        $data['category_id'] = $categoryId;
        $data['desc']        = $desc;
        $data['code']        = $code;
        $data['qz_code']     = $qz_code;
        $data['realname']    = $realname;
        $data['mobile']      = $mobile;
        $data['qq']          = $qq;
        $id = (new WxgroupLogic())->addWxGroup($data);
        return $id ? $this->ajaxReturn(0, '添加成功') : $this->ajaxReturn(1, '添加失败');
    }
    
    public function editWxGroupAction()
    {
        if(!$this->user){
            return $this->response->redirect($this->url->get('login/index'));
        }
        
        if(isset($this->user['email_status']) && $this->user['email_status'] == 0){
            return $this->response->redirect($this->url->get('login/email'));
        }
        
        $id = $this->request->getQuery('id', 'int', 0);
        $info = (new WxgroupLogic())->getByIdAndUid($id, $this->user['id']);
        if(empty($info)){
            Redis::getInstance()->setex($this->user['id'].'_error', 600, '数据不存在');
            return $this->response->redirect($this->url->get('member/index'));
        }
        
        $this->view->info = $info;
        $this->view->category = (new WxgroupLogic())->getCategory();
    }
    
    public function doEditWxGroupAction()
    {
        if(!$this->user){
            return $this->ajaxReturn(201, '未登录');
        }
        $id        = $this->request->getQuery('id', 'int', 0);
        $info = (new WxgroupLogic())->getByIdAndUid($id, $this->user['id']);
        if(empty($info)){
            Redis::getInstance()->setex($this->user['id'].'_error', 600, '数据不存在');
            return $this->response->redirect($this->url->get('member/index'));
        }
        
        $name      = $this->request->getPost('name', 'string', '');
        $qz_number = $this->request->getPost('qz_number', 'string', '');
        $categoryId= $this->request->getPost('category_id', 'int', 0);
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
    
        $data = [];
        $data['uid']         = $this->user['id'];
        $data['name']        = $name;
        $data['qz_number']   = $qz_number;
        $data['category_id'] = $categoryId;
        $data['desc']        = $desc;
        $data['code']        = $code;
        $data['qz_code']     = $qz_code;
        $data['realname']    = $realname;
        $data['mobile']      = $mobile;
        $data['qq']          = $qq;
        $data['deleted']     = 1;
        $id = (new WxgroupLogic())->editWxGroup($data, $id);
        return $id ? $this->ajaxReturn(0, '修改成功') : $this->ajaxReturn(1, '修改失败');
    }
    
    public function delWxGroupAction()
    {
        $id = $this->request->getQuery('id', 'int', 0);
        if(empty($id)){
            Redis::getInstance()->setex($this->user['id'].'_error', 600, '数据不存在');
            return $this->response->redirect($this->url->get('member/index'));
        }
    
        $info = (new WxgroupLogic())->getByIdAndUid($id, $this->user['id']);
        if(empty($info)){
            Redis::getInstance()->setex($this->user['id'].'_error', 600, '数据不存在');
            return $this->response->redirect($this->url->get('member/index'));
        }
    
        $r = (new WxgroupLogic())->del($id);
        if($r){
            Redis::getInstance()->setex($this->user['id'].'_success', 600, '删除成功');
        }else{
            Redis::getInstance()->setex($this->user['id'].'_error', 600, '删除失败');
        }
        return $this->response->redirect($this->url->get('member/index'));
    }
}