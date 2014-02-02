<?php

class models_ExternalTableRefTest extends PHPUnit_Framework_TestCase
{
    protected $_init_tbl;

    protected $_match1;
    protected $_connection1;
    protected $_data1;
    protected $_trans1;
    protected $_match2;
    protected $_connection2;
    protected $_data2;
    protected $_trans2;
    protected $_connection12;
    protected $_data12;
    protected $_trans12;

    public function setUp()
    {
        // $this->_init_tbl 
        $this->_match1 = array("localField" => "album_id",
                               "externalField" => "id");
        $this->_connection1 = array("id" => "album_id");
        $this->_data1 = array("album_id" => 1);
        $this->_trans1 = array("id" => 1);
        $this->_match2 = array("localField" => "artist",
                               "externalField" => "last_name");
        $this->_connection2 = array("last_name" => "artist");
        $this->_data2 = array("artist" => 'Lennon');
        $this->_trans2 = array("last_name" => 'Lennon');
        $this->_connection12 = array("id" => "album_id",
                                    "last_name" => "artist");
        $this->_data12 = array("album_id" => 1, "artist" => 'Lennon');
        $this->_trans12 = array("id" => 1, "last_name" => 'Lennon');
    }

    public function testExtRefHasNoReferenceInfo()
    {
        $this->setExpectedException('Exception',
                                    'without a viewingSequence property');
        $spec = new Ramp_Table_ExternalTableReference(null);
        return $spec;
    }

    public function testExtRefHasNoViewingSequence()
    {
        $this->setExpectedException('Exception',
                                    'without a viewingSequence property');
        $spec = new Ramp_Table_ExternalTableReference(array());
        return $spec;
    }

    public function testValidExternalRefWithSingleMatchingField()
    {
        $refInfo = array("viewingSequence" => "Users") + $this->_match1;
        $ref = new Ramp_Table_ExternalTableReference($refInfo);
        $this->assertSame('Users', $ref->getViewingSeqName());
        $this->assertSame('Users', $ref->getTitle());
        $this->assertSame($this->_connection1,
                          $ref->getConnectionExpressions());
        $this->assertSame($this->_trans1,
                          $ref->xlFieldValuePairs($this->_data1));
        return $ref;
    }

    public function testExpectedValidViewingSeq()
    {
        $refInfo = array("viewingSequence" => "Users") + $this->_match1;
        $ref = new Ramp_Table_ExternalTableReference($refInfo);
        $setTable = $ref->getViewingSeq()->getSetTableForViewing();
        $this->assertSame('ramp_auth_users', $setTable->getDbTableName());
        $this->assertTrue(array_key_exists('id', $setTable->getFields()));
        return $ref;
    }

    public function testValidExternalRefWithSingleMatchingFieldInArray()
    {
        $refInfo = array("title" => "Title",
                         "viewingSequence" => "Users",
                         "match1" => $this->_match1);
        $ref = new Ramp_Table_ExternalTableReference($refInfo,
                                                            'DetailedView');
        $this->assertSame('Users', $ref->getViewingSeqName());
        $this->assertSame('Title', $ref->getTitle());
        $this->assertSame($this->_connection1,
                          $ref->getConnectionExpressions());
        $this->assertSame($this->_trans1,
                          $ref->xlFieldValuePairs($this->_data1));
        return $ref;
    }

    public function testValidExternalRefWithMultipleMatchingFields()
    {
        $refInfo = array("viewingSequence" => "Users",
                         "match1" => $this->_match1,
                         "match2" => $this->_match2);
        $ref = new Ramp_Table_ExternalTableReference($refInfo,
                                                            'DetailedView');
        $this->assertSame('Users', $ref->getViewingSeqName());
        $this->assertSame('Users', $ref->getTitle());
        $this->assertSame($this->_connection12,
                          $ref->getConnectionExpressions());
        $this->assertSame($this->_trans12,
                          $ref->xlFieldValuePairs($this->_data12));
        return $ref;
    }

    public function testUnmatchedLocalField()
    {
        $this->setExpectedException('Exception',
                        'not a valid external reference connector');
        $refInfo = array("viewingSequence" => "Users",
                         "localField" => "album_id");
        $ref = new Ramp_Table_ExternalTableReference($refInfo);
    }

    public function testUnmatchedExternalField()
    {
        $this->setExpectedException('Exception',
                        'not a valid external reference connector');
        $refInfo = array("viewingSequence" => "Users",
                         "externalField" => "id");
        $ref = new Ramp_Table_ExternalTableReference($refInfo);
    }

    public function testInvalidTableConnection()
    {
        $this->setExpectedException('Exception',
                        'not a valid external reference connector');
        $refInfo = array("viewingSequence" => "Users",
                         "localField" => "album_id",
                         "extField" => "should_be_externalField");
        $ref = new Ramp_Table_ExternalTableReference($refInfo);
    }

    public function testTableRefHasNoConnectionInfo()
    {
        $refInfo = array("viewingSequence" => "Users");
        $ref = new Ramp_Table_ExternalTableReference($refInfo);
        $this->assertSame('Users', $ref->getViewingSeqName());
        $this->assertSame(array(), $ref->getConnectionExpressions());
    }

    public function testExternalFieldIsNotAVisibleFieldOrPrimaryKey()
    {
        $this->setExpectedException('Exception',
                                    'not a visible field or primary key');
        $refInfo = array("viewingSequence" => "Users",
                         "match1" => $this->_match1,
                         "match2" => $this->_match2);
        $ref = new Ramp_Table_ExternalTableReference($refInfo);
        $this->assertSame($this->_trans2,
                          $ref->xlFieldValuePairs($this->_data2));
        $this->assertTrue(false);
    }

}
