<?php
namespace Logics;

use Basic\BasicLogic;
use Models\Config;

class ConfigLogic extends BasicLogic 
{
    public function getConfigs($type, $key='') {
        if(empty($type))return false;
        $where = ['type' => $type];
        $configs = (new Config())->getList($where);
        $arr = [];
        if($configs){
            foreach ($configs as $val) {
                $arr[$val['config_key']] = $val['config_value'];
            }
        }
        return $arr;
    }
}