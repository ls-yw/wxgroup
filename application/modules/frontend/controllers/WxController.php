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
        $this->view->topActive = 'm-list';
        
        $this->view->seoTitle    = '微信群列表-'.$this->systemConfig['seoTitle'];
        $this->view->seoKeywords = '微信群,分享群,点赞群,微商群,互粉群';
        $this->view->seoDesc     = '微信群列表';
    }
    
    public function detailAction()
    {
        $id = $this->request->getQuery('id', 'int', 0);
        
        $info = (new WxgroupLogic())->getById($id);
        if(empty($info)){
            die('<script>alert("数据不存在");history.go(-1)</script>');
        }
        
        //记录查看次数
        (new WxgroupLogic())->addPV($_SERVER['REMOTE_ADDR'], 'wxgroup', $id);
        $info['pv']++;
        
        $this->view->info = $info;
        $this->view->categoryPairs = (new WxgroupLogic())->getCategoryPairs();
        $this->view->seoTitle    = $info['name'].$this->systemConfig['seoTitle'];
        $this->view->seoKeywords = '微信群,分享群,点赞群,微商群,互粉群';
        $this->view->seoDesc     = $info['desc'];
    }
}