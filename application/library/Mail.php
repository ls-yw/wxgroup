<?php
namespace Library;

use PHPMailer\PHPMailer\PHPMailer;

class Mail 
{
    
    protected $_mailerService;
    
    public function __construct() {
        $this->_mailerService = new PHPMailer(true);
        $this->_mailerService->SMTPDebug = 2;
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
    }
    
    public function sendEmail($to, $subject, $message)
    {
        
    }
}