<?php
/**
 * TwitterBootstrap_Form_Decorator_BootstrapErrors
 *
 * @author     Michael Scholl <michael@sch0ll.de>
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @link       https://github.com/easybib/EasyBib_Form_Decorator
 */

/**
 * Uses the formErrors view helper to render errors in a span. Marks the
 * control group decorator as erroneous if present.
 *
 * @author     Michael Scholl <michael@sch0ll.de>
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @link       https://github.com/easybib/EasyBib_Form_Decorator
 */
class TwitterBootstrap_Form_Decorator_BootstrapErrors extends Zend_Form_Decorator_HtmlTag
{
    /**
     * Render content wrapped in an HTML tag
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        $view    = $element->getView();
        if (null === $view) {
            return $content;
        }

        $errors = $element->getMessages();
        if (empty($errors)) {
            return $content;
        }
        
        $controlGroupDecorator = $element->getDecorator(TwitterBootstrap_Form_Decorator_Factory::DECORATOR_CONTROL_GROUP);
        if ($controlGroupDecorator instanceof TwitterBootstrap_Form_Decorator_ControlGroup) {
            $controlGroupDecorator->markAsError();
        }

        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $formErrorHelper = $view->getHelper('formErrors');
        $formErrorHelper->setElementStart('<span%s>')
            ->setElementSeparator(' | ')
            ->setElementEnd('</span>');
        $errors = $formErrorHelper->formErrors($errors, array('class' => TwitterBootstrap_Css::HELP_INLINE));

        switch ($placement) {
            case self::PREPEND:
                return $errors . $separator . $content;
            case self::APPEND:
            default:
                return $content . $separator . $errors;
        }
    }
}