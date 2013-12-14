<?php

class Application_Form_GetSettingName extends Zend_Form
{
    const SETTING_NAME = 'settingFilename';

    public function init()
    {
        $this->setName('getSettingFilename');

        // Get the root directory for setting sequence filenames.
        $configs = Ramp_RegistryFacade::getInstance();
        $rootDir = $configs->getSettingsDirectory();

        $filename = new Zend_Form_Element_Text(self::SETTING_NAME);
        $prompt = "Enter setting sequence filename (relative to " .
            "root settings directory $rootDir):";
        $filename->setLabel($prompt)
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Check Syntax')
                ->setAttrib('index','submitbutton');

        $this->addElements(array($filename, $submit));
    }

}

