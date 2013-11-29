<?php
/**
 * TwitterBootstrap_Form_Decorator_Factory
 * 
 * @author     Michael Scholl <michael@sch0ll.de>
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @link       https://github.com/easybib/EasyBib_Form_Decorator
 */

/**
 * Applies decorators to a form in order to use the twitter bootstrap form
 * features.
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class TwitterBootstrap_Form_Decorator_Factory
{

    const FORM_STYLE_BASIC = "basic";
    const FORM_STYLE_INLINE = "inline";
    const FORM_STYLE_HORIZONTAL = "horizontal";
    
    const DECORATOR_BOOTSTRAP_ERRORS = 'BootstrapErrors';
    const DECORATOR_CONTROLS = 'Controls';
    const DECORATOR_CONTROL_GROUP = 'ControlGroup';
    const DECORATOR_BUTTON = 'Button';

    const PREFIX = 'TwitterBootstrap_Form_Decorator';
    const PREFIX_PATH = 'TwitterBootstrap/Form/Decorator';
    
    /**
     * applied form style
     * @var string
     */
    private $appliedStyle = null;

    /**
     * Constructor. Pass a form style or use basic as default
     * 
     * @param string $appliedStyle 
     */
    public function __construct($appliedStyle = null)
    {
        if ($appliedStyle === null) {
            $appliedStyle = self::FORM_STYLE_BASIC;
        }
        $this->setFormStyle($appliedStyle);
    }

    /**
     * set the form style to use for decorators
     * 
     * @param string $appliedStyle
     */
    public function setFormStyle($appliedStyle)
    {
        $allowedStyles = array(
            self::FORM_STYLE_BASIC,
            self::FORM_STYLE_INLINE,
            self::FORM_STYLE_HORIZONTAL
        );

        if (!in_array($appliedStyle, $allowedStyles)) {
            throw new InvalidArgumentException('Invalid form style ' . $appliedStyle);
        }

        $this->appliedStyle = $appliedStyle;
    }

    /**
     * returns the current form style
     * 
     * @return string
     */
    public function getFormStyle()
    {
        return $this->appliedStyle;
    }
    
    /**
     * applies all necessary decorators and css classes
     * 
     * @param Zend_Form $form 
     */
    public function applyBootstrapDecoratorsTo(Zend_Form $form)
    {
        $form->setDisableLoadDefaultDecorators(true);
        $form->setDisplayGroupDecorators($this->getDisplayGroupDecoratorConfig());
        $form->setDecorators($this->getFormDecoratorConfig());
        $form->addElementPrefixPath(self::PREFIX, self::PREFIX_PATH, Zend_Form::DECORATOR);

        $form->setElementDecorators($this->getElementDecoratorConfig());
        $this->setButtonDecorators($form);
    }
    
    /**
     * Set Button Decorators
     *
     * @param  Zend_Form $form
     */
    public function setButtonDecorators(Zend_Form $form)
    {
        foreach ($form->getElements() as $element) {
            if ($element instanceof Zend_Form_Element_Button ||
                $element instanceof Zend_Form_Element_Reset ||
                $element instanceof Zend_Form_Element_Submit
            ) {
                $element->setDecorators($this->getButtonDecoratorConfig());
            }
        }
    }
    
    /**
     * returns the config for display groups
     * 
     * @return array 
     */
    protected function getDisplayGroupDecoratorConfig()
    {
        return array('FormElements', 'Fieldset');
    }
    /**
     * returns the config for the form
     * 
     * @return array 
     */
    protected function getFormDecoratorConfig()
    {
        return array(
            'FormElements',
            array('Form', array('class' => $this->getFormCssClass()))
        );
    }

    /**
     * returns the proper css classes based on the chosen form style
     * 
     * @return string 
     */
    protected function getFormCssClass()
    {
        $classes = array(
            self::FORM_STYLE_BASIC => '',
            self::FORM_STYLE_INLINE => TwitterBootstrap_Css::FORM_INLINE,
            self::FORM_STYLE_HORIZONTAL => TwitterBootstrap_Css::FORM_HORIZONTAL,
        );
        
        return $classes[$this->getFormStyle()];
    }
    
    /**
     * config suitable for most elements
     * 
     * @return array 
     */
    protected function getGenericElementConfig()
    {
        $config = array();
        $config[] = array(
            'ViewHelper'
        );
        $config[] = array(
            self::DECORATOR_BOOTSTRAP_ERRORS,
        );
        $config[] = array(
            'Description',
            array(
                'tag' => 'p',
                'class' => TwitterBootstrap_Css::HELP_BLOCK,
            )
        );
        if ($this->getFormStyle() != self::FORM_STYLE_INLINE) {
            $config[] = array(
                self::DECORATOR_CONTROLS
            );
        }
        
        $config[] = array(
            'Label',
            array(
                'requiredSuffix' => ' * '
            )
        );
        
        if ($this->getFormStyle() != self::FORM_STYLE_INLINE) {
            $config[] = array(
                self::DECORATOR_CONTROL_GROUP,
            );
        }
        
        return $config;
    }
    
    /**
     * returns the decorator configuration for an element
     * 
     * @return array
     */
    public function getElementDecoratorConfig()
    {
        return $this->getGenericElementConfig();
    }
    
    /**
     * returns the decorator configuration for a file element
     * 
     * @return array
     */
    public function getFileElementDecoratorConfig()
    {
        $config = $this->getGenericElementConfig();
        array_shift($config);
        $first = array('File', array('class' => 'input-file'));
        array_unshift($config, $first);
        return $config;
    }

    /**
     * returns the decorator configuration for a button element, which
     * is the generic config except that 
     * - the viewhelper decorator is replaced by the button decorator
     * - and the label decorator is removed
     * 
     * @return array
     */
    public function getButtonDecoratorConfig()
    {
        $config = $this->getGenericElementConfig();
        array_shift($config);
        $first = array(self::DECORATOR_BUTTON);
        array_unshift($config, $first);
        return $this->removeDecoratorFromConfig($config, 'Label');
    }
    
    /**
     * removes a decorator config entry by name
     * 
     * @param array  $config
     * @param string $decoratorName
     * 
     * @return array 
     */
    protected function removeDecoratorFromConfig(array $config, $decoratorName)
    {
        foreach ($config as $key => $decorator) {
            if ($decorator === $decoratorName 
                || (is_array($decorator) && $decorator[0] == $decoratorName)
            ) {
                unset($config[$key]);
            }
        }
        
        return $config;
    }
}