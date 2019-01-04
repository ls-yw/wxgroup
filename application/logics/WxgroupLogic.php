<?php
namespace Logics;

use Basic\BasicLogic;
use Models\Group;
use Models\GroupCategory;

class WxgroupLogic extends BasicLogic
{
    public function addWxGroup($data) {
        return (new Group())->insertData($data);
    }
    
    public function editWxGroup($data, $id) {
        return (new Group())->updateData($data, ['id'=>$id]);
    }
    
    public function getList($uid, $page)
    {
        $offset = ($page-1) * 20;
        $where = ['uid'=>$uid];
        $list = (new Group())->getList($where, 'create_time desc', $offset, 20);
        
        $total = (new Group())->count($where);
        
        return ['list'=>$list, 'pageinfo'=>['page'=>$page, 'total'=>$total]];
    }
    
    public function getByCategory($categoryId, $page)
    {
        $offset = ($page-1) * 24;
        $where = ['deleted'=>0];
        if(!empty($categoryId))$where['category_id'] = $categoryId;
        $list = (new Group())->getList($where, 'create_time desc', $offset, 24);
    
        $total = (new Group())->count($where);
    
        return ['list'=>$list, 'pageinfo'=>['page'=>$page, 'total'=>$total]];
    }
    
    public function getByIdAndUid($id, $uid)
    {
        $where = ['uid'=>$uid, 'id'=>$id];
        $r = (new Group())->getOne($where);
        return $r;
    }
    
    public function getById($id)
    {
        $where = ['id'=>$id, 'deleted'=>0];
        $r = (new Group())->getOne($where);
        return $r;
    }
    
    public function del($id)
    {
        $data = ['deleted'=>2];
        $r = (new Group())->updateData($data, ['id'=>$id]);
        return $r;
    }
    
    public function getCategory()
    {
        $list = (new GroupCategory())->getList(['deleted'=>0], 'pid asc,id asc');
        $arr = [];
        if(!empty($list)){
            foreach ($list as $val) {
                if($val['pid'] == 0){
                    $arr[$val['id']] = ['id'=>$val['id'], 'name'=>$val['name']];
                }else{
                    $arr[$val['pid']]['children'][] = ['id'=>$val['id'], 'name'=>$val['name']];
                }
            }
        }
        return $arr;
    }
    
    public function getTops($num)
    {
        $rows = (new Group())->getList(['deleted'=>0], 'examine_time desc', 0, $num);
        return $rows;
    }
}