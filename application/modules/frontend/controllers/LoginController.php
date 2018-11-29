<?php
namespace Modules\Frontend\Controllers;

use Basic\BasicController;
use Logics\MemberLogic;
use PHPMailer\PHPMailer\PHPMailer;

class LoginController extends BasicController  
{
    public function indexAction() {
        $this->view->aaa = 'bbbb';
    }
    
    public function registerAction()
    {
        
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
        
        $uniqid = uniqid('1030', true);
        
        $data = [];
        $data['user_name'] = $userName;
        $data['email']     = $email;
        $data['password']  = md5(md5($password).$uniqid);
        $data['uniqid']    = $uniqid;
        
        $uid = (new MemberLogic())->addMember($data);
        if(!$uid){
            return $this->ajaxReturn(4, '注册失败，请联系管理员');
        }
        
        return $this->ajaxReturn(0, '注册成功，请去激活邮箱');
    }
    
    public function sendEmailAction()
    {
        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {
            //Server settings
            $mail->SMTPDebug = 2;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp.qq.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = '376580487@qq.com';                 // SMTP username
            $mail->Password = 'lcdgnkqtfjxhbida';                           // SMTP password
            $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 465;                                    // TCP port to connect to
        
            //Recipients
            $mail->setFrom('376580487@qq.com', 'fkz');
            // 设置收件人邮箱地址
            $mail->addAddress('405548753@qq.com', 'yls');
            $mail->addReplyTo('info@example.com', 'Information');
//             $mail->addCC('cc@example.com');
//             $mail->addBCC('bcc@example.com');
        
            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Here is the subject';
            $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        
            $mail->send();
            echo 'Message has been sent';
            exit;
        } catch (\Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }
    }
}