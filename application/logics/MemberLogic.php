<?php
namespace Logics;

use Basic\BasicLogic;
use Models\Member;
use Library\Mail;
use Phalcon\DI;
use Library\Log;
use Library\Redis;

class MemberLogic extends BasicLogic 
{
    public function getMemberByUsername($username) 
    {
        return (new Member())->getOne(['user_name'=>$username, 'id,user_name,email,password,uniqid,email_status,count', 'deleted'=>0]);
    }
    
    public function getMemberByEmail($email) 
    {
        return (new Member())->getOne(['email'=>$email, 'id,user_name,email,password,uniqid,email_status,count', 'deleted'=>0]);
    }
    
    public function addMember($data)
    {
        return (new Member())->insertData($data);
    }
    
    public function sendActivateEmail($user, $host)
    {
        $key = $user['uniqid'].time();
        DI::getDefault()->get('session')->set($key, $user);
        $subject = '激活你的微信群帐号';
        $message = '您在微信群注册了帐号, 请在20分钟内点击下面地址进行激活:<br/><a href="http://'.$host.DI::getDefault()->get('url')->get('login/activate', ['token'=>$key]).'" style="font-weight:36px;">点我激活</a>';
        Log::write('email', $user['email']."\n".$subject."\n".$message);
        return (new Mail())->sendEmail($user['email'], $subject, $message);
    }
    
    public function sendForgetEmail($user, $host)
    {
        $key = $user['uniqid'].time().'forget';
        $key = md5($key);
        Redis::getInstance()->setex($key, 1200, json_encode($user));
        $subject = '重置你的微信群密码';
        $message = '您在微信群发起了找回密码, 请在20分钟内点击下面地址进行密码重置:<br/><a href="http://'.$host.DI::getDefault()->get('url')->get('login/resetPassword', ['token'=>$key]).'" style="font-weight:36px;">点我重置</a>';
        Log::write('email', $user['email']."\n".$subject."\n".$message);
        return (new Mail())->sendEmail($user['email'], $subject, $message);
    }
    
    public function addLogin($user)
    {
        $data = ['last_time'=>date('Y-m-d H:i:s'), 'count'=>$user['count'] + 1, 'last_ip'=>$_SERVER['REMOTE_ADDR']];
        (new Member())->updateData($data, ['id'=>$user['id']]);
    }
    
    public function activateEmail($user, $token)
    {
        if($user['email_status'] == 1)return true;
        $r = (new Member())->updateData(['email_status'=>1, 'email_time'=>date('Y-m-d H:i:s')], ['id'=>$user['id']]);
        if($r){
            $user['email_status'] = 1;
            $user['email_time'] = date('Y-m-d H:i:s');
            DI::getDefault()->get('session')->remove($token);
            DI::getDefault()->get('session')->set('user', $user);
        }
        return $r;
    }
    
    public function setPassword($password, $user)
    {
        $uniqid = $user['uniqid'];
        $data = ['password'=>md5(md5($password).$uniqid)];
        return (new Member())->updateData($data, ['id'=>$user['id']]);
    }
}