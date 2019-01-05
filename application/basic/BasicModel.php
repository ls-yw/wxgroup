<?php
namespace Basic;

use Phalcon\Mvc\Model;
use Library\Log;
use Phalcon\Db;

class BasicModel extends Model
{
    protected $_targetTable = null;
    
    public function initialize(){
        $this->setWriteConnectionService("dbMaster");
        $this->setReadConnectionService("dbMaster");
    }
    
    /**
     * 设置表名
     * @param $table
     */
    public function setTargetTable($table)
    {
        $this->_targetTable = $table;
        $this->setSource($this->_targetTable);
    }
    
    /**
     * SQL重置前缀
     * @param $string
     * @return string
     */
    public function loadPrefix($string=null){
        static $db_prefix=null;
        if(!$db_prefix){
            $dbConfig=$this->getReadConnection()->getDescriptor();
            $db_prefix=isset($dbConfig['prefix'])?$dbConfig['prefix']:null;
        }
        if(!$string || !is_string($string)){
            $preg_name=str_replace('\\', '_',get_class($this));
            $string=preg_replace('/^(([^_]_?)*Models_?)/i', '',$preg_name );
            $string='{{'.strtolower($string).'}}';
        }
    
        return preg_replace('/\{\{(.+?)\}\}/',$db_prefix.'\\1',$string);
    }
    
    /**
     * 处理where条件，转化为sql
     * array(
     *     'id' => [1,2,3]               //id in (1,2,3)
     *     'status' => ['in', [1,2]]     //status in (1,2)
     *     'name' => ['like', '%产品%']   //name like '%产品%'
     *     'year' => ['between', [2015,2017]] //year between 2015 AND 2017
     *     'age' => ['>', 10]            //age > 10
     *     'start_time' =>['or', 'start_time'=>['>=', '2017-11-17 17:20:10'], 'start_time'=>['<=', '2017-11-17 17:30:10']]
     *                                   //(start_time >= '2017-11-17 17:20:10') or (start_time <= '2017-11-17 17:30:10')
     *     '_sql' => ['_sql', "(start_time >= '2017-11-17 17:20:10') or (start_time <= '2017-11-17 17:30:10')"],    //直接拼接后面的sql，若有多个 key 可随意写，但不能为数字
     * )
     * 默认 and 连接
     * @param string|array $where
     */
    public function dealWhere($where)
    {
        if(empty($where))return ['where'=>'1=1', 'params'=>[]];
        if(!is_array($where))return ['where'=>$where, 'params'=>[]];
         
        $fields = $val = [];
         
        foreach ($where as $key => $value) {
            if(empty($key) || is_numeric($key))continue;
            if(is_string($value[0]))$value[0] = trim(strtolower($value[0]));
            if(is_array($value)){
                if(in_array($value[0], ['>', '>=', '<', '<=', 'like', '!=', '<>'])){
                    $fields[] = "`{$key}` {$value[0]} ?";
                    $val[] = $value[1];
                }elseif ($value[0] == 'between' && is_array($value[1])){
                    $fields[] = $key." between ? AND ?";
                    $val[] = $value[1][0];
                    $val[] = $value[1][1];
                }elseif ($value[0] == 'in'){
                    $w = [];
                    for($i=1;$i<=count($value[1]);$i++)  {
                        $w[] = '?';
                    }
                    $fields[] = "`{$key}` in (".implode(',', $w).')';
                    foreach ($value[1] as $v) {
                        $val[] = $v;
                    }
                }elseif ($value['0'] == 'or'){
                    $childWhere = [];
                    $childParams = [];
                    foreach ($value as $k => $v) {
                        if($k == 0)continue;
                        $tmp_Where = $this->dealWhere([$k=>$v]);
                        $childWhere[] = $tmp_Where['where'];
                        $childParams = empty($childParams) ? $tmp_Where['params'] : array_merge($childParams, $tmp_Where['params']);
                    }
                    $fields[] = ' ('.implode(') OR (', $childWhere).') ';
                    $val = array_merge($val, $childParams);
                }elseif ($value['0'] == '_sql'){
                    $fields[] = $value[1];
                }else{
                    $w = [];
                    for($i=1;$i<=count($value);$i++)  {
                        $w[] = '?';
                    }
                    $fields[] = "`{$key}` in (".implode(',', $w).')';
                    foreach ($value as $v) {
                        $val[] = $v;
                    }
                }
            }else{
                $fields[] = "`{$key}` = ?";
                $val[] = $value;
            }
        }
        $whereSql = ' ('.implode(') AND (', $fields).') ';
        $paramSql = $val;
         
        return ['where'=>$whereSql, 'params'=>$paramSql];
    }
    
    /**
     * 表字段属性
     *
     * @create_time 2017年11月17日
     */
    public function attribute(){}
    
    /**
     * 处理需要查询的字段
     * @param string|array $fiedls
     * @create_time 2017年11月17日
     */
    public function dealFields($fiedls='')
    {
        $defaultFields = $this->attribute();
        if((empty($fiedls) || trim($fiedls) == '*') && !empty($defaultFields)){
            $fiedls = array_keys($defaultFields);
        }
        if(is_array($fiedls))return '`'.implode('`,`', $fiedls).'`';
        return $fiedls;
    }
    
    /**
     * 处理新增数据
     * @param int $data
     */
    public function dealInsertData(array $data)
    {
        $value = $fieldArr = $fieldPlaceholderArr = [];
        foreach ($data as $key => $val) {
            $fieldArr[]            = $key;
            $fieldPlaceholderArr[] = '?';
            $value[]               = $val;
        }
        $str = '(`'.implode('`,`', $fieldArr).'`) VALUES ('.implode(',', $fieldPlaceholderArr).')';
        return ['val'=>$str, 'params'=>$value];
    }
    
    /**
     * 处理更新数据
     * @param array $data
     * @create_time 2017年11月14日
     */
    public function dealUpdateData(array $data)
    {
        $value = $fieldArr = [];
	    foreach ($data as $key => $val) {
	        if(strpos($val, '+') || strpos($val, ' - ')){
	            $fieldArr[]            = '`'.$key.'`='.$val;
	        }else{
	            $fieldArr[]            = '`'.$key.'`=?';
	            $value[]            = $val;
	        }
	    }
	    
	    $str = implode(',', $fieldArr);
	    return ['val'=>$str, 'params'=>$value];
    }
    
    /**
     * 添加数据
     * @param $data
     * @return bool|\Lib\Extend\Database\bool
     * @throws \Exception
     */
    public function insertData(array $data)
    {
        $fields = $this->attribute();
        if(isset($fields['create_time']) && !isset($data['create_time']))$data['create_time'] = date('Y-m-d H:i:s');
        if(isset($fields['update_time']) && !isset($data['update_time']))$data['update_time'] = date('Y-m-d H:i:s');
        $data = $this->dealInsertData($data);
        $sql = "INSERT INTO {$this->_targetTable} ".$data['val'];
        $result = $this->execute($sql, $data['params']);
    
        return $result;
    }
    
    /**
     * 更新数据
     * @param $data
     * @param $where
     * @return bool|\Lib\Extend\Database\bool
     * @throws \Exception
     */
    public function updateData(array $data, $where)
    {
        $fields = $this->attribute();
        if(isset($fields['update_time']) && !isset($data['update_time']))$data['update_time'] = date('Y-m-d H:i:s');
        $data = $this->dealUpdateData($data);
        $whereSql = $this->dealWhere($where);
        $params = array_merge($data['params'], $whereSql['params']);
        $sql = "UPDATE {$this->_targetTable} set ".$data['val'].' where '.$whereSql['where'];
        return $this->execute($sql, $params);
    }
    
    /**
     * 逻辑删除数据
     * @param $where
     * @return bool|\Lib\Extend\Database\bool
     * @throws \Exception
     */
    public function deleteData(array $where)
    {
        $whereSql = $this->dealWhere($where);
        $sql = "UPDATE {$this->_targetTable} set `status`=1 where ".$whereSql['where'];
        return $this->execute($sql, $whereSql['params']);
    }
    
    /**
     * 物理删除数据
     * @param $where
     * @return bool|\Lib\Extend\Database\bool
     * @throws \Exception
     */
    public function delData(array $where)
    {
        $whereSql = $this->dealWhere($where);
        $sql = "DELETE FROM {$this->_targetTable} where ".$whereSql['where'];
        return $this->execute($sql, $whereSql['params']);
    }
    
    /**
     * 获取单条数据(通过主键)
     * @param $id
     * @param $fields
     * @return array
     * @throws \Exception
     */
    public function getById(int $id, $fields=NULL)
    {
        $fields = $this->dealFields($fields);
        $sql = "select {$fields} from {$this->_targetTable} where id=?";
        $params = [$id];
        $data = $this->getRows($sql, $params);
        return isset($data[0]) ? $data[0] : array();
    }
    
    /**
     * 获取单条数据
     * @param $where
     * @param $fields
     * @param $orderBy
     * @return array
     * @throws \Exception
     */
    public function getOne(array $where, $fields="", string $orderBy=''):array
    {
        $data = $this->getList($where, $orderBy, null, null, $fields);
        return isset($data[0]) ? $data[0] : array();
    }
    
    /**
     * 获取列表(支持分页)
     * @param $where
     * @param $orderBy
     * @param $offset
     * @param $row
     * @param $fields
     * @return array
     * @throws \Exception
     */
    public function getList($where, $orderBy='', $offset=NUll, $row=NUll, $fields=NUll)
    {
        $whereSql = $this->dealWhere($where);
        $fields = $this->dealFields($fields);
        if(!empty($orderBy))$orderBy = 'order by '.$orderBy;
    
        $sql = "select {$fields} from {$this->_targetTable} where ".$whereSql['where']." {$orderBy}";
        if($offset !== NULl && ($offset !== false && $offset >=0 && $row>0)) $sql .= " limit {$offset},{$row}";
        return $this->getRows($sql, $whereSql['params']);
    }
    
    /**
     * 获取多条数据
     * @param string $sql
     * @param array $params
     * @return array|bool
     */
    public function getRows($sql, $params=[], $isCache= false, $cacheTime=10)
    {
    
//         if($isCache == true){
//             $key = md5($sql.json_encode($params));
//             if(Redis::getInstance()->exists($key)){
//                 return json_decode(Redis::getInstance()->get($key), true);
//             }
//         }
        try{
            $rows = $this->readData($sql, $params);
        } catch (\Exception $e) {
            Log::write('sql', $e->getMessage(), 'error');
            return false;
        }
         
//         if($isCache == true){
//             Redis::getInstance()->setex($key, $cacheTime, json_encode($rows, JSON_UNESCAPED_UNICODE));
//         }
        return $rows ? $rows : [];
    }
    
    //原生SQL查询 (只读)
    # -----------------
    # 例:表全名sn_user 或设定前缀sn_, 可用 {{user}}
    # @params sql 必需
    # @cacheTime 类型:整数(单位秒,0 永久缓存)
    #
    # $this->readData("select * from sn_user|{{user}} where 1=1 limit 10"); 或
    # $this->readData("select * from sn_user|{{user}} where name=? limit 10",['小李']);
    # @return --\Simple 对像
    #
    #
    public function readData($sql=null,$bindParams=null,$cacheTime=null){
        if(!$sql || !is_string($sql)){
            Log::write('sql', 'readData sql error：'.json_encode($sql), 'error');
            return [];
        }
        $sql=$this->loadPrefix($sql);
    
        //执行SQL
        try{
            $connection = $this->getReadConnection();
            $result = $connection->query($sql, $bindParams);
            $result->setFetchMode(Db::FETCH_ASSOC);
            $data = $result->fetchAll();
    
        }catch(\Exception $e){
            Log::write('sql', 'SQL:'.$sql.' VALUE:'.json_encode($bindParams, JSON_UNESCAPED_UNICODE), 'error');
            Log::write('sql', $e->getMessage(), 'error');
            return [];
        }
        return $data;
    }
    
    /**
     * 执行sql(只写)
     * @param string $sql
     * @param array $params
     * @return boolean|id     新增时返回新增的ID
     */
    public function execute($sql, $params)
    {
        try {
            $rows = $this->getWriteConnection()->execute($sql,$params);
    
            return $this->getWriteConnection()->lastInsertId() > 0 ? $this->getWriteConnection()->lastInsertId() : true;
        }catch (\Exception $e){
            Log::write('sql', 'SQL:'.$sql.' VALUE:'.json_encode($params, JSON_UNESCAPED_UNICODE), 'error');
            Log::write('sql', $e->getMessage(), 'error');
            return false;
        }
    }
}