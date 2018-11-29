<?php
namespace Models;

use Basic\BasicModel;

class Config extends BasicModel 
{
    protected $_targetTable = "wx_config";
    
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
            'config_type' => '类型',
            'config_key' => 'key',
            'config_value' => 'value',
        ];
    }
}