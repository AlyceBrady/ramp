<?php

class models_TVSFactoryTest extends PHPUnit_Framework_TestCase
{
    const BASIC_FILE = TestSettings::BASIC_SETTINGS_FILE;
    const MULT_SETTINGS = TestSettings::MULT_SETTINGS_FILE;

    protected $_factory;

    public function setUp()
    {
        $this->_factory = Ramp_Table_TVSFactory::getInstance();
    }

    public function testGetValidSettingFile()
    {
        $sequence = $this->_factory->getSequenceOrSetting(self::BASIC_FILE);
    }

    public function testGetValidSequenceFile()
    {
        $sequence = $this->_factory->getSequenceOrSetting(self::MULT_SETTINGS);
    }

    public function testGetSettingFileWhenNameIsNull()
    {
        $this->setExpectedException('Exception', 'Missing settings file');
        $sequence = $this->_factory->getSequenceOrSetting(null);
    }

    public function testGetSettingFileWhenNameIsEmpty()
    {
        $this->setExpectedException('Exception', 'Missing settings file');
        $sequence = $this->_factory->getSequenceOrSetting("");
    }

    public function testGetSettingFileForUnknownSettingName()
    {
        $this->setExpectedException('Exception', 'Missing settings file');
        $sequence = $this->_factory->getSequenceOrSetting("invalidSetting");
    }

    public function testGetSettingFileAlreadyReadIn()
    {
        $sequence = $this->_factory->getSequenceOrSetting(self::BASIC_FILE);
    }

    public function testGetSequenceFileAlreadyReadIn()
    {
        $sequence = $this->_factory->getSequenceOrSetting(self::MULT_SETTINGS);
    }

}
