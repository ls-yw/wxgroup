<?php
namespace Modules\Frontend\Controllers;

use Basic\BasicController;

class ActicleController extends BasicController 
{
    public function indexAction() {
        $this->view->aaa = 'bbbb';
    }
    
    public function detailAction()
    {
        
    }
}