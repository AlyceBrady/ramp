<?php
/**
 * TwitterBootstrap_Form_Decorator_Button
 *
 * @author     Michael Scholl <michael@sch0ll.de>
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @link       https://github.com/easybib/EasyBib_Form_Decorator
 */

/**
 * Decorator form submit / button, applies the css styles on rendering
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class TwitterBootstrap_Form_Decorator_Button extends Zend_Form_Decorator_ViewHelper
{
    /**
     * applies the button decorator classes
     * 
     * @param string $content
     * 
     * @return string
     */
    public function render($content)
    {

        $class = $this->getElement()->getAttrib('class');
        $class .= ' ' . TwitterBootstrap_Css::BTN;

        if ($this->isElementUsedAsSubmit()) {
            $class .= ' ' . TwitterBootstrap_Css::BTN_PRIMARY;
        }

        $this->getElement()->setAttrib('class', $this->uniqueClasses($class));

        return parent::render($content);
    }

    /**
     * check if the element is used as submit button
     * 
     * @return boolean 
     */
    protected function isElementUsedAsSubmit()
    {
        $element  = $this->getElement();
        $isSubmit = false;
        
        if ($element instanceof Zend_Form_Element_Button) {
            $attributes = $this->getElementAttribs();
            if (isset($attributes['type']) && strtolower($attributes['type']) == 'submit') {
                $isSubmit = true;
            }
        } elseif ($element instanceof Zend_Form_Element_Submit) {
            $isSubmit = true;
        }

        return $isSubmit;
    }

    /**
     * takes care the automatically applied classes have not been assigned twice
     * 
     * @param string $classes
     * 
     * @return string
     */
    protected function uniqueClasses($classes)
    {
        $classes = explode(' ', $classes);
        return implode(' ', array_unique($classes));
    }

}