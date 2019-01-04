<?php
namespace Modules\Frontend\Controllers;

use Basic\BasicController;
use Logics\WxgroupLogic;

class WxController extends BasicController 
{
    public function listAction() {
        $categoryId = $this->request->getQuery('categoryId', 'int', 0);
        $page = $this->request->getQuery('page', 'int', 1);
        
        $list = (new WxgroupLogic())->getByCategory($categoryId, $page);
        
        $this->view->category   = (new WxgroupLogic())->getCategory();
        $this->view->categoryId = $categoryId;
        $this->view->wxGroup    = $list;
    }
    
    public function detailAction()
    {
        $id = $this->request->getQuery('id', 'int', 0);
        
        $info = (new WxgroupLogic())->getById($id);
        if(empty($info)){
            die('<script>alert("数据不存在");history.go(-1)</script>');
        }
        
        $this->view->info = $info;
    }
}