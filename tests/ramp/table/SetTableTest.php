<?php
require_once 'TestConfiguration.php';
require_once 'TestSettings.php';

// TODO: This test suite has not been updated to test search comarison 
// operators other than (=) (equality checks).  Nor are there any methods
// to test the getDependentTables method when there aren't dependent tables.
//
// TODO: Has not been updated to test handling of blockEntry.label, 
// blockEntry.count, and blockEntry.field.

class models_SetTableTest extends PHPUnit_Framework_TestCase
{
    const BAD_TC_FORMAT_1 = 'settingTests/badTableConnectionFormat1';
    const BAD_TC_FORMAT_2 = 'settingTests/badTableConnectionFormat2';
    const BAD_TC_FORMAT_3 = 'settingTests/badTableConnectionFormat3';
    const BAD_TC_TBL = 'settingTests/badTableConnectionTable';
    const BAD_TC_ATTRIBUTE = "settingTests/badTableConnectionAttribute";
    const IMPORT_W_NO_TC = "settingTests/badImportWithNoConnectionClause";
    const TC_WITH_ALIAS = "settingTests/TableConnectionWithAlias";

    protected $_settingTests;
    protected $_basic_setTable_name;
    protected $_basic_setTable;
    protected $_variant_setTable;
    protected $_setTableWithImports;
    protected $_setTableShowingColsByDefault;
    protected $_setTableWithInitAndExtRef;
    protected $_setTableWithBadInitAndExtRef;
    protected $_settingForPartialTable;
    protected $_setTableWithDependentTables;

    public function setUp()
    {
        // reset database to known state
        TestConfiguration::setupDatabase();

        $this->_settingTests = TestSettings::getInstance();

        $settingFileName = $this->_basic_setTable_name =
                                        TestSettings::BASIC_SETTINGS_FILE;
        $gateway = new Ramp_Table_TVSGateway($settingFileName);
        $this->_basic_setTable =
                    new Ramp_Table_SetTable($settingFileName, $gateway);

        $settingFileName = TestSettings::BASIC_2_SETTINGS_FILE;
        $gateway = new Ramp_Table_TVSGateway($settingFileName);
        $this->_variant_setTable =
                    new Ramp_Table_SetTable($settingFileName, $gateway);

        $settingFileName = TestSettings::MULT_SETTINGS_FILE;
        $settingName = 'ModifyingView';
        $gateway = new Ramp_Table_TVSGateway($settingFileName);
        $this->_setTableWithImports =
                    new Ramp_Table_SetTable($settingName, $gateway);

        $settingFileName = TestSettings::FILE_WITH_EXTERNAL_INIT;
        $settingName = 'ModifyingView';
        $gateway = new Ramp_Table_TVSGateway($settingFileName);
        $this->_setTableWithInitAndExtRef =
                    new Ramp_Table_SetTable($settingName, $gateway);

        $settingFileName = TestSettings::FILE_WITH_EXTERNAL_INIT;
        $settingName = 'DetailedView';
        $gateway = new Ramp_Table_TVSGateway($settingFileName);
        $this->_setTableWithBadInitAndExtRef =
                    new Ramp_Table_SetTable($settingName, $gateway);

        $settingFileName = TestSettings::MULT_SETTINGS_FILE;
        $settingName = 'DetailedView';
        $gateway = new Ramp_Table_TVSGateway($settingFileName);
        $this->_settingForPartialTable =
                    new Ramp_Table_SetTable($settingName, $gateway);

        $settingFileName = TestSettings::FILE_WITH_EXTERNAL_FILES;
        $settingName = 'TestSetting';
        $gateway = new Ramp_Table_TVSGateway($settingFileName);
        $this->_setTableWithDependentTables =
                    new Ramp_Table_SetTable($settingName, $gateway);

    }

    public function testSettingWithNoTableName()
    {
        // Test constructing a table setting with table setting
        // properties, but no table specified.
        $this->setExpectedException('Exception',
                                    'setting must include a key');
        $settingFileName = TestSettings::NO_TABLE_SETTINGS_FILE;
        $settingName = 'TableSetting';
        $gateway = new Ramp_Table_TVSGateway($settingFileName);
        $table = new Ramp_Table_SetTable($settingName, $gateway);
    }

    public function testSettingWithInheritedTableName()
    {
        $table = $this->_settingForPartialTable;
        $this->assertSame("ramp_test_addresses", $table->getDbTableName());
    }

    public function testValidSettingWithTableFootnote()
    {
        $expSetting = $this->_settingTests->getBasicSetting();
        $expTableName = $expSetting['tableName'];
        $table = $this->_basic_setTable;

        $this->assertSame($this->_basic_setTable_name,
                          $table->getSettingName());
        $this->assertSame($expTableName, $table->getDbTableName());
        $this->assertSame($expSetting['tableTitle'], $table->getTitle());
        $this->assertSame($expSetting['tableDescription'],
                          $table->getDescription());
        $this->assertSame($expSetting['tableFootnote'],
                          $table->getTableFootnote());
    }

    public function testValidSettingWithNoTableFootnote()
    {
        $this->assertSame("", $this->_variant_setTable->getTableFootnote());
    }

    // Most information about table connections is not checked when first
    // read in; invalid table connections show up when the connection is 
    // used to retrieve information, so those tests show up below with 
    // the other getTableEntries tests.

    public function testTableConnectionWithNoConnectionAttribute()
    {
        $this->setExpectedException('Exception',
                                    'does not have the required format');
        $settingFileName = self::BAD_TC_ATTRIBUTE;
        $gateway = new Ramp_Table_TVSGateway($settingFileName);
        $table = new Ramp_Table_SetTable($settingFileName, $gateway);
    }

    public function testInitReferenceWithSingleMatchField()
    {
        $table = $this->_setTableWithInitAndExtRef;
        $ref = $table->getInitRefInfo('ramp_auth_users');
        $this->assertSame('Users', $ref->getViewingSeqName());
        $this->assertSame(array('id' => 'userid'),
                          $ref->getConnectionExpressions());
    }

    public function testInitReferenceWithMultipleMatchFields()
    {
        $table = $this->_setTableWithInitAndExtRef;
        $ref = $table->getInitRefInfo('albums');
        $this->assertSame('BasicTableSetting', $ref->getViewingSeqName());
        $this->assertSame(array('id' => 'album_id', 'artist' => 'artist'),
                          $ref->getConnectionExpressions());
    }

    public function testInitReferenceForBadTableName()
    {
        $table = $this->_setTableWithInitAndExtRef;
        $ref = $table->getInitRefInfo('nonExistentTable');
        $this->assertNull($ref);
    }

    public function testGetExternalTableRefs()
    {
        $table = $this->_setTableWithInitAndExtRef;
        $refs = $table->getExtTableReferences();
        $this->assertSame(1, count($refs));
        $ref = $refs["ramp_auth_users"];
        $this->assertSame('Users', $ref->getViewingSeqName());
        $this->assertSame('Users', $ref->getTitle());
        $this->assertSame(array('id' => 'userid'),
                          $ref->getConnectionExpressions());
    }

    public function testGetDependentTables()
    {
        $table = $this->_setTableWithDependentTables;
        $tbls = $table->getDependentTables();
        $expTables = $this->_settingTests->getDataSourceTables();
        $this->assertSame($expTables, $tbls);
    }

    public function testGetFieldsFromSimpleTable()
    {
        $table = $this->_basic_setTable;
        $expSetting = $this->_settingTests->getBasicSetting();
        $this->assertSame(3, count($table->getFields()));
        $this->assertSame(array_keys($expSetting['field']),
                          array_keys($table->getFields()));
    }

    public function testGetSimpleFieldCollections()
    {
        $expSetting = $this->_settingTests->getBasicSetting();
        $table = $this->_basic_setTable;

        $this->assertSame(array(), $table->getUndefinedFieldNames());
        $this->assertSame(1, count($table->getPrimaryKeys()));
        $defaults = $table->getDefaults();
        $this->assertSame(1, count($defaults));
        $this->assertSame('The Beatles', $defaults['artist']);
        $this->assertSame(array(), $table->getTableLinkFields());
        $this->assertSame(array(), $table->getExternallyInitFields());
        $this->assertSame(array(), $table->getExtTableReferences());
    }

    public function testGetTableLinkFields()
    {
        // Tests links established with selectUsing attributes, these
        // are "foreign key" fields that establish a link to another 
        // table (used for matching in JOIN expressions).
        $table = $this->_setTableWithImports;
        $linkFields = $table->getTableLinkFields();
        $this->assertSame(1, count($linkFields));
        $keys = array_keys($linkFields);
        $this->assertSame('userid', $keys[0]);
    }

    public function testGetNonexistentTableLinkFields()
    {
        // Tests getTableLinkFields on a table with no selectUsing attributes
        $table = $this->_basic_setTable;
        $this->assertSame(0, count($table->getTableLinkFields()));
    }

    public function testGetExternallyInitFields()
    {
        $table = $this->_setTableWithInitAndExtRef;
        $initFieldInfo = $table->getExternallyInitFields();
        $initFields = array_keys($initFieldInfo);
        $this->assertSame(array('fname', 'artist'), $initFields);
    }

    public function testGetFieldsIncludingImportedFields()
    {
        // Fields that come from other tables as a result of JOIN expressions.
        $table = $this->_setTableWithImports;
        $this->assertSame(10, count($table->getFields()));
    }

    public function testInvalidImportBecauseNoConnectionClause()
    {
        $this->setExpectedException('Exception',
                                    "there is no 'tableConnection' clause");
        $settingFileName = self::IMPORT_W_NO_TC;
        $gateway = new Ramp_Table_TVSGateway($settingFileName);
        $table = new Ramp_Table_SetTable($settingFileName, $gateway);
    }

    public function testInvalidInitBecauseNoInitRefsForTable()
    {
        $table = $this->_setTableWithBadInitAndExtRef;
        $badField = $table->getFieldObject('title');
        $sourceTable = $badField->getInitTableName();
        $this->assertNull($table->getInitRefInfo($sourceTable));
    }

    public function testGetLocalFieldObjectInTable()
    {
        $fieldObj = $this->_setTableWithImports->getFieldObject('addr_id');
        $this->assertNotNull($fieldObj);
        $this->assertTrue($fieldObj->isInTable());
        $this->assertTrue($fieldObj->isDiscouraged());
    }

    public function testGetInheritedFieldObjectInTable()
    {
        $fieldObj = $this->_settingForPartialTable->getFieldObject('addr_id');
        $this->assertNotNull($fieldObj);
        $this->assertTrue($fieldObj->isInTable());
        $this->assertSame('Addr ID', $fieldObj->getLabel());
        $this->assertSame('', $fieldObj->getFieldFootnote());
        $this->assertFalse($fieldObj->isDiscouraged());
    }

    public function testGetPartiallyInheritedFieldObjectInTable()
    {
        $fieldObj = $this->_setTableWithImports->getFieldObject('addr_id');
        $this->assertNotNull($fieldObj);
        $this->assertTrue($fieldObj->isInTable());
        $this->assertSame('Addr ID', $fieldObj->getLabel());
        $this->assertSame('set automatically; do not update!',
                          $fieldObj->getFieldFootnote());
        $this->assertTrue($fieldObj->isDiscouraged());
    }

    public function testGetImportedFieldObject()
    {
        $table = $this->_setTableWithImports;
        $fieldObj = $table->getFieldObject('first_name');
        $this->assertNotNull($fieldObj);
        $this->assertFalse($fieldObj->isInTable());
        $this->assertTrue($fieldObj->isInDb());
        $this->assertSame(10, count($table->getFields()));
    }

    public function testGetNonexistentFieldObject()
    {
        $fieldObj = $this->_basic_setTable->getFieldObject('nonField');
        $this->assertNull($fieldObj);
    }

    public function testVisibilityAndRelevanceWhenSomeFieldsHidden()
    {
        $table = $this->_basic_setTable;
        $expSetting = $this->_settingTests->getBasicSetting();

        // Note: primary key is not visible in this case.  All fields 
        // are visible or primary.
        $this->assertSame(2, count($table->getVisibleFields()));
        $this->assertSame(1, count($table->getPrimaryKeys()));
        $relevantFields = array_keys($table->getRelevantFields());
        $this->assertSame(3, count($relevantFields));
        $allFields = array_keys($expSetting['field']);
        $this->assertSame(sort($allFields), sort($relevantFields));
        $this->assertSame($table->getRelevantFields(),
                          $table->getLocalRelevantFields());
    }

    public function testVisibilityAndRelevanceWhenExplicitlyNotHidden()
    {
        $table = $this->_variant_setTable;
        $expSetting = $this->_settingTests->getVariantBasicSetting();

        // Note: primary key is visible in this case.  All fields are 
        // visible or primary.
        $this->assertSame(3, count($table->getVisibleFields()));
        $this->assertSame(1, count($table->getPrimaryKeys()));
        $relevantFields = array_keys($table->getRelevantFields());
        $this->assertSame(3, count($relevantFields));
        $allFields = array_keys($expSetting['field']);
        $this->assertSame(sort($allFields), sort($relevantFields));
        $this->assertSame($table->getRelevantFields(),
                          $table->getLocalRelevantFields());
    }

    public function testVisibilityAndRelevanceWhenSomeImported()
    {
        $table = $this->_setTableWithImports;

        // Setting has 8 local fields (1 is primary).  1 non-primary key 
        // is hidden.  2 additional fields are imported.
        $this->assertSame(9, count($table->getVisibleFields()));
        $this->assertSame(1, count($table->getPrimaryKeys()));
        $this->assertSame(9, count($table->getRelevantFields()));
        $this->assertSame(7, count($table->getLocalRelevantFields()));
    }

    public function testRelevantFieldsWhenUnspecifiedFieldsShownByDefault1()
    {
        $settingFileName = TestSettings::FILE_SHOWING_COLS_BY_DEFAULT;
        $settingName = 'DetailedView';
        $gateway = new Ramp_Table_TVSGateway($settingFileName);
        $table = new Ramp_Table_SetTable($settingName, $gateway);

        // Setting has 8 local fields (1 is primary) and 2 imported 
        // fields.  No fields are explicitly hidden.
        $this->assertSame(10, count($table->getRelevantFields()));
    }

    public function testRelevantFieldsWhenUnspecifiedFieldsShownByDefault2()
    {
        $settingFileName = TestSettings::FILE_SHOWING_COLS_BY_DEFAULT;
        $settingName = 'ModifyingView';
        $gateway = new Ramp_Table_TVSGateway($settingFileName);
        $table = new Ramp_Table_SetTable($settingName, $gateway);

        // Setting has 8 local fields (1 is primary) and 1 imported 
        // fields.  No fields are explicitly hidden.
        $this->assertSame(9, count($table->getRelevantFields()));
    }

    public function testGetTableEntriesWithoutSpecifyingMatchInfo()
    {
        $entries = $this->_basic_setTable->getTableEntries();
        $this->assertSame(7, count($entries));
    }

    public function testGetTableEntriesWithSomeNullValuesNoAliasesOneRow()
    {
        // Null values indicated with null and empty string
        $data = array('id' => 1,
                      'artist' => null,
                      'title' => '');
        $entries = $this->_basic_setTable->getTableEntries($data);
        $this->assertSame(1, count($entries));
    }

    public function testGetTableEntriesMultRows()
    {
        $data = array('artist' => 'The Beatles');
        $entries = $this->_basic_setTable->getTableEntries($data);
        $this->assertSame(2, count($entries));
    }

    public function testGetTableEntriesMultRowsWithAnyVal()
    {
        $data = array("id" => Ramp_Table_SetTable::ANY_VAL,
                      'artist' => 'The Beatles');
        $entries = $this->_basic_setTable->getTableEntries($data);
        $this->assertSame(2, count($entries));
    }

    public function testGetTableEntriesMultRowsMatchAnyType()
    {
        $data = array('id' => 1,
                      'artist' => 'The Beatles');
        $entries = $this->_basic_setTable->getTableEntries($data, array(), 
                                        Ramp_Table_SetTable::ANY);
        $this->assertSame(3, count($entries));
    }

    /*  The EXCLUDE match type has been removed.
    public function testGetTableEntriesMultRowsExcludeType()
    {
        $data = array("artist" => "The Beatles");
        $entries = $this->_basic_setTable->getTableEntries($data, null,
                                        Ramp_Table_SetTable::EXCLUDE);
        $this->assertSame(5, count($entries));
    }
     */

    public function testGetTableEntriesNoRows()
    {
        $data = array('id' => 1,
                      'artist' => 'The Beatles');
        $entries = $this->_basic_setTable->getTableEntries($data);
        $this->assertSame(0, count($entries));
    }

    public function testGetTableEntriesWithBadField()
    {
        $this->setExpectedException('Exception', 'is not a field');
        $data = array('id' => 1,
                      'band' => 'The Beatles');
        $entries = $this->_basic_setTable->getTableEntries($data);
    }

    public function testGetTableEntriesIncludingImportedFields()
    {
        $data = array('userid' => 1);
        $entries = $this->_setTableWithImports->getTableEntries($data);
        $this->assertSame(1, count($entries));
        $entry = $entries[0];
        $this->assertSame('Charlie', $entry['first_name']);
    }

    public function testGetTableEntriesIncludingImportedFieldsWithAliases()
    {
        $settingFileName = self::TC_WITH_ALIAS;
        $gateway = new Ramp_Table_TVSGateway($settingFileName);
        $table = new Ramp_Table_SetTable($settingFileName, $gateway);

        $data = array('userid' => 1);
        $entries = $table->getTableEntries($data);
        $this->assertSame(1, count($entries));
        $entry = $entries[0];
        $this->assertSame('Charlie', $entry['first_name']);
    }

    public function testGetTableEntriesIncludingImportedRenamedFields()
    {
        $data = array('addr_id' => 1);
        $entries = $this->_setTableWithImports->getTableEntries($data);
        $this->assertSame(1, count($entries));
        $entry = $entries[0];
        $this->assertSame('Brown', $entry['lastname']);
    }

    public function testGetDataWhenTableConnectionHasBadTable()
    {
        $this->setExpectedException('Exception', 'is not a table');
        $data = array('addr_id' => 1);
        $settingFileName = self::BAD_TC_TBL;
        $gateway = new Ramp_Table_TVSGateway($settingFileName);
        $table = new Ramp_Table_SetTable($settingFileName, $gateway);
        $entries = $table->getTableEntries($data);
        $this->assertSame('Brown', $entries[0]['last_name']);
    }

    public function testBadlyFormattedTableConnection1()
    {
        $data = array('addr_id' => 1);
        $settingFileName = self::BAD_TC_FORMAT_1;
        $gateway = new Ramp_Table_TVSGateway($settingFileName);
        $table = new Ramp_Table_SetTable($settingFileName, $gateway);
        $entries = $table->getTableEntries($data);
        $this->assertSame('Brown', $entries[0]['last_name']);
    }

    public function testBadlyFormattedTableConnection2()
    {
        $this->setExpectedException('Exception', 'Invalid data request');
        $data = array('addr_id' => 1);
        $settingFileName = self::BAD_TC_FORMAT_2;
        $gateway = new Ramp_Table_TVSGateway($settingFileName);
        $table = new Ramp_Table_SetTable($settingFileName, $gateway);
        $entries = $table->getTableEntries($data);
        $this->assertSame('Brown', $entries[0]['last_name']);
    }

    public function testBadlyFormattedTableConnection3()
    {
        $this->setExpectedException('Exception', 'Invalid data request');
        $data = array('addr_id' => 1);
        $settingFileName = self::BAD_TC_FORMAT_3;
        $gateway = new Ramp_Table_TVSGateway($settingFileName);
        $table = new Ramp_Table_SetTable($settingFileName, $gateway);
        $entries = $table->getTableEntries($data);
        $this->assertSame('Brown', $entries[0]['last_name']);
    }

    public function testGetValidTableEntry()
    {
        $data = array('id' => 1);
        $entry = $this->_basic_setTable->getTableEntry($data);
        $this->assertSame('1', $entry['id']);
    }

    public function testGetInvalidTableEntryNoData()
    {
        $this->setExpectedException('Exception', 'Could not find row');
        $data = array('id' => 1100);
        $entry = $this->_basic_setTable->getTableEntry($data);
        $this->assertSame(1100, $entry['id']);
    }

    public function testGetInvalidTableEntryTooMuchData()
    {
        $this->setExpectedException('Exception',
                                    'does not uniquely identify a single row');
        $data = array('artist' => 'The Beatles');
        $entry = $this->_basic_setTable->getTableEntry($data);
        $this->assertSame('The Beatles', $entry['artist']);
    }

    public function testGetStatusForSingleCompleteRecord()
    {
        $keys = array('id' => '1');
        $status = $this->_basic_setTable->getStatusOfRecord($keys);
        $this->assertSame(Ramp_Table_SetTable::GOOD, $status);
    }

    public function testGetStatusForSinglePartialRecord()
    {
        $keys = array('addr_id' => '1');
        $status = $this->_settingForPartialTable->getStatusOfRecord($keys);
        $this->assertSame(Ramp_Table_SetTable::PARTIAL, $status);
    }

    public function testGetStatusForNoRecord()
    {
        $keys = array('id' => '1000');
        $status = $this->_basic_setTable->getStatusOfRecord($keys);
        $this->assertSame(Ramp_Table_SetTable::BLANK, $status);
    }

    public function testGetStatusBasedOnBadKeys()
    {
        $this->setExpectedException('Exception', 'not a field in this setting');
        $keys = array('nonKey' => '1000');
        $status = $this->_basic_setTable->getStatusOfRecord($keys);
        $this->assertSame(Ramp_Table_SetTable::BLANK, $status);
    }

    public function testKeyInfoFromNonKeyData()
    {
        $data = array('userid' => 1);
        $keyInfo = $this->_setTableWithImports->getKeyInfo($data);
        $this->assertSame(1, count($keyInfo));
        $this->assertSame('1', $keyInfo['addr_id']);
    }

    public function testKeyInfoFromKeyData()
    {
        $data = array('addr_id' => 1);
        $keyInfo = $this->_setTableWithImports->getKeyInfo($data);
        $this->assertSame(1, count($keyInfo));
        $this->assertSame('1', $keyInfo['addr_id']);
    }

    public function testFilterPrimaryKeyInfo()
    {
        $data = array('id' => 5, 'artist' => 'The Beatles',
                      'title' => 'Abbey Road');
        $pkeys = $this->_basic_setTable->filterPrimaryKeyInfo($data);
        $this->assertSame(1, count($pkeys));
        $this->assertSame(5, $pkeys['id']);
    }

    public function testFilterNonPrimaryKeyInfo()
    {
        $data = array('id' => 5, 'artist' => 'The Beatles',
                      'title' => 'Abbey Road');
        $pkeys = $this->_basic_setTable->filterPrimaryKeyInfo($data, false);
        $this->assertSame(2, count($pkeys));
        $this->assertSame('The Beatles', $pkeys['artist']);
    }

    public function testCloneableFieldsForSingleRecord()
    {
        $searchFields = array('id' => 1);
        $cloneable =
            $this->_basic_setTable->getCloneableFields($searchFields);
        $this->assertSame(2, count($cloneable));
    }

    public function testAddValidTableEntry()
    {
        $numEntries = count($this->_basic_setTable->getTableEntries());
        $data = array('id' => 15, 'artist' => 'The Beatles',
                      'title' => 'Rubber Soul');
        $entries = $this->_basic_setTable->getTableEntries($data);
        $this->assertSame(0, count($entries));
        $pkey = $this->_basic_setTable->addTableEntry($data);
        $this->assertSame(15, $pkey);
        $this->assertSame($numEntries + 1,
                          count($this->_basic_setTable->getTableEntries()));
        $entries = $this->_basic_setTable->getTableEntries($data);
        $this->assertSame(1, count($entries));
    }

    public function testAddTableEntryWithNoKeyInfo()
    {
        $numEntries = count($this->_basic_setTable->getTableEntries());
        $data = array('artist' => 'The Beatles',
                      'title' => 'Rubber Soul');
        $entries = $this->_basic_setTable->getTableEntries($data);
        $this->assertSame(0, count($entries));
        $pkey = $this->_basic_setTable->addTableEntry($data);
        $this->assertSame($numEntries + 1,
                          count($this->_basic_setTable->getTableEntries()));
        $entries = $this->_basic_setTable->getTableEntries($data);
        $this->assertSame(1, count($entries));
    }

    public function testAddDuplicateTableEntry()
    {
        $this->setExpectedException('Exception',
                                    'insert a record with a duplicate key');
        $numEntries = count($this->_basic_setTable->getTableEntries());
        $data = array('id' => 5, 'artist' => 'The Beatles',
                      'title' => 'Rubber Soul');
        $pkey = $this->_basic_setTable->addTableEntry($data);
        $this->assertSame($numEntries,
                          count($this->_basic_setTable->getTableEntries()));
        $entries = $this->_basic_setTable->getTableEntries($data);
    }

    public function testUpdateSingleTableEntry()
    {
        $numEntries = count($this->_basic_setTable->getTableEntries());
        $data = array('id' => '6', 'title' => 'Red Album');
        $entries = $this->_basic_setTable->getTableEntries($data);
        $this->assertSame(0, count($entries));

        $numUpdated = $this->_basic_setTable->updateTableEntry($data);

        $this->assertSame(1, $numUpdated);
        $this->assertSame($numEntries,
                          count($this->_basic_setTable->getTableEntries()));
        $entries = $this->_basic_setTable->getTableEntries($data);
        $this->assertSame(1, count($entries));
    }

    public function testConstructUniqueWhereWithNonUniqueData()
    {
        $this->setExpectedException('Exception',
                                    'should affect only one table entry');
        $data = array('artist' => 'The Beatles');
        $numUpdated = $this->_basic_setTable->updateTableEntry($data);
    }

    public function testDeleteSingleTableEntry()
    {
        $numEntries = count($this->_basic_setTable->getTableEntries());
        $data = array('id' => '6');
        $entries = $this->_basic_setTable->getTableEntries($data);
        $this->assertSame(1, count($entries));

        $numUpdated = $this->_basic_setTable->deleteTableEntry($data);

        $this->assertSame(1, $numUpdated);
        $this->assertSame($numEntries - 1,
                          count($this->_basic_setTable->getTableEntries()));
        $entries = $this->_basic_setTable->getTableEntries($data);
        $this->assertSame(0, count($entries));
    }

}
