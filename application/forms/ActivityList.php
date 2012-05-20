<?php

class Application_Form_ActivityList extends Zend_Form
{
    const COMMENT_TYPE       = Application_Model_ActivitySpec::COMMENT_TYPE;
    const SEPARATOR_TYPE     = Application_Model_ActivitySpec::SEPARATOR_TYPE;
    const SETTING_TYPE       = Application_Model_ActivitySpec::SETTING_TYPE;
    const ACTIVITY_LIST_TYPE =
                            Application_Model_ActivitySpec::ACTIVITY_LIST_TYPE;


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
        /*
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'ul')),
            array(
                array('DivTag' => 'HtmlTag'),
                array('tag' => 'div', 'class' => 'formContentsWrapper')
            ),
            'Form'
        ));
         */

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
            /*
            $activityButton->setDecorators(array(
                array('Tooltip'),
                array('ViewHelper'),
                array('HtmlTag', array('tag' => 'li', 'class' => 'submit'))
            ));
             */

            $elements[] = $activityButton;

    /*
            $name = $field->getDbFieldName();
            $decorations = $this->_getReqRecDecorations($field);
            $label = $decorations['label'] . $field->getLabel();
            $class = $decorations['class'];
            $title = $decorations['title'];
            if ( $field->isVisible() )
            {
                $fieldElement = new Zend_Form_Element_Text($name);
                $fieldElement->setLabel($label)
                        ->addFilter('StripTags')
                        ->addFilter('StringTrim');
                $footnote = $field->getFieldFootnote();
                if ( $footnote != "" )
                {
                    $fieldElement->addDecorator('Label',
                                array('tag'=>'dt', 'title'=>$footnote));
                    if ( strlen($title) > 0 )
                        { $title .= ": "; }
                    $title .= $footnote;
                }
                if ( ! $this->_isSearch && ! $this->_allowKeyMods
                     && $field->isPrimaryKey() )
                {
                    $fieldElement->setAttrib('readOnly', 'readOnly');
                    $class .= " readonly";
                }
                if ( strlen($class) > 0 )
                {
                    if ( $class == 'required' )
                        { $fieldElement->setRequired(true); }
                    $fieldElement->setAttrib('class', $class);
                }
                if ( strlen($title) > 0 )
                {
                    $fieldElement->setAttrib('title', $title);
                }
            }
            else    // must be a primary key, then, to be "relevant"
            {
                $fieldElement = new Zend_Form_Element_Hidden($name);
                $fieldElement->setLabel($label)
                             ->setAttrib('class', 'hidden')
                             ->addDecorator('Label',
                                array('tag'=>'div', 'class'=>'hidden'))
                             ->addDecorator('HtmlTag',
                                array('tag'=>'div', 'class'=>'hidden'));
            }
            $elements[] = $fieldElement;
            $elementNames[] = $name;
    */
        }

        $this->addElements($elements);
        // $this->addDisplayGroup($elementNames, 'dataFields');

    /*
        if ( ! $this->_isSearch )
            { $this->setDefaults($this->_activityList->getDefaults()); }
    */
    }

    /*
    protected function _getReqRecDecorations($field)
    {
        // Draw attention to required and recommended fields unless this 
        // is a Search request.
        $decorations['label'] = "";
        $decorations['class'] = "";
        $decorations['title'] = "";

        if ( $this->_isSearch )
            { return $decorations; }

        if ( $field->isAutoIncremented() )
        {
            $decorations['class'] = "identity";
            $decorations['title'] = "Auto-incremented by default";
        }
        elseif ( $field->isRequired() )
        {
            // $decorations['label'] .= "**";
            $decorations['class'] = "required";
            $decorations['title'] = "Required field";
        }
        elseif ( $field->isRecommended() && ! $field->isAutoIncremented() )
        {
            // $decorations['label'] .= ">";
            $decorations['class'] = "recommended";
            $decorations['title'] = "Recommended field";
        }

        return $decorations;
    }
     */


}

