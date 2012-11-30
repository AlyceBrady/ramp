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
 * @package    Ramp_Controller
 * @copyright  Copyright (c) 2012 Alyce Brady (http://www.cs.kzoo.edu/~abrady)
 * @license    http://www.cs.kzoo.edu/ramp/LICENSE.txt   Simplified BSD License
 * @version    $Id: TableController.php 1 2012-07-12 alyce $
 *
 */

/* TODO: might re-organize action functions to handle callbacks first, 
 * especially callbacks that just mean going elsewhere (eliminating the 
 * need to read the table from the database first).
 */

class TableController extends Zend_Controller_Action
{
    /* values used as keyword parameters (keyword or value) */
    const SUBMIT_BUTTON         = 'submit';
    const SETTING_NAME          = '_setting';
    const SEARCH_TYPE           = '_search';
    const ANY                   = Application_Model_SetTable::ANY;
    const ALL                   = Application_Model_SetTable::ALL;
    const EXCLUDE               = Application_Model_SetTable::EXCLUDE;

    /* labels for forms and buttons */
    const SEARCH                = "Search";
    const MATCH_ALL             = "Search On All Fields";
    const MATCH_ANY             = "Match Against Any Field";
    const MATCH_EXCLUDE         = "Exclude Fields";
    const DISPLAY_ALL           = "Display All Entries";
    const TABLE                 = "Tabular Display";
    const ADD                   = "Add New Entry";
    const CLONE_BUTTON          = "Clone This Entry";
    const EDIT                  = "Edit Entry";
    const DEL_BUTTON            = "Delete Entry";
    const RESET_BUTTON          = "Reset Fields";
    const CANCEL                = "Cancel";
    const CONFIRM               = "Confirm";
    const SAVE                  = "Save Changes";

    // Constant representing an unspecified enum value for a search
    const ANY_VAL               = '__any_search_value__';

    protected $_encodedSeqName;

    protected $_tblViewingSeq;

    protected $_buttonAction;

    protected $_searchType;

    protected $_fieldsToMatch;

    protected $_baseParams = null;

    protected $_matchAbbrevs = array(self::MATCH_ALL => self::ALL,
                                     self::MATCH_ANY => self::ANY,
                                     self::MATCH_EXCLUDE => self::EXCLUDE);

    /**
     * Initializes the attributes for this object as well as some
     * values commonly used by the associated view scripts.
     *
     */
    public function init()
    {
        // Get the sequence information (types of table settings to use).
        $this->_encodedSeqName = $this->_getParam(self::SETTING_NAME, "");
        $seqName = urldecode($this->_encodedSeqName);
        $this->_tblViewingSeq =
                Application_Model_TVSFactory::getSequenceOrSetting($seqName);

        // Get and store parameters for possible future use.
        $this->_buttonAction = $this->_getParam(self::SUBMIT_BUTTON);
        $this->_searchType = $this->_getParam(self::SEARCH_TYPE);
        $this->_fieldsToMatch = $this->_getFieldsToMatch();

        // Set the basic parameters to build on when going to other actions.
        $this->_baseParams = array('controller'=>'table',
                       self::SETTING_NAME=>$this->_encodedSeqName);

        // Initialize values that are passed to the view scripts.
        $this->view->seqSetting = $seqName;
        $this->view->baseParams = $this->_baseParams;
        $this->view->msgs = array();
        $this->view->errMsgs = array(
                // ""
                // . " Request params are: "
                // . print_r($this->getRequest()->getParams(), true)
                // . " Fields to match are: "
                // . print_r($this->_fieldsToMatch, true)
            );
    }

    /**
     * Provides a gateway to this set of actions.  Chooses the actual 
     * action to take based on the initial action defined for this table 
     * setting.
     *
     */
    public function indexAction()
    {
        $this->_forward($this->_tblViewingSeq->getInitialAction());
    }

    /**
     * Controls the search action, presenting a new page in which
     * to add values to match against.
     *
     */
    public function searchAction()
    {
        $setTable = $this->_tblViewingSeq->getSetTableForSearching();

        // Let the view renderer know the table, buttons, and data form to use.
        $this->_initViewTableInfo($setTable);
        $this->view->buttonList = array(self::MATCH_ALL, self::MATCH_ANY,
                                        self::MATCH_EXCLUDE,
                                        self::RESET_BUTTON, self::DISPLAY_ALL);
        $this->view->dataEntryForm = $form =
            new Application_Form_TableRecordEntry($setTable, 0, self::SEARCH);

        // Is this the initial display or a callback from a button action?
        if ( $this->_thisIsInitialDisplay() )
        {
            // Have fields been provided?
            if ( ! empty($this->_fieldsToMatch) )
                $this->_executeSearch($setTable, $this->_fieldsToMatch,
                    self::MATCH_ALL);

            // Otherwise, nothing to do except render view (done automatically).
        }
        elseif ( $this->_buttonAction == self::DISPLAY_ALL )
        {
            $this->_goTo('list-view');
        }
        else    // Searching or adding...
        {
            $formData = $this->getRequest()->getPost();
            if ( $form->isValid($formData) )
            {
                $nonNullData = $this->_filledFields($form->getValues());

                // Adding new entry based on failed search.
                if ( $this->_buttonAction == self::ADD )
                    { $this->_goTo('add', $nonNullData); }

                // Searching for any or all matches. Display based on 
                // number of results.
                $searchType = $this->_buttonAction;
                $this->_executeSearch($setTable, $nonNullData, $searchType);

                // Will only get here if search failed.
            }
            else
            {
                // Invalid entries: show them for editing.
                $this->view->errMsgs[] =
                        "Invalid data values.  Please correct.";
                $form->populate($formData);
            }
        }
        
    }

    /**
     * Executes the search and goes to the appropriate display page.  
     * Only returns if the search failed.
     *
     * @param $setTable   table setting for the table in which to search
     * @param $data       column/value pairs on which to search
     * @param $searchType whether to match any specified field (OR), all
     *                    specified fields (AND), or no specified fields
     */
    protected function _executeSearch($setTable, $data, $searchType)
    {
        // Execute the search and decide how to display the results.
        $matchAbbrev = $this->_matchAbbrevs[$searchType];
        $rows = $setTable->getTableEntries($data, $matchAbbrev);
        $numResults = count($rows);
        if ( $numResults == 1 )
        {
            // One match found.
            $keyInfo = $setTable->getKeyInfo($data);
            $this->_goTo('record-view', $keyInfo);
        }
        elseif ( $numResults > 1 )
        {
            // Multiple matches found.
            $params = $data +
                        array(self::SEARCH_TYPE => $matchAbbrev);
            $this->_goTo('list-view', $params);
        }
        else
        {
            // Search failed.
            $this->view->errMsgs[] = "No matching results were found.";
            $this->view->buttonList[] = self::ADD;
            $this->view->dataEntryForm->populate($data);
        }
    }

    /**
     * Provides a list view of table records (usually search results).
     *
     */
    public function listViewAction()
    {
        $setTable = $this->_tblViewingSeq->getSetTableForSearchResults();

        // Is this the initial display or a callback from a button action?
        if ( $this->_thisIsInitialDisplay() )
        {
            // Let view renderer know the table, buttons, and data form to use.
            $this->_initViewTableInfo($setTable);
            $this->view->buttonList = array(self::ADD, self::TABLE,
                                            self::SEARCH);
            $this->view->dataToDisplay =
                $setTable->getTableEntries($this->_fieldsToMatch,
                                           $this->_searchType);
            $this->view->displayingSubset = ! empty($this->_fieldsToMatch);
            if ( $this->view->displayingSubset )
                { $this->view->buttonList[] = self::DISPLAY_ALL; }

            // List will get filled-in status based on ADD setting.
            $this->view->addSetting = $this->_tblViewingSeq->getSetTableForAdding();
                            // Application_Model_TableViewSequence::ADD_SETTING);
        }
        elseif ( $this->_buttonAction == self::TABLE )
        {
            // Re-display same data in table mode.
            $this->_goTo('table-view', $this->_fieldsToMatch, true);
        }
        else
            { $this->_goTo($this->_getUsualAction($this->_buttonAction)); }

    }

    /**
     * Provides a tabular view of table records.
     *
     */
    public function tableViewAction()
    {
        $setTable = $this->_tblViewingSeq->getSetTableForViewing();

        // Is this the initial display or a callback from a button action?
        if ( $this->_thisIsInitialDisplay() )
        {
            // Let view renderer know the table, buttons, and data form to use.
            $this->_initViewTableInfo($setTable);
            $this->view->buttonList = array(self::ADD, self::SEARCH);
            $this->view->dataToDisplay =
                $setTable->getTableEntries($this->_fieldsToMatch,
                                           $this->_searchType);
            $this->view->displayingSubset = ! empty($this->_fieldsToMatch);
            if ( $this->view->displayingSubset )
            {
                $this->view->buttonList[] = self::DISPLAY_ALL;
            }

            // List will get filled-in status based on ADD setting.
            $this->view->addSetting = $this->_tblViewingSeq->getSetTableForAdding();
                            // Application_Model_TableViewSequence::ADD_SETTING);
        }
        elseif ( $this->_buttonAction == self::DISPLAY_ALL )
        {
            // Re-display with all data in same table mode.
            $this->_goTo('table-view');
        }
        else
            { $this->_goTo($this->_getUsualAction($this->_buttonAction)); }

    }

    /**
     * Presents a single record on a page for viewing.
     *
     * Precondition: this action should only be invoked when the 
     * provided parameters uniquely identify a single record.
     *
     */
    public function recordViewAction()
    {
        $setTable = $this->_tblViewingSeq->getSetTableForViewing();

        // Let the view renderer know the table, buttons, and data form to use.
        $this->_initViewTableInfo($setTable);
        $this->view->buttonList = array(self::EDIT, self::ADD,
                                        self::CLONE_BUTTON, self::SEARCH,
                                        self::DISPLAY_ALL);
        $this->view->dataEntryForm = $form =
                    new Application_Form_TableRecordEntry($setTable, 0);

        // Is this the initial display or a callback from a button action?
        if ( $this->_thisIsInitialDisplay() )
        {
            // Retrieve record based on provided fields / primary keys.
            $form->populate($setTable->getTableEntry($this->_fieldsToMatch));
        }
        elseif ( $this->_buttonAction == self::CLONE_BUTTON )
        {
            $this->_goTo('add',
                         $setTable->getCloneableFields($this->_fieldsToMatch));
        }
        elseif ( $this->_buttonAction == self::EDIT )
            { $this->_goTo('record-edit', $this->_fieldsToMatch); }
        else
            { $this->_goTo($this->_getUsualAction($this->_buttonAction)); }

    }

    /**
     * Controls the editing action for a single, editable record on a page.
     *
     * Precondition: this action should only be invoked when the 
     * parameters provided uniquely identify a single record.
     *
     */
    public function recordEditAction()
    {
        $setTable = $this->_tblViewingSeq->getSetTableForModifying();

        // Let the view renderer know the table, buttons, and data form to use.
        $this->_initViewTableInfo($setTable);
        $this->view->buttonList = array(self::SAVE, self::RESET_BUTTON,
                                        self::CANCEL, self::DEL_BUTTON);
        $this->view->dataEntryForm = $form =
              new Application_Form_TableRecordEntry($setTable, 0, self::EDIT);

        // Is this the initial display or the post-edit callback?
        if ( $this->_thisIsInitialDisplay() )
        {
            // Retrieve record based on provided fields / primary keys.
            $form->populate($setTable->getTableEntry($this->_fieldsToMatch));
        }
        elseif ( $this->_buttonAction == self::SAVE )
        {
            // Process the filled-out form that has been posted:
            // if the changes are valid, update the database.
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData))
            {
                // Update the database and redisplay the record.
                $setTable->updateTableEntry($form->getValues());
                $this->_goTo('record-view', $this->_fieldsToMatch);
            }
            else
            {
                // Invalid entries: show them for editing.
                $this->view->errMsgs[] =
                        "Invalid data values.  Please correct.";
                $form->populate($formData);
            }
        }
        elseif ( $this->_buttonAction == self::DEL_BUTTON )
            { $this->_goTo('delete', $this->_fieldsToMatch); }
        else  // Cancel
            { $this->_goTo('record-view', $this->_fieldsToMatch); }

    }

    /**
     * Controls the Table add action, presenting a new page in which
     * to add a new entry to the table.
     *
     */
    public function addAction()
    {
        $setTable = $this->_tblViewingSeq->getSetTableForAdding();

        // Let the view renderer know the table, buttons, and data form to use.
        $this->_initViewTableInfo($setTable);
        $this->view->buttonList = array(self::SAVE, self::RESET_BUTTON,
                                        self::CANCEL, self::SEARCH);
        $this->view->form = $form =
                new Application_Form_TableRecordEntry($setTable, 0, self::ADD);

        // Is this the initial display or the callback with fields provided?
        if ( $this->_thisIsInitialDisplay() )
        {
            // If "starter" fields have been provided, fill them in.
            if ( ! empty($this->_fieldsToMatch) )
            {
                $form->populate($this->_fieldsToMatch);
            }
        }
        elseif ( $this->_buttonAction == self::SAVE )
        {
            // Process the filled-out form that has been posted:
            // if the changes are valid, update the database.
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData))
            {
                // If some fields should be initialized from existing values, 
                // fill them in.  Remove null fields.
                $addValues = $this->_fillInitValues($setTable,
                                                    $form->getValues());
                $nonNullData = $this->_filledFields($addValues);

                // Update the database and redisplay the record.
                $setTable->addTableEntry($nonNullData);
                $this->_goTo('record-view', $nonNullData);
            }
            else
            {
                // Invalid entries: show them for editing.
                $this->view->errMsgs[] =
                        "Invalid data values.  Please correct.";
                $form->populate($formData);
            }
        }
        elseif ( $this->_buttonAction == self::CANCEL )
        {
            $this->_goTo('index');
        }
        else
        {
            $this->_goTo($this->_getUsualAction($this->_buttonAction));
        }
        
    }

    /**
     * Controls the Table delete action, presenting a confirmation page
     * before deleting an existing entry from the table.
     * TODO: Confirmation might be better handled on the client side, so 
     * that only confirmed delete requests get to the server.  In that 
     * case, processing of deletion requests would probably move to the 
     * appropriate display or edit actions.
     *
     */
    public function deleteAction()
    {
        $setTable = $this->_tblViewingSeq->getSetTableForModifying();

        // Let the view renderer know the table, buttons, and data form to use.
        $this->_initViewTableInfo($setTable);
        $this->view->buttonList = array(self::CONFIRM, self::CANCEL);
        $this->view->dataEntryForm = $form =
                    new Application_Form_TableRecordEntry($setTable, 0,
                                                          self::DEL_BUTTON);

        // Is this the initial display or the callback with confirmation?
        if ( $this->_thisIsInitialDisplay() )
        {
            // Retrieve the table entry to display based on the key(s);
            // assign to view for confirmation.
            $form->populate($setTable->getTableEntry($this->_fieldsToMatch));
        }
        elseif ( $this->_buttonAction == self::CANCEL )
        {
            $this->_goTo('record-view', $this->_fieldsToMatch);
        }
        else        // Delete has been confirmed.
        {
            // Process the posted delete confirmation information. 
            $formData = $this->getRequest()->getPost();
            if ( $form->isValid($formData) )
            {
                $rows = $setTable->deleteTableEntry($form->getValues());
                // TODO: report to user that entry was deleted!
            }

            // After deletion, return to initial page.
            $this->_goTo('index');
        }

    }

    /**
     * Initializes basic view renderer information from the set table.
     *
     * @param Application_Model_SetTable  table: setting & db info
     */
    protected function _initViewTableInfo($setTable)
    {
        $this->view->tableInfo = $setTable;
        $this->view->undefinedFieldsErrorMsg =
            $this->_getExtraneousFieldsErrorMsg($setTable);

        if ( count($setTable->getVisibleFields()) == 0 )
        {
            $this->view->errMsgs[] =
                    "This table setting has no visible fields.";
        }
    }

    /**
     * Builds an error message if there are fields specified in the 
     * table setting that do not exist in the database.
     *
     * @param Application_Model_SetTable  table: setting & db info
     * @return string       error message (or empty string if no error)
     *
     */
    protected function _getExtraneousFieldsErrorMsg($setTable)
    {
        $msg = "";
        $undefinedFields = $setTable->getUndefinedFieldNames();
        if ( count($undefinedFields) > 0 )
        {
            $msg = "Note: the following fields in the table setting are
                    undefined in the database: ";
            $delim = "";
            foreach ($undefinedFields as $field)
            {
                $msg .= $delim . $field;
                $delim = ", ";
            }
        }
        return $msg;
    }

    /**
     * Returns true if the current request represents the initial 
     * display for the current action.  A return of false, therefore, 
     * indicates that the current request represents the callback with 
     * fields to add, modify, or search filled in.
     *
     */
    protected function _thisIsInitialDisplay()
    {
        return !  $this->getRequest()->isPost();
    }

    /**
     * Gets the action usually associated with the given button.
     *
     */
    protected function _getUsualAction($buttonLabel)
    {
        $commonMapping = array(
            self::SEARCH => 'search', self::DISPLAY_ALL => 'list-view',
            self::ADD => 'add', self::EDIT => 'record-edit',
            self::DEL_BUTTON => 'delete', self::TABLE => 'table-view');

        return isset($commonMapping[$buttonLabel]) ?
                $commonMapping[$buttonLabel] : null;
    }

    /**
     * Gets the user data from request parameters as name=>value pairs
     * to use in database queries.
     *
     * @return array    name=>value pairs passed as params
     *
     */
    protected function _getFieldsToMatch()
    {
        $request = $this->getRequest();

        // Remove Zend and Ramp keyword parameters.
        $paramsToRemove = array( 
                                'controller' => null,
                                'action' => null,
                                'module' => null,
                                self::SETTING_NAME => null,
                                self::SUBMIT_BUTTON => null,
                                self::SEARCH_TYPE => null);

        // Return an array that does not include those parameters.
        return array_diff_key($request->getUserParams(), $paramsToRemove);
    }

    /**
     * Redirects the action, passing whatever parameters are 
     * appropriate.
     *
     * @param string $nextAction     the name of the next action
     * @param array $matchingFields  fields and values to search/select for
     * @param bool $includeSearchType  whether to pass search type as parameter
     */
    protected function _goTo($nextAction, $matchingFields = null,
                             $includeSearchType = false)
    {
        // Build up parameters to pass to next action.
        $params = array(self::SETTING_NAME => $this->_encodedSeqName);
        if ( $matchingFields != null )
            $params += $matchingFields;
        if ( $includeSearchType )
            $params[self::SEARCH_TYPE] = $this->_searchType;

        $this->_helper->redirector($nextAction, 'table', null, $params);
    }

    /**
     * Filters out fields for which no values were provided.
     *
     * @param array $data   Column-value pairs
     * @return array        Column-value pairs, with no empty values
     */
    protected function _filledFields(array $data)
    {
        // Remove column-value pairs with null values.
        $nonNullData = array();
        foreach ( $data as $field => $value )
        {
            if ( $value !== null && $value != "" && $value != self::ANY_VAL )
            {
                $nonNullData[$field] = $value;
            }
        }
        return $nonNullData;
    }

    /**
     * Fill in initial values based on information in setting.
     * TODO: Is there any need to consider cases where the 
     * "external table" is this table, so one field is being
     * initialized  from another that is not in the database yet?
     *
     * @param Application_Model_SetTable  table: setting & db info
     * @param array $data   Column-value pairs representing provided data
     */
    protected function _fillInitValues($setTable, array $data)
    {
        $storedSourceRecs = array();
        $searchData = $data;
        $initializedData = $data;

        // Loop through fields in this table to see if any should be 
        // initialized from values in another table.
        $inputFieldNames = array_keys($data);
        $relevantFields = $this->view->tableInfo->getExternallyInitFields();
        foreach ( $relevantFields as $newFieldName => $newField )
        {
            // Initialize from another table if data not already provided.
            if ( $data[$newFieldName] == null )
            {
                // Determine initializing table; see if source record 
                // has already been retrieved.
                $sourceTblName = $newField->getInitTableName();
                if ( ! isset($storedSourceRecs[$sourceTblName]) )
                {
                    $sourceRecord =
                            $this->_getSourceRecord($setTable, $sourceTblName,
                                                    $data);
                    if ( $sourceRecord == null )
                    {
                        continue;
                    }
                    else
                    {
                        $storedSourceRecs[$sourceTblName] = $sourceRecord;
                    }
                }
                $sourceRecord = $storedSourceRecs[$sourceTblName];
                $initializedData[$newFieldName] = $sourceRecord[$newFieldName];
            }
        }

        return $initializedData;
    }

    /**
     * Gets the record with the initializing information.
     *
     * @param Application_Model_SetTable $setTable    setting & db info
     * @param string $sourceTblName  name of table with initializing info
     * @param array $userData        fields with user-supplied data
     */
    protected function _getSourceRecord($setTable, $sourceTblName, $userData)
    {
        // Need to retrieve initializing record; check that
        // user provided enough information to find it.
        $initRef = $setTable->getInitRefInfo($sourceTblName);
        $sourceTbl = $initRef->getViewingSeq()->getReferenceSetTable();
        $searchKeys = $initRef->findConnectionFields($userData);
        $matches = $sourceTbl->getTableEntries($searchKeys);
        if ( count($matches) != 1 )
        {
            // Cannot initialize with info provided; proceed 
            // directly to next field.
            $this->view->errMsgs[] =
                "Cannot initialize $newFieldName from " .
                "$sourceTblName -- insufficient primary " .
                "key information to find source record.";
            return null;
        }
        else
        {
            return $matches[0];
        }
    }

}

