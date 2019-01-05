<?php
namespace Models;

use Basic\BasicModel;

class Group extends BasicModel 
{
    protected $_targetTable = "wx_group";
    
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
            'uid' => 'uid',
            'name' => '微信群名称',
            'qz_number' => '群主微信号',
            'desc' => 'desc',
            'code' => 'code',
            'qz_code' => 'qz_code',
            'realname' => 'realname',
            'mobile' => 'mobile',
            'category_id'=>'category_id',
            'qq' => 'qq',
            'pv' => 'pv',
            'deleted' => 'deleted',
            'examine_time'=>'examine_time',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
}