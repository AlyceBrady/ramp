<?php
/**
 * TwitterBootstrap_Form_Decorator_ControlGroup
 *
 * @package    TwitterBootstrap
 * @author     Michael Scholl <michael@sch0ll.de>
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @link       https://github.com/easybib/EasyBib_Form_Decorator
 */

/**
 * Control group wraps the label and the controls (which contains the element etc)
 * 
 * <code>
 * <div class="control-group">
 *   <label  ...
 *   <div class="controls"  ...
 *     <input ...
 * </code>
 * 
 * @package    TwitterBootstrap
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */
class TwitterBootstrap_Form_Decorator_ControlGroup extends Zend_Form_Decorator_HtmlTag
{
    /**
     * constructor set default twitter css class
     * 
     * @param type $options 
     */
    public function __construct($options = null)
    {
        $this->setOptions(
            array(
                'tag'   => 'div',
                'class' => TwitterBootstrap_Css::CONTROL_GROUP
            )
        );
        
        parent::__construct($options);
    }
    
    /**
     * add css class "error" 
     */
    public function markAsError()
    {
        $newClass = $this->getOption('class') . ' ' . TwitterBootstrap_Css::ERROR;
        $this->setOption('class', trim($newClass));
    }
}
