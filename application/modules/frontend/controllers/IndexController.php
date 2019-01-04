<?php
namespace Modules\Frontend\Controllers;

use Basic\BasicController;
use Logics\WxgroupLogic;

class IndexController extends BasicController 
{
    public function indexAction() {
        $this->view->wxGroup = (new WxgroupLogic())->getTops(12);
        $this->view->category = (new WxgroupLogic())->getCategory();
    }
    
    public function aaAction()
    {
        
    }
}