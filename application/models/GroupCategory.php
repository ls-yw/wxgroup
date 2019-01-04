<?php
namespace Models;

use Basic\BasicModel;

class GroupCategory extends BasicModel 
{
    protected $_targetTable = "wx_group_category";
    
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
            'name' => '微信群名称',
            'pid' => 'pid',
            'deleted' => 'deleted',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
}