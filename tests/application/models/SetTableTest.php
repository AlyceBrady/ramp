<?php
require_once 'TestConfiguration.php';
require_once 'TestSettings.php';

class models_SetTableTest extends PHPUnit_Framework_TestCase
{
    protected $_settingTests;
    protected $_basic_tableSetting_name;
    protected $_basic_tableSetting;
    protected $_variant_tableSetting;
    protected $_tableSettingWithImports;

    public function setUp()
    {
        // reset database to known state
        TestConfiguration::setupDatabase();
        $this->_settingTests = TestSettings::getInstance();

        $settingFileName = $this->_basic_tableSetting_name =
                                        TestSettings::BASIC_SETTINGS_FILE;
        $gateway = new Application_Model_TVSGateway($settingFileName);
        $this->_basic_tableSetting =
                    new Application_Model_SetTable($settingFileName, $gateway);

        $settingFileName = TestSettings::BASIC_2_SETTINGS_FILE;
        $gateway = new Application_Model_TVSGateway($settingFileName);
        $this->_variant_tableSetting =
                    new Application_Model_SetTable($settingFileName, $gateway);

        $settingFileName = TestSettings::MULT_SETTINGS_FILE;
        $settingName = 'ModifyingView';
        $gateway = new Application_Model_TVSGateway($settingFileName);
        $this->_tableSettingWithImports =
                    new Application_Model_SetTable($settingName, $gateway);
    }

    public function testSettingWithNoTableName()
    {
        // $table = new Application_Model_SetTable($settingName, $gateway);
    }

    public function testValidSettingWithTableFootnote()
    {
        $expSetting = $this->_settingTests->getBasicSetting();
        $expTableName = $expSetting['tableName'];
        $table = $this->_basic_tableSetting;

        $this->assertSame($this->_basic_tableSetting_name,
                          $table->getSettingName());
        $this->assertSame($expTableName, $table->getDbTableName());
        $this->assertSame($expSetting['tableTitle'], $table->getTitle());
        $this->assertSame($expSetting['tableDescription'],
                          $table->getDescription());
        $this->assertSame($expSetting['tableFootnote'],
                          $table->getTableFootnote());
        $this->assertSame(array(), $table->getUndefinedFieldNames());
        $this->assertSame(1, count($table->getPrimaryKeys()));
        $defaults = $table->getDefaults();
        $this->assertSame(1, count($defaults));
        $this->assertSame('The Beatles', $defaults['artist']);
        $this->assertSame(array(), $table->getTableLinkFields());
        $this->assertSame(array(), $table->getExternallyInitFields());
        $this->assertNull($table->getInitRefInfo($expTableName));
        $this->assertSame(array(), $table->getExtTableReferences());

    }

    public function testValidSettingWithNoTableFootnote()
    {
        $this->assertSame("", $this->_variant_tableSetting->getTableFootnote());
    }

    public function testGetFieldsWhenNoneImported()
    {
        $table = $this->_basic_tableSetting;
        $expSetting = $this->_settingTests->getBasicSetting();
        $this->assertSame(3, count($table->getFields()));
        $this->assertSame(array_keys($expSetting['field']),
                          array_keys($table->getFields()));
    }

    public function testGetFieldsIncludingImportedFields()
    {
        $table = $this->_tableSettingWithImports;
        $this->assertSame(10, count($table->getFields()));
    }

    public function testGetTableLinkFields()
    {
        $table = $this->_tableSettingWithImports;
        $linkFields = $table->getTableLinkFields();
        $this->assertSame(1, count($linkFields));
        $keys = array_keys($linkFields);
        $this->assertSame('userid', $keys[0]);
    }

    public function testGetNonexistentTableLinkFields()
    {
        $table = $this->_basic_tableSetting;
        $this->assertSame(0, count($table->getTableLinkFields()));
    }

    public function testVisibilityAndRelevanceWhenSomeFieldsHidden()
    {
        $table = $this->_basic_tableSetting;
        $expSetting = $this->_settingTests->getBasicSetting();

        $visibleFields = $table->getVisibleFields();
        $this->assertSame(2, count($visibleFields));

        $pkeys = $table->getPrimaryKeys();
        $this->assertSame(1, count($pkeys));

        $relevantFields = array_keys($table->getRelevantFields());
        $this->assertSame(3, count($relevantFields));
        $allFields = array_keys($expSetting['field']);
        $this->assertSame(sort($allFields), sort($relevantFields));
        $this->assertSame($table->getRelevantFields(),
                          $table->getLocalRelevantFields());
    }

    public function testVisibilityAndRelevanceWhenExplicitlyNotHidden()
    {
        $table = $this->_variant_tableSetting;
        $expSetting = $this->_settingTests->getVariantBasicSetting();

        $visibleFields = $table->getVisibleFields();
        $this->assertSame(3, count($visibleFields));

        $pkeys = $table->getPrimaryKeys();
        $this->assertSame(1, count($pkeys));

        $relevantFields = array_keys($table->getRelevantFields());
        $this->assertSame(3, count($relevantFields));
        $allFields = array_keys($expSetting['field']);
        $this->assertSame(sort($allFields), sort($relevantFields));
        $this->assertSame($table->getRelevantFields(),
                          $table->getLocalRelevantFields());
    }

    public function testVisibilityAndRelevanceWhenSomeImported()
    {
        $table = $this->_tableSettingWithImports;
        $expSetting = $this->_settingTests->getVariantBasicSetting();

        $visibleFields = $table->getVisibleFields();
        $this->assertSame(9, count($visibleFields));

        $pkeys = $table->getPrimaryKeys();
        $this->assertSame(1, count($pkeys));

        $this->assertSame(9, count($table->getRelevantFields()));
        $this->assertSame(7, count($table->getLocalRelevantFields()));
    }

    public function testRelevantFieldsWhenUnspecifiedFieldsShownByDefault()
    {
        $this->assertTrue(false);
    }

    public function testGetExternallyInitFields()
    {
        $this->assertTrue(false);
    }

    public function testTableConnectionsInitializedInInitConnections()
    {
        // initFrom?  importedFrom? selectUsing? 
        // getExternallyInitFields()? getInitRefInfo()?
        $this->assertTrue(false);
    }

    public function testTableConnectionsInitializedInInitReferences()
    {
        // initFrom?  selectUsing?
        // getExternallyInitFields()? getInitRefInfo()?
        $this->assertTrue(false);
    }

    public function testGetExternalTableRefs()
    {
        // getExtTableReferences()
        $this->assertTrue(false);
    }

    public function testGetFieldObjectInTable()
    {
        $fieldObj = $this->_tableSettingWithImports->getFieldObject('addr_id');
        $this->assertNotNull($fieldObj);
        $this->assertTrue($fieldObj->isInTable());
        $this->assertTrue($fieldObj->isDiscouraged());
    }

    public function testGetImportedFieldObject()
    {
        $table = $this->_tableSettingWithImports;
        $fieldObj = $table->getFieldObject('first_name');
        $this->assertNotNull($fieldObj);
        $this->assertFalse($fieldObj->isInTable());
        $this->assertTrue($fieldObj->isInDb());
        $this->assertSame(10, count($table->getFields()));
    }

    public function testGetNonexistentFieldObject()
    {
        $fieldObj = $this->_basic_tableSetting->getFieldObject('nonField');
        $this->assertNull($fieldObj);
    }

    public function testGetTableEntriesWithoutSpecifyingMatchInfo()
    {
        $entries = $this->_basic_tableSetting->getTableEntries();
        $this->assertSame(7, count($entries));
    }

    public function testGetTableEntriesWithSomeNullValuesNoAliasesOneRow()
    {
        // Null values indicated with null and empty string
        $data = array('id' => 1,
                      'artist' => null,
                      'title' => '');
        $entries = $this->_basic_tableSetting->getTableEntries($data);
        $this->assertSame(1, count($entries));
    }

    public function testGetTableEntriesMultRows()
    {
        // Don't use field set to ANY_VAL for matching.
        $data = array("id" => Application_Model_SetTable::ANY_VAL,
                      'artist' => 'The Beatles');
        $entries = $this->_basic_tableSetting->getTableEntries($data);
        $this->assertSame(2, count($entries));
    }

    public function testGetTableEntriesMultRowsMatchAnyType()
    {
        $data = array('id' => 1,
                      'artist' => 'The Beatles');
        $entries = $this->_basic_tableSetting->getTableEntries($data,
                                        Application_Model_SetTable::ANY);
        $this->assertSame(3, count($entries));
    }

    public function testGetTableEntriesMultRowsExcludeType()
    {
        $data = array("artist" => "The Beatles");
        $entries = $this->_basic_tableSetting->getTableEntries($data,
                                        Application_Model_SetTable::EXCLUDE);
        $this->assertSame(5, count($entries));
    }

    public function testGetTableEntriesNoRows()
    {
        $data = array('id' => 1,
                      'artist' => 'The Beatles');
        $entries = $this->_basic_tableSetting->getTableEntries($data);
        $this->assertSame(0, count($entries));
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    "is not a field"
     */
    public function testGetTableEntriesWithBadField()
    {
        $data = array('id' => 1,
                      'band' => 'The Beatles');
        $entries = $this->_basic_tableSetting->getTableEntries($data);
    }

    public function testGetTableEntriesWithAliases()
    {
        $data = array('addr_id' => 1);
        $entries = $this->_tableSettingWithImports->getTableEntries($data);
        $this->assertSame(1, count($entries));
    }

    public function testGetValidTableEntry()
    {
        $data = array('id' => 1);
        $entry = $this->_basic_tableSetting->getTableEntry($data);
        $this->assertSame('1', $entry['id']);
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    "Could not find row"
     */
    public function testGetInvalidTableEntryNoData()
    {
        $data = array('id' => 1100);
        $entry = $this->_basic_tableSetting->getTableEntry($data);
        $this->assertSame(1100, $entry['id']);
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    "does not uniquely identify a single row"
     */
    public function testGetInvalidTableEntryTooMuchData()
    {
        $data = array('artist' => 'The Beatles');
        $entry = $this->_basic_tableSetting->getTableEntry($data);
        $this->assertSame('The Beatles', $entry['artist']);
    }

    public function testGetStatusForSingleCompleteRecord()
    {
        $keys = array('id' => '1');
        $status = $this->_basic_tableSetting->getStatusOfRecord($keys);
        $this->assertSame(Application_Model_SetTable::GOOD, $status);
    }

    public function testGetStatusForSinglePartialRecord()
    {
        $this->assertTrue(false);
    }

    public function testGetStatusForNoRecord()
    {
        $keys = array('id' => '1000');
        $status = $this->_basic_tableSetting->getStatusOfRecord($keys);
        $this->assertSame(Application_Model_SetTable::BLANK, $status);
    }

    public function testKeyInfoFromNonKeyData()
    {
        $data = array('userid' => 1);
        $keyInfo = $this->_tableSettingWithImports->getKeyInfo($data);
        $this->assertSame(1, count($keyInfo));
        $this->assertSame('1', $keyInfo['addr_id']);
    }

    public function testKeyInfoFromKeyData()
    {
        $data = array('addr_id' => 1);
        $keyInfo = $this->_tableSettingWithImports->getKeyInfo($data);
        $this->assertSame(1, count($keyInfo));
        $this->assertSame('1', $keyInfo['addr_id']);
    }

    public function testFilterPrimaryKeyInfo()
    {
        $data = array('id' => 5, 'artist' => 'The Beatles',
                      'title' => 'Abbey Road');
        $pkeys = $this->_basic_tableSetting->filterPrimaryKeyInfo($data);
        $this->assertSame(1, count($pkeys));
        $this->assertSame(5, $pkeys['id']);
    }

    public function testFilterNonPrimaryKeyInfo()
    {
        $data = array('id' => 5, 'artist' => 'The Beatles',
                      'title' => 'Abbey Road');
        $pkeys = $this->_basic_tableSetting->filterPrimaryKeyInfo($data, false);
        $this->assertSame(2, count($pkeys));
        $this->assertSame('The Beatles', $pkeys['artist']);
    }

    public function testCloneableFieldsForSingleRecord()
    {
        $searchFields = array('id' => 1);
        $cloneable =
            $this->_basic_tableSetting->getCloneableFields($searchFields);
        $this->assertSame(2, count($cloneable));
    }

    public function testAddValidTableEntry()
    {
        $numEntries = count($this->_basic_tableSetting->getTableEntries());
        $data = array('id' => 15, 'artist' => 'The Beatles',
                      'title' => 'Rubber Soul');
        $entries = $this->_basic_tableSetting->getTableEntries($data);
        $this->assertSame(0, count($entries));
        $pkey = $this->_basic_tableSetting->addTableEntry($data);
        $this->assertSame(15, $pkey);
        $this->assertSame($numEntries + 1,
                          count($this->_basic_tableSetting->getTableEntries()));
        $entries = $this->_basic_tableSetting->getTableEntries($data);
        $this->assertSame(1, count($entries));
    }

    public function testAddTableEntryWithNoKeyInfo()
    {
        $numEntries = count($this->_basic_tableSetting->getTableEntries());
        $data = array('artist' => 'The Beatles',
                      'title' => 'Rubber Soul');
        $entries = $this->_basic_tableSetting->getTableEntries($data);
        $this->assertSame(0, count($entries));
        $pkey = $this->_basic_tableSetting->addTableEntry($data);
        $this->assertSame($numEntries + 1,
                          count($this->_basic_tableSetting->getTableEntries()));
        $entries = $this->_basic_tableSetting->getTableEntries($data);
        $this->assertSame(1, count($entries));
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    "insert a record with a duplicate key"
     */
    public function testAddDuplicateTableEntry()
    {
        $numEntries = count($this->_basic_tableSetting->getTableEntries());
        $data = array('id' => 5, 'artist' => 'The Beatles',
                      'title' => 'Rubber Soul');
        $pkey = $this->_basic_tableSetting->addTableEntry($data);
        $this->assertSame($numEntries,
                          count($this->_basic_tableSetting->getTableEntries()));
        $entries = $this->_basic_tableSetting->getTableEntries($data);
    }

    public function testUpdateSingleTableEntry()
    {
        $numEntries = count($this->_basic_tableSetting->getTableEntries());
        $data = array('id' => '6', 'title' => 'Red Album');
        $entries = $this->_basic_tableSetting->getTableEntries($data);
        $this->assertSame(0, count($entries));

        $numUpdated = $this->_basic_tableSetting->updateTableEntry($data);

        $this->assertSame(1, $numUpdated);
        $this->assertSame($numEntries,
                          count($this->_basic_tableSetting->getTableEntries()));
        $entries = $this->_basic_tableSetting->getTableEntries($data);
        $this->assertSame(1, count($entries));
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    "should affect only one table entry"
     */
    public function testConstructUniqueWhereWithNonUniqueData()
    {
        $data = array('artist' => 'The Beatles');
        $numUpdated = $this->_basic_tableSetting->updateTableEntry($data);
    }

    public function testDeleteSingleTableEntry()
    {
        $numEntries = count($this->_basic_tableSetting->getTableEntries());
        $data = array('id' => '6');
        $entries = $this->_basic_tableSetting->getTableEntries($data);
        $this->assertSame(1, count($entries));

        $numUpdated = $this->_basic_tableSetting->deleteTableEntry($data);

        $this->assertSame(1, $numUpdated);
        $this->assertSame($numEntries - 1,
                          count($this->_basic_tableSetting->getTableEntries()));
        $entries = $this->_basic_tableSetting->getTableEntries($data);
        $this->assertSame(0, count($entries));
    }

    public function testUndefinedFields()
    {
        // $this->assertTrue(false);
    }

    public function testTableConnectionHasInvalidTable()
    {
        $this->assertTrue(false);
    }

    public function testTableConnectionWithFullyQualifiedFormat()
    {
        $this->assertTrue(false);
    }

    public function testTableConnectionWithoutFullyQualifiedFormat()
    {
        $this->assertTrue(false);
    }

    public function testInvalidImportBecauseNoConnectionClause()
    {
        // Constructor should throw exception (_initConnections() and/or 
        // _categorizeField()
        $this->assertTrue(false);
    }

    public function testSimpleExternalRef()
    {
        $this->assertTrue(false);
    }

    public function testBadExternalRefBecauseHasNoSubProperties()
    {
        $this->assertTrue(false);
    }

    public function testSuiteIsComplete()
    {
        $this->assertTrue(false);
    }

}
