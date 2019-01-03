<?php
namespace Library;

use PHPMailer\PHPMailer\PHPMailer;

class Mail 
{
    
    protected $_mailerService;
    
    public function __construct() {
        $this->_mailerService = new PHPMailer(true);
        $this->setConfig();
    }
    
    public function setConfig()
    {
        $this->_mailerService->isSMTP();                                      // Set mailer to use SMTP
        $this->_mailerService->Host = 'smtp.qq.com';  // Specify main and backup SMTP servers
        $this->_mailerService->SMTPAuth = true;                               // Enable SMTP authentication
        $this->_mailerService->Username = '376580487@qq.com';                 // SMTP username
        $this->_mailerService->Password = 'lcdgnkqtfjxhbida';                           // SMTP password
        $this->_mailerService->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        $this->_mailerService->Port = 465;
        $this->_mailerService->setFrom($this->_mailerService->Username);
    }
    
    public function sendEmail($to, $subject, $message)
    {
        try {
            // 设置收件人邮箱地址
            $this->_mailerService->addAddress($to);
        
            //Content
            $this->_mailerService->isHTML(true);                                  // Set email format to HTML
            $this->_mailerService->Subject = $subject;
            $this->_mailerService->Body    = $message;
        
            $this->_mailerService->send();
            return true;
        } catch (\Exception $e) {
            Log::write('email', 'email发送失败，原因：'.$this->_mailerService->ErrorInfo);
            return false;
        }
    }
}