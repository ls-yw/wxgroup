<?php
namespace Models;

use Basic\BasicModel;

class Member extends BasicModel
{
    
    protected $_targetTable = "wx_member";
    
    /**
     * 初始化
     * @throws \Exception
     */
    public function initialize()
    {
        parent::initialize();
        $this->setTargetTable($this->_targetTable);
    }
    
    /**
     * 表字段属性(non-PHPdoc)
     * @see \Basic\BaseModel::attribute()
     * @create_time 2017年11月17日
     */
    public function attribute()
    {
        return [
            'id'   => 'ID',
            'user_name' => '用户名',
            'email' => '邮箱',
            'password' => '密码',
            'uniqid'=> '唯一码',
            'email_status' => '邮箱状态 0 未验证 1已验证',
            'email_time' => '邮箱验证时间',
            'role' => '角色',
            'last_time' => '最后登录时间',
            'last_ip' => '最后登录IP',
            'count' => '登录次数',
            'deleted' => '状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    
    
}