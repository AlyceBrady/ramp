<?php
/**
 * TwitterBootstrap_Form_Decorator_Controls
 *
 * @package    TwitterBootstrap
 * @author     Michael Scholl <michael@sch0ll.de>
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @link       https://github.com/easybib/EasyBib_Form_Decorator
 */

/**
 * Controls contains the element etc.
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
class TwitterBootstrap_Form_Decorator_Controls extends Zend_Form_Decorator_HtmlTag
{
    /**
     * constructor turns decorator into a div with a css class
     * 
     * @param  array|Zend_Config $options
     */
    public function __construct($options = null)
    {
        $this->setOptions(
            array(
                'tag'   => 'div',
                'class' => TwitterBootstrap_Css::CONTROLS
            )
        );
        
        parent::__construct($options);
    }
}
