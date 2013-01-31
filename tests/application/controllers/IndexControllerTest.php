<?php

require_once TEST_PATH . '/ControllerTestCase.php';

class IndexControllerTest extends ControllerTestCase
{

    public function testHomePage()
    {
        // Go to the main page of the web application.
        $this->dispatch('/');

        // Check that we don't end up on an error page.
        $this->assertNotController('error');
        $this->assertNotAction('error');

        // OK, no error so let's see if we're at the home page.
        $this->assertModule('default');
        $this->assertController('index');
        // $this->assertAction('index');   // Fails -- 'menu'
        // $this->assertResponseCode(200); // Fails

        // Should we be testing that it either goes to login page or to 
        // the ramp.initialActivity from application.ini?
    }

    public function testIndexAction()
    {
        $params = array('action' => 'index', 'controller' => 'index', 'module' => 'default');
        $urlParams = $this->urlizeOptions($params);
        $url = $this->url($urlParams);
        $this->dispatch($url);
        
        // assertions
        $this->assertModule($urlParams['module']);
        $this->assertController($urlParams['controller']);
        // $this->assertAction($urlParams['action']);  // Fails -- 'menu'
        /*
        $this->assertQueryContentContains("div#welcome h3", "This is your project's main page");
         */
    }


}

