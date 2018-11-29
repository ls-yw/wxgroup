<?php
namespace Logics;

use Basic\BasicLogic;
use Models\Member;

class MemberLogic extends BasicLogic 
{
    public function getMemberByUsername($username) 
    {
        return (new Member())->getOne(['user_name'=>$username, 'id,user_name,email,password,uniqid,email_status', 'status'=>0]);
    }
    
    public function getMemberByEmail($email) 
    {
        return (new Member())->getOne(['email'=>$email, 'id,user_name,email,password,uniqid,email_status', 'status'=>0]);
    }
    
    public function addMember($data)
    {
        return (new Member())->insertData($data);
    }
}