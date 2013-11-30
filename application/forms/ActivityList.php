<?php

class Application_Form_ActivityList extends Zend_Form
{
    const COMMENT_TYPE       = Ramp_Activity_Specification::COMMENT_TYPE;
    const SEPARATOR_TYPE     = Ramp_Activity_Specification::SEPARATOR_TYPE;
    const SETTING_TYPE       = Ramp_Activity_Specification::SETTING_TYPE;
    const ACTIVITY_LIST_TYPE =
                            Ramp_Activity_Specification::ACTIVITY_LIST_TYPE;


    protected $_activityList;

    /**
     * Constructor
     *
     * Registers form view helper as decorator
     *
     * @param array $activityList   list of ActivitySpec objects
     *
     * @param Application_Model_TableSetting $activityList the table setting
     * @return void
     */
    public function __construct($activityList)
    {
        $this->_activityList = $activityList;

        parent::__construct();
    }

    public function init()
    {
        // should add a filter specifying type

        $this->setName('activityList');

        $elements = array();

        foreach ( $this->_activityList as $activity )
        {
            if ( $activity->getType() == self::COMMENT_TYPE )
            {
                // do nothing for now
                continue;
            }
            elseif ( $activity->getType() == self::SEPARATOR_TYPE )
            {
                // do nothing for now
                continue;
            }

            $title = $activity->getTitle();
            $activityButton = new Zend_Form_Element_Submit($title, $title);

            $elements[] = $activityButton;

        }

        $this->addElements($elements);

    }


}

