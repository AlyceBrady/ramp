<?php
require_once 'TestConfiguration.php';

class models_ActivitySpecTest extends PHPUnit_Framework_TestCase
{
    protected $_actSpecList;
    protected $_badList;

    public function setUp()
    {
        $this->_actSpecList = array(
            'goodActList' => array( 'type' => 'activityList',
                                    'title' => 'This is a title',
                                    'description' => 'This is a description',
                                    'source' => 'This is the source'
                                  ),
            'goodComment' => array( 'type' => 'comment',
                                    'comment' => 'This is a comment'
                                  ),
            'goodContActionAct' => array( 'type' => 'controllerAction',
                                    'title' => 'This is a title',
                                    'description' => 'This is a description',
                                    'controller' => 'This is the controller',
                                    'action' => 'This is the action',
                                    'parameter' => "p1=param1&p2=param2"
                                  ),
            'goodHTML' => array( 'type' => 'html',
                                    'html' => 'This is the html'
                                  ),
            'goodReport' => array( 'type' => 'report',
                                    'title' => 'This is a title',
                                    'description' => 'This is a description',
                                    'source' => 'This is the source'
                                  ),
            'goodSeparator' => array( 'type' => 'separator'
                                  ),
            'goodSequence' => array( 'type' => 'sequence',
                                    'title' => 'This is a title',
                                    'description' => 'This is a description',
                                    'source' => 'This is the source'
                                  ),
            'goodSetting' => array( 'type' => 'setting',
                                    'title' => 'This is a title',
                                    'description' => 'This is a description',
                                    'source' => 'This is the source'
                                  ),
            'goodUrl' => array( 'type' => 'url',
                                    'title' => 'This is a title',
                                    'description' => 'This is a description',
                                    'url' => 'This is the url'
                                )
        );
        $this->_badList = array(
            'noType' => array( 'comment' => 'This is a bad comment'),
            'badType' => array( 'type' => 'This is a bad type'),
            'alNoTitle' => array( 'type' => 'activityList',
                                    'description' => 'This is a description',
                                    'source' => 'This is the source'
                                  ),
            'alNoDescription' => array( 'type' => 'activityList',
                                    'title' => 'This is a title',
                                    'source' => 'This is the source'
                                  ),
            'alNoSource' => array( 'type' => 'activityList',
                                    'title' => 'This is a title',
                                    'description' => 'This is a description',
                                  ),
            'badComment' => array( 'type' => 'comment'),
            'caNoTitle' => array( 'type' => 'controllerAction',
                                    'description' => 'This is a description',
                                    'controller' => 'This is the controller',
                                    'action' => 'This is the action',
                                    'parameter' => "p1=param1&p2=param2"
                                  ),
            'caNoDescription' => array( 'type' => 'controllerAction',
                                    'title' => 'This is a title',
                                    'controller' => 'This is the controller',
                                    'action' => 'This is the action',
                                    'parameter' => "p1=param1&p2=param2"
                                  ),
            'caNoController' => array( 'type' => 'controllerAction',
                                    'title' => 'This is a title',
                                    'description' => 'This is a description',
                                    'action' => 'This is the action',
                                    'parameter' => "p1=param1&p2=param2"
                                  ),
            'caNoAction' => array( 'type' => 'controllerAction',
                                    'title' => 'This is a title',
                                    'description' => 'This is a description',
                                    'controller' => 'This is the controller',
                                    'parameter' => "p1=param1&p2=param2"
                                  ),
            'caNoParameter' => array( 'type' => 'controllerAction',
                                    'title' => 'This is a title',
                                    'description' => 'This is a description',
                                    'controller' => 'This is the controller',
                                    'action' => 'This is the action'
                                  ),
            'badHTML' => array( 'type' => 'html'),
            'repNoTitle' => array( 'type' => 'report',
                                    'description' => 'This is a description',
                                    'source' => 'This is the source'
                                  ),
            'repNoDescription' => array( 'type' => 'report',
                                    'title' => 'This is a title',
                                    'source' => 'This is the source'
                                  ),
            'repNoSource' => array( 'type' => 'report',
                                    'title' => 'This is a title',
                                    'description' => 'This is a description'
                                  ),
            'seqNoTitle' => array( 'type' => 'sequence',
                                    'description' => 'This is a description',
                                    'source' => 'This is the source'
                                  ),
            'seqNoDescription' => array( 'type' => 'sequence',
                                    'title' => 'This is a title',
                                    'source' => 'This is the source'
                                  ),
            'seqNoSource' => array( 'type' => 'sequence',
                                    'title' => 'This is a title',
                                    'description' => 'This is a description'
                                  ),
            'setNoTitle' => array( 'type' => 'setting',
                                    'description' => 'This is a description',
                                    'source' => 'This is the source'
                                  ),
            'setNoDescription' => array( 'type' => 'setting',
                                    'title' => 'This is a title',
                                    'source' => 'This is the source'
                                  ),
            'setNoSource' => array( 'type' => 'setting',
                                    'title' => 'This is a title',
                                    'description' => 'This is a description'
                                  ),
            'urlNoTitle' => array( 'type' => 'url',
                                    'description' => 'This is a description',
                                    'url' => 'This is the url'
                                ),
            'urlNoDescription' => array( 'type' => 'url',
                                    'title' => 'This is a title',
                                    'url' => 'This is the url'
                                ),
            'urlNoUrl' => array( 'type' => 'url',
                                    'title' => 'This is a title',
                                    'description' => 'This is a description'
                                ),
            'nonStringParam' => array( 'type' => 'controllerAction',
                                    'title' => 'This is a title',
                                    'description' => 'This is a description',
                                    'controller' => 'This is the controller',
                                    'action' => 'This is the action',
                                    'parameter' => array('p1'=>'param1',
                                                         'p2'=>'param2')
                                  )
        );
    }

    public function testActivityTypesAreDistinct()
    {
        $actTypes = array(Application_Model_ActivitySpec::ACTIVITY_LIST_TYPE,
                          Application_Model_ActivitySpec::COMMENT_TYPE,
                      Application_Model_ActivitySpec::CONTROLLER_ACTION_TYPE,
                          Application_Model_ActivitySpec::HTML_TYPE,
                          Application_Model_ActivitySpec::REPORT_TYPE,
                          Application_Model_ActivitySpec::SEPARATOR_TYPE,
                          Application_Model_ActivitySpec::SEQUENCE_TYPE,
                          Application_Model_ActivitySpec::SETTING_TYPE,
                          Application_Model_ActivitySpec::URL_TYPE);
        $uniqueTypes = array_unique($actTypes);
        $this->assertSame(count($actTypes), count($uniqueTypes));
    }

    public function testConstructorForSettingWithValidActList()
    {
        $aSpec = $this->_actSpecList['goodActList'];
        $spec = new Application_Model_ActivitySpec('validActList', $aSpec);
        $this->assertSame($spec->getType(),
            Application_Model_ActivitySpec::ACTIVITY_LIST_TYPE);
        $this->assertTrue($spec->isActivityList());

        $this->assertSame($spec->getTitle(), 'This is a title');
        $this->assertTrue(strpos($spec->getDescription(),
            'This is a description') === 0);
        $this->assertSame($spec->getComment(), $spec->getDescription());
        $this->assertSame($spec->getHTML(), $spec->getDescription());
        $this->assertSame($spec->getSource(), 'This is the source');
        $this->assertSame($spec->getUrl(), $spec->getSource());
        $this->assertSame('Controller' . $spec->getController(), 'Controller');
        $this->assertSame('Action' . $spec->getAction(), 'Action');
        $this->assertSame($spec->getParameters(), array());
    }

    public function testConstructorForSettingWithValidComment()
    {
        $aSpec = $this->_actSpecList['goodComment'];
        $spec = new Application_Model_ActivitySpec('validComment', $aSpec);
        $this->assertSame($spec->getType(),
            Application_Model_ActivitySpec::COMMENT_TYPE);
        $this->assertTrue($spec->isComment());

        $this->assertTrue(strpos($spec->getDescription(),
            'This is a comment') === 0);
        $this->assertSame($spec->getComment(), $spec->getDescription());
        $this->assertSame($spec->getHTML(), $spec->getDescription());
        $this->assertSame('Title' . $spec->getTitle(), 'Title');
        $this->assertSame('Source' . $spec->getSource(), 'Source');
        $this->assertSame('Controller' . $spec->getController(), 'Controller');
        $this->assertSame('Action' . $spec->getAction(), 'Action');
        $this->assertSame('Url' . $spec->getUrl(), 'Url');
        $this->assertSame($spec->getParameters(), array());
    }

    public function testConstructorForSettingWithValidControllerAction()
    {
        $aSpec = $this->_actSpecList['goodContActionAct'];
        $spec = new Application_Model_ActivitySpec('validControllerAction', $aSpec);
        $this->assertSame($spec->getType(),
            Application_Model_ActivitySpec::CONTROLLER_ACTION_TYPE);
        $this->assertTrue($spec->isControllerAction());

        $this->assertSame($spec->getTitle(), 'This is a title');
        $this->assertTrue(strpos($spec->getDescription(),
            'This is a description') === 0);
        $this->assertSame($spec->getComment(), $spec->getDescription());
        $this->assertSame($spec->getHTML(), $spec->getDescription());
        $this->assertSame('Source' . $spec->getSource(), 'Source');
        $this->assertSame('Url' . $spec->getUrl(), 'Url');
        $this->assertSame($spec->getController(), 'This is the controller');
        $this->assertSame($spec->getAction(), 'This is the action');
        $this->assertSame(count($spec->getParameters()), 2);
    }

    public function testConstructorForSettingWithValidHTMLType()
    {
        $aSpec = $this->_actSpecList['goodHTML'];
        $spec = new Application_Model_ActivitySpec('validHTML', $aSpec);
        $this->assertSame($spec->getType(),
            Application_Model_ActivitySpec::HTML_TYPE);
        $this->assertTrue($spec->isHTML());

        $this->assertTrue(strpos($spec->getDescription(),
            'This is the html') === 0);
        $this->assertSame($spec->getComment(), $spec->getDescription());
        $this->assertSame($spec->getHTML(), $spec->getDescription());
        $this->assertSame('Title' . $spec->getTitle(), 'Title');
        $this->assertSame('Source' . $spec->getSource(), 'Source');
        $this->assertSame('Controller' . $spec->getController(), 'Controller');
        $this->assertSame('Action' . $spec->getAction(), 'Action');
        $this->assertSame('Url' . $spec->getUrl(), 'Url');
        $this->assertSame($spec->getParameters(), array());
    }

    public function testConstructorForSettingWithValidReport()
    {
        $aSpec = $this->_actSpecList['goodReport'];
        $spec = new Application_Model_ActivitySpec('validReport', $aSpec);
        $this->assertSame($spec->getType(),
            Application_Model_ActivitySpec::REPORT_TYPE);
        $this->assertTrue($spec->isReport());

        $this->assertSame($spec->getTitle(), 'This is a title');
        $this->assertTrue(strpos($spec->getDescription(),
            'This is a description') === 0);
        $this->assertSame($spec->getComment(), $spec->getDescription());
        $this->assertSame($spec->getHTML(), $spec->getDescription());
        $this->assertSame($spec->getSource(), 'This is the source');
        $this->assertSame($spec->getUrl(), $spec->getSource());
        $this->assertSame('Controller' . $spec->getController(), 'Controller');
        $this->assertSame('Action' . $spec->getAction(), 'Action');
        $this->assertSame($spec->getParameters(), array());
    }

    public function testConstructorForSettingWithValidSeparator()
    {
        $aSpec = $this->_actSpecList['goodSeparator'];
        $spec = new Application_Model_ActivitySpec('validSeparator', $aSpec);
        $this->assertSame($spec->getType(),
            Application_Model_ActivitySpec::SEPARATOR_TYPE);
        $this->assertTrue($spec->isSeparator());

        $this->assertSame('Comment' . $spec->getComment(), 'Comment');
        $this->assertSame('Source' . $spec->getSource(), 'Source');
        $this->assertSame('Title' . $spec->getTitle(), 'Title');
        $this->assertSame('Descrip' . $spec->getDescription(), 'Descrip');
        $this->assertSame('Html' . $spec->getHTML(), 'Html');
        $this->assertSame('Controller' . $spec->getController(), 'Controller');
        $this->assertSame('Action' . $spec->getAction(), 'Action');
        $this->assertSame('Url' . $spec->getUrl(), 'Url');
        $this->assertSame($spec->getParameters(), array());
    }

    public function testConstructorForSettingWithValidSequence()
    {
        $aSpec = $this->_actSpecList['goodSequence'];
        $spec = new Application_Model_ActivitySpec('validSequence', $aSpec);
        $this->assertSame($spec->getType(),
            Application_Model_ActivitySpec::SETTING_TYPE);
        $this->assertTrue($spec->isSetting());

        $this->assertSame($spec->getTitle(), 'This is a title');
        $this->assertTrue(strpos($spec->getDescription(),
            'This is a description') === 0);
        $this->assertSame($spec->getComment(), $spec->getDescription());
        $this->assertSame($spec->getHTML(), $spec->getDescription());
        $this->assertSame($spec->getSource(), 'This is the source');
        $this->assertSame($spec->getUrl(), $spec->getSource());
        $this->assertSame('Controller' . $spec->getController(), 'Controller');
        $this->assertSame('Action' . $spec->getAction(), 'Action');
        $this->assertSame($spec->getParameters(), array());
    }

    public function testConstructorForSettingWithValidSetting()
    {
        $aSpec = $this->_actSpecList['goodSetting'];
        $spec = new Application_Model_ActivitySpec('validSetting', $aSpec);
        $this->assertSame($spec->getType(),
            Application_Model_ActivitySpec::SETTING_TYPE);
        $this->assertTrue($spec->isSetting());

        $this->assertSame($spec->getTitle(), 'This is a title');
        $this->assertTrue(strpos($spec->getDescription(),
            'This is a description') === 0);
        $this->assertSame($spec->getComment(), $spec->getDescription());
        $this->assertSame($spec->getHTML(), $spec->getDescription());
        $this->assertSame($spec->getSource(), 'This is the source');
        $this->assertSame($spec->getUrl(), $spec->getSource());
        $this->assertSame('Controller' . $spec->getController(), 'Controller');
        $this->assertSame('Action' . $spec->getAction(), 'Action');
        $this->assertSame($spec->getParameters(), array());
    }

    public function testConstructorForSettingWithValidUrl()
    {
        $aSpec = $this->_actSpecList['goodUrl'];
        $spec = new Application_Model_ActivitySpec('validUrl', $aSpec);
        $this->assertSame($spec->getType(),
            Application_Model_ActivitySpec::URL_TYPE);
        $this->assertTrue($spec->isUrl());

        $this->assertSame($spec->getTitle(), 'This is a title');
        $this->assertTrue(strpos($spec->getDescription(),
            'This is a description') === 0);
        $this->assertSame($spec->getComment(), $spec->getDescription());
        $this->assertSame($spec->getHTML(), $spec->getDescription());
        $this->assertTrue(strpos($spec->getSource(), 'This is the url') === 0);
        $this->assertSame($spec->getUrl(), $spec->getSource());
        $this->assertSame('Controller' . $spec->getController(), 'Controller');
        $this->assertSame('Action' . $spec->getAction(), 'Action');
        $this->assertSame($spec->getParameters(), array());
    }

    public function testConstructorWithNoName()
    {
        $aSpec = $this->_actSpecList['goodComment'];
        $spec = new Application_Model_ActivitySpec("", $aSpec);
        $this->assertSame($spec->getType(),
            Application_Model_ActivitySpec::COMMENT_TYPE);
        $this->assertTrue($spec->isComment());

        $this->assertTrue(strpos($spec->getDescription(),
            'This is a comment') === 0);
        $this->assertSame($spec->getComment(), $spec->getDescription());
        $this->assertSame($spec->getHTML(), $spec->getDescription());
        $this->assertSame('Title' . $spec->getTitle(), 'Title');
        $this->assertSame('Source' . $spec->getSource(), 'Source');
        $this->assertSame('Controller' . $spec->getController(), 'Controller');
        $this->assertSame('Action' . $spec->getAction(), 'Action');
        $this->assertSame('Url' . $spec->getUrl(), 'Url');
        $this->assertSame($spec->getParameters(), array());
    }

    public function testSetSource()
    {
        $aSpec = $this->_actSpecList['goodComment'];
        $spec = new Application_Model_ActivitySpec("", $aSpec);

        $type = $spec->getType();
        $title = $spec->getTitle();
        $description = $spec->getDescription();
        $comment = $spec->getComment();
        $html = $spec->getHTML();
        $source = $spec->getSource();
        $url = $spec->getUrl();
        $controller = $spec->getController();
        $action = $spec->getAction();
        $params = $spec->getParameters();

        $newSource = $source . '; new source';

        $spec->setSource($newSource);
        $this->assertSame($spec->getSource(), $newSource);
        $this->assertSame($spec->getUrl(), $spec->getSource());

        $this->assertSame($spec->getType(), $type);
        $this->assertSame($spec->getTitle(), $title);
        $this->assertSame($spec->getDescription(), $description);
        $this->assertSame($spec->getComment(), $comment);
        $this->assertSame($spec->getHTML(), $html);
        $this->assertSame($spec->getController(), $controller);
        $this->assertSame($spec->getAction(), $action);
        $this->assertSame($spec->getParameters(), $params);
    }

    public function testCreateActSpecFromNullRawSpec()
    {
        $this->setExpectedException('Exception', 'has no type property');
        $spec = new Application_Model_ActivitySpec('nullSpec', array());
        return $spec;
    }

    public function testCreateActSpecFromBadRawSpec()
    {
        $this->setExpectedException('Exception', 'has no type property');
        $aSpec = $this->_badList['noType'];
        $spec = new Application_Model_ActivitySpec('noType', $aSpec);
        return $spec;
    }

    public function testCreateActSpecFromBadType()
    {
         $this->setExpectedException('Exception', 'invalid specification type');
        $aSpec = $this->_badList['badType'];
        $spec = new Application_Model_ActivitySpec('badType', $aSpec);
        return $spec;
    }

    public function testConstructorForActListWithNoTitle()
    {
        $this->setExpectedException('Exception', 'has no title property');
        $aSpec = $this->_badList['alNoTitle'];
        $spec = new Application_Model_ActivitySpec('alNoTitle', $aSpec);
    }

    public function testConstructorForActListWithNoDescription()
    {
        $this->setExpectedException('Exception', 'has no description property');
        $aSpec = $this->_badList['alNoDescription'];
        $spec = new Application_Model_ActivitySpec('alNoDescription', $aSpec);
    }

    public function testConstructorForActListWithNoSource()
    {
        $this->setExpectedException('Exception', 'has no source property');
        $aSpec = $this->_badList['alNoSource'];
        $spec = new Application_Model_ActivitySpec('alNoSource', $aSpec);
    }

    public function testConstructorForInvalidComment()
    {
        $this->setExpectedException('Exception', 'has no comment property');
        $aSpec = $this->_badList['badComment'];
        $spec = new Application_Model_ActivitySpec('badComment', $aSpec);
    }

    public function testConstructorForControllerActionWithNoTitle()
    {
        $this->setExpectedException('Exception', 'has no title property');
        $aSpec = $this->_badList['caNoTitle'];
        $spec = new Application_Model_ActivitySpec('caNoTitle', $aSpec);
    }

    public function testConstructorForControllerActionWithNoDescription()
    {
        $this->setExpectedException('Exception', 'has no description property');
        $aSpec = $this->_badList['caNoDescription'];
        $spec = new Application_Model_ActivitySpec('caNoDescription', $aSpec);
    }

    public function testConstructorForControllerActionWithNoController()
    {
        $this->setExpectedException('Exception', 'has no controller property');
        $aSpec = $this->_badList['caNoController'];
        $spec = new Application_Model_ActivitySpec('caNoController', $aSpec);
    }

    public function testConstructorForControllerActionWithNoAction()
    {
        $this->setExpectedException('Exception', 'has no action property');
        $aSpec = $this->_badList['caNoAction'];
        $spec = new Application_Model_ActivitySpec('caNoAction', $aSpec);
    }

    public function testConstructorForControllerActionWithNoParameter()
    {
        $this->setExpectedException('Exception', 'has no parameter property');
        $aSpec = $this->_badList['caNoParameter'];
        $spec = new Application_Model_ActivitySpec('caNoParameter', $aSpec);
    }

    public function testConstructorForInvalidHTML()
    {
        $this->setExpectedException('Exception', 'has no html property');
        $aSpec = $this->_badList['badHTML'];
        $spec = new Application_Model_ActivitySpec('badHTML', $aSpec);
    }

    public function testConstructorForReportWithNoTitle()
    {
        $this->setExpectedException('Exception', 'has no title property');
        $aSpec = $this->_badList['repNoTitle'];
        $spec = new Application_Model_ActivitySpec('repNoTitle', $aSpec);
    }

    public function testConstructorForReportWithNoDescription()
    {
        $this->setExpectedException('Exception', 'has no description property');
        $aSpec = $this->_badList['repNoDescription'];
        $spec = new Application_Model_ActivitySpec('repNoDescription', $aSpec);
    }

    public function testConstructorForReportWithNoSource()
    {
        $this->setExpectedException('Exception', 'has no source property');
        $aSpec = $this->_badList['repNoSource'];
        $spec = new Application_Model_ActivitySpec('repNoSource', $aSpec);
    }

    public function testConstructorForSequenceWithNoTitle()
    {
        $this->setExpectedException('Exception', 'has no title property');
        $aSpec = $this->_badList['seqNoTitle'];
        $spec = new Application_Model_ActivitySpec('seqNoTitle', $aSpec);
    }

    public function testConstructorForSequenceWithNoDescription()
    {
        $this->setExpectedException('Exception', 'has no description property');
        $aSpec = $this->_badList['seqNoDescription'];
        $spec = new Application_Model_ActivitySpec('seqNoDescription', $aSpec);
    }

    public function testConstructorForSequenceWithNoSource()
    {
        $this->setExpectedException('Exception', 'has no source property');
        $aSpec = $this->_badList['seqNoSource'];
        $spec = new Application_Model_ActivitySpec('seqNoSource', $aSpec);
    }

    public function testConstructorForSettingWithNoTitle()
    {
        $this->setExpectedException('Exception', 'has no title property');
        $aSpec = $this->_badList['setNoTitle'];
        $spec = new Application_Model_ActivitySpec('setNoTitle', $aSpec);
    }

    public function testConstructorForSettingWithNoDescription()
    {
        $this->setExpectedException('Exception', 'has no description property');
        $aSpec = $this->_badList['setNoDescription'];
        $spec = new Application_Model_ActivitySpec('setNoDescription', $aSpec);
    }

    public function testConstructorForSettingWithNoSource()
    {
        $this->setExpectedException('Exception', 'has no source property');
        $aSpec = $this->_badList['setNoSource'];
        $spec = new Application_Model_ActivitySpec('setNoSource', $aSpec);
    }

    public function testConstructorForUrlWithNoTitle()
    {
        $this->setExpectedException('Exception', 'has no title property');
        $aSpec = $this->_badList['urlNoTitle'];
        $spec = new Application_Model_ActivitySpec('urlNoTitle', $aSpec);
    }

    public function testConstructorForUrlWithNoDescription()
    {
        $this->setExpectedException('Exception', 'has no description property');
        $aSpec = $this->_badList['urlNoDescription'];
        $spec = new Application_Model_ActivitySpec('urlNoDescription', $aSpec);
    }

    public function testConstructorForUrlWithNoUrl()
    {
        $this->setExpectedException('Exception', 'has no url property');
        $aSpec = $this->_badList['urlNoUrl'];
        $spec = new Application_Model_ActivitySpec('urlNoUrl', $aSpec);
    }

    public function testConstructorForNonStringParam()
    {
        $this->setExpectedException('Exception', 'must be a string');
        $aSpec = $this->_badList['nonStringParam'];
        $spec = new Application_Model_ActivitySpec('nonStringParam', $aSpec);
    }

}
