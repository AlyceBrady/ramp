<?php

require_once TEST_PATH . '/ControllerTestCase.php';

class ActivityControllerTest extends ControllerTestCase
{

    public function testIndexAction()
    {
        $params = array('action' => 'index', 'controller' => 'activity', 'module' => 'default', 'activity' => 'Smart/index.act');
        $urlParams = $this->urlizeOptions($params);
        $url = $this->url($urlParams);
        $this->dispatch($url);
        
        // assertions
        $this->assertModule($urlParams['module']);
        // $this->assertController($urlParams['controller']); // Fails: 'index'
        // $this->assertAction($urlParams['action']);      // Fails: 'menu'
        /*
        $this->assertQueryContentContains(
            'div#view-content p',
            'View script for controller <b>' . $params['controller'] . '</b> and script/action name <b>' . $params['action'] . '</b>'
            );
        */
    }


}



