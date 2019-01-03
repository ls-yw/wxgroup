<?php
namespace Modules\Frontend\Controllers;

use Basic\BasicController;
use Logics\MemberLogic;
use Library\Log;

class LoginController extends BasicController  
{
    public function indexAction() {
        $this->view->topActive = 'm-login';
    }
    
    public function doLoginAction()
    {
        $userName = $this->request->getPost('userName', 'string', '');
        $password = $this->request->getPost('password');
        
        if(empty($userName) || strlen($userName) < 6){
            return $this->ajaxReturn(1, '用户名错误');
        }
        
        if(empty($password) || strlen($password) < 6){
            return $this->ajaxReturn(2, '密码错误');
        }
        
        $userByUsername = (new MemberLogic())->getMemberByUsername($userName);
        if(!$userByUsername){
            return $this->ajaxReturn(3, '用户名或密码错误');
        }
        
        $mpassword = md5(md5($password).$userByUsername['uniqid']);
        if($mpassword != $userByUsername['password']){
            return $this->ajaxReturn(3, '用户名或密码错误');
        }
        
        $this->session->set('user', $userByUsername);
        if(!$this->session->has('user')){
            Log::write('session', 'session设置失败，data:'.json_encode($userByUsername, JSON_UNESCAPED_UNICODE));
            return $this->ajaxReturn(3, '登录失败，请联系管理员');
        }
        
        (new MemberLogic())->addLogin($userByUsername);
        
        return $this->ajaxReturn(0, '登录成功');
    }
    
    public function registerAction()
    {
        $this->view->topActive = 'm-register';
    }
    
    public function doRegisterAction()
    {
        $userName = $this->request->getPost('userName', 'string', '');
        $email    = $this->request->getPost('email', 'string', '');
        $password = $this->request->getPost('password');
        
        if(empty($userName) || strlen($userName) < 6){
            return $this->ajaxReturn(1, '用户名不能小于6个字');
        }
        
        $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        if(empty($email) || !preg_match( $pattern, $email)){
            return $this->ajaxReturn(2, '请输入正确的电子邮箱');
        }
        
        if(empty($password) || strlen($password) < 6){
            return $this->ajaxReturn(2, '密码不能小于6个字');
        }
        
        $userByUsername = (new MemberLogic())->getMemberByUsername($userName);
        if($userByUsername){
            return $this->ajaxReturn(3, '该用户名已存在');
        }
        
        $userByUsername = (new MemberLogic())->getMemberByEmail($email);
        if($userByUsername){
            return $this->ajaxReturn(3, '该邮箱已存在');
        }
        
        $uniqid = uniqid(rand(1000, 9999), true);
        
        $data = [];
        $data['user_name'] = $userName;
        $data['email']     = $email;
        $data['password']  = md5(md5($password).$uniqid);
        $data['uniqid']    = $uniqid;
        
        $uid = (new MemberLogic())->addMember($data);
        if(!$uid){
            return $this->ajaxReturn(4, '注册失败，请联系管理员');
        }
        
        $data['id'] = $uid;
        $this->session->set('user', $data);
        
        (new MemberLogic())->sendActivateEmail($data, $this->systemConfig['host']);
        
        return $this->ajaxReturn(0, '注册成功，请去激活邮箱');
    }
    
    public function emailAction()
    {
        if(!$this->user){
            return $this->response->redirect($this->url->get('login/index'));
        }
        
        if(isset($this->user['email_status']) && $this->user['email_status'] != 0){
            return $this->response->redirect($this->url->get('member/index'));
        }
    }
    
    public function sendEmailAction()
    {
        if(!$this->user){
            return $this->ajaxReturn(201, '未登录');
        }
        
        if(isset($this->user['email_status']) && $this->user['email_status'] != 0){
            return $this->ajaxReturn(202, '已激活，请勿重复激活');
        }
        $res = (new MemberLogic())->sendActivateEmail($this->user, $this->systemConfig['host']);
        return $res ? $this->ajaxReturn(0, '发送成功') : $this->ajaxReturn(1, '发送失败，请联系管理员');
    }
    
    public function activateAction()
    {
        $token = $this->request->getQuery('token', 'string');
        if($token && $this->session->has($token)){
            $user = $this->session->get($token);
            $row = (new MemberLogic())->activateEmail($user, $token);
            if($row){
                return $this->response->redirect($this->url->get('member/index'));
            }else{
                $this->view->msg = '激活失败，请联系管理员。';
            }
        }
        $this->view->msg = '该邮箱验证链接已失效。';
    }
    
    public function logoutAction()
    {
        $this->session->remove('user');
        return $this->response->redirect($this->url->get('index/index'));
    }
}