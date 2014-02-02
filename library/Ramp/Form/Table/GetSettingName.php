<?php

/**
 * RAMP: Records and Activity Management Program
 *
 * LICENSE
 *
 * This source file is subject to the BSD-2-Clause license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.cs.kzoo.edu/ramp/LICENSE.txt
 *
 * @category   Ramp
 * @package    Ramp_Forms
 * @copyright  Copyright (c) 2012-2014 Alyce Brady
 *             (http://www.cs.kzoo.edu/~abrady)
 * @license    http://www.cs.kzoo.edu/ramp/LICENSE.txt   Simplified BSD License
 *
 */

class Ramp_Form_Table_GetSettingName extends Zend_Form
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

        $this->addElements(array($filename));
    }

}

