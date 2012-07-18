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
 * @version    $Id: multiTableController.php 1 2012-07-12 alyce $
 *
 */

class TableController extends Zend_Controller_Action
{
    /* values used as keyword parameters */
    const SUBMIT_BUTTON         = 'submit';
    const SETTING_NAME          = 'setting';
    const SEARCH_RESULTS        = "searchResults";
    const FIELDS                = 'fields';
    // const FROM                  = "from";

    /* values used to determine which setting to get */
    const DISPLAY_MULT          = "displayMult";
    const DISPLAY_ONE           = "displayOne";

    /* labels for buttons */
    const MATCH_ALL             = "Search On All Fields";
    const MATCH_ANY             = "Match Against Any Field";
    const DISPLAY_ALL           = "Display All Entries";
    const CLEAR                 = "Clear Fields";

    protected $_tableViewingSequence = null;

    protected $_seqSetting = null;

    protected $_baseParams = null;

    /**
     * Initializes the attributes for this object, and the tableInfo 
     * attribute of the associated View object.
     *
     */
    public function init()
    {
        /* Initialize action controller here */
        $rawSettingName = $this->_getParam(self::SETTING_NAME);
        $this->_seqSetting = urldecode($rawSettingName);
        $this->_tableViewingSequence =
                    $this->_getViewingSequence($this->_seqSetting);
        $this->_baseParams = array('controller'=>'table',
                       self::SETTING_NAME=>urlencode($this->_seqSetting));

        // Initialize values that are passed to the view scripts.
        $this->view->seqSetting = $this->_seqSetting;
        $this->view->baseParams = $this->_baseParams;
        $this->view->msgs = array();
        $this->view->errMsgs = array();
    }

    /**
     * Provides a gateway to the Table actions.  Chooses the actual 
     * action to take based on the initial action defined for this table 
     * setting.
     *
     */
    public function indexAction()
    {
        $this->_forward($this->_tableViewingSequence->getInitialAction());
    }

    /**
     * Controls the Table search action, presenting a new page in which
     * to add values to match against.
     *
     */
    public function searchAction()
    {
        $setTable = $this->_getSetTable(self::DISPLAY_ONE);

        // Instantiate the form and set the label for the submit button.
        $form = new Application_Form_SearchForm();

        // Add appropriate buttons to form; group for display.
        $matchAll = $this->_searchAddSubmitButton($form, self::MATCH_ALL);
        $matchAny = $this->_searchAddSubmitButton($form, self::MATCH_ANY);
        $displayAll = $this->_searchAddSubmitButton($form, self::DISPLAY_ALL);
        $reset = $this->_searchAddResetButton($form, self::CLEAR);
        $buttonElementNames = array($matchAll->getName(),
            $matchAny->getName(), $reset->getName(), $displayAll->getName());
        $buttons = $form->addDisplayGroup($buttonElementNames, 'buttons',
                            array('id'=>'body-sidebar', 'class'=>'sidebar',
                                'disableLoadDefaultDecorators'=>true));
        $form->setDisplayGroupDecorators(array('FormElements', 'Fieldset'));

        // Add search fields to form.
        $fieldsForm = new Application_Form_LabeledFields($setTable, true, true);
        $form->addSubForm($fieldsForm, self::FIELDS);

        // Specify the form to render.
        $this->view->form = $form;

        // Is this the initial display or a callback from a button action?
        if ( $this->_thisIsInitialDisplay() )
        {
            // Nothing to do except render view (done automatically).
        }
        else        // Callback based on a button action.
        {
            // Validate form data in order to determine what to do next.
            $formData = $this->getRequest()->getPost();
            if ( $form->isValid($formData) )
            {
                // Did the user choose to do something other than a search?
                if ( $displayAll->isChecked() )
                {
                    $this->_helper->redirector('multi-display', 'table', null,
                                               $this->_baseParams);
                }

                // True search callback: institute the appropriate search.
                $rows = $setTable->getTableEntries($fieldsForm->getValues(),
                                                   $matchAny->isChecked());

                // Display results; action depends on number of results.
                if ( count($rows) > 0 )
                {
                    $nextAction = ( count($rows) == 1 ) ?
                                        'page-display' : 'multi-display';
                    $this->_setParam(self::SEARCH_RESULTS, $rows);
                    $this->_forward($nextAction);
                }
                else
                {
                    $this->view->errMsgs[] =
                                    "No matching results were found.";
                    $form->populate($formData);
                }
            }
            else
            {
                // Invalid entries: show them for editing.
                $form->populate($formData);
            }
        }
        
    }

    /**
     * Provides a gateway to the various Table actions for displaying
     * multiple  records.
     *
     */
    public function multiDisplayAction()
    {
        $setTable = $this->_getSetTable(self::DISPLAY_MULT);
        $isSearchDisplay = $this->_hasParam(self::SEARCH_RESULTS);
        $searchResults = $this->_getParam(self::SEARCH_RESULTS);

        // Turn off the default view rendering; the actual viewing 
        // format will be determined by the multiple display action 
        // specified by the table setting.
        $this->_helper->viewRenderer->setNoRender();

        // Get the data to display from the database unless displaying 
        // search results (which have already been retrieved from the 
        // database).
        $this->view->dataToDisplay = $isSearchDisplay ?
                            $searchResults : $setTable->getTableEntries();
        $this->view->displayingSubset = $isSearchDisplay;
        // $this->view->returnParams = $this->_buildFromParams();

        // Render using the appropriate multiple display format.
        $this->render($this->_tableViewingSequence->getMultDisplayAction());
    }

    /**
     * Controls the page display action, presenting a single,
     * editable record on a page.
     *
     * Precondition: this action should only be invoked when the 
     * parameters provided uniquely identify a single record.
     *
     */
    public function pageDisplayAction()
    {
        $setTable = $this->_getSetTable(self::DISPLAY_ONE);
        $searchResults = $this->getRequest()->getParam(self::SEARCH_RESULTS);
        $isSearchDisplay = (bool) $searchResults;

        // Instantiate the form and set the label for the submit button.
        $form = new Application_Form_TableRecordEntry($setTable, 0);
        $this->_addSubmitButton($form, 'Save');

        // Specify the form to render.
        $this->view->form = $form;

        // Is this the initial display or the post-change callback?
        if ( $this->_thisIsInitialDisplay() )
        {
            // Retrieve the table entry to display and/or edit based
            // on the primary key(s) specified by a select.
            $keyValuePairs = $this->_getUserDataFields();
            $entry = $setTable->getOneTableEntry($keyValuePairs);
            $form->populate($entry);
        }
        else if ( $isSearchDisplay )
        {
            // Populate the form based on the previously retrieved 
            // search results (passed as a parameter).  Search action has 
            // already checked that there will be only one result.
            $form->populate($searchResults[0]);
        }
        else        // Callback: Changes have been made and saved
        {
            // Process the filled-out form that has been posted:
            // if the changes are valid, update the database.
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData))
            {
                // Update the database and redisplay the table.
                $num = $setTable->updateTableEntry($form->getValues());
                $this->_helper->redirector('index', 'table', null,
                                           $this->_baseParams);
            }
            else
            {
                // Invalid entries: show them for editing.
                $form->populate($formData);
            }
        }
        
    }

    /**
     * Controls the Table add action, presenting a new page in which
     * to add a new entry to the table.
     *
     */
    public function addAction()
    {
        $setTable = $this->_getSetTable(self::DISPLAY_ONE);

        // Instantiate the form and set the label for the submit button.
        $form = new Application_Form_TableRecordEntry($setTable, 0, true);
        $this->_addSubmitButton($form, 'Add');

        // Specify the form to render.
        $this->view->form = $form;

        // Is this the initial display or the callback with fields provided?
        if ( $this->_thisIsInitialDisplay() )
        {
            // Nothing to do except render view (done automatically).
        }
        else        // Callback: New values have been entered.
        {
            // Process the filled-out form that has been posted:
            // if the changes are valid, update the database.
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData))
            {
                // Update the database and redisplay the table.
                $setTable->addTableEntry($form->getValues());
                $this->_helper->redirector('index', 'table', null,
                                           $this->_baseParams);
            }
            else
            {
                // Invalid entries: show them for editing.
                $form->populate($formData);
            }
        }
        
    }

    /**
     * Controls the Table delete action, presenting a confirmation page
     * before deleting an existing entry from the table.
     * TODO: Confirmation might be better handled on the client side, so 
     * that only confirmed delete requests get to the server.  In that 
     * case, processing of deletion requests would probably move to the 
     * appropriate display actions.
     *
     */
    public function deleteAction()
    {
        $setTable = $this->_getSetTable(self::DISPLAY_ONE);

        // Instantiate the form and set the label for the submit button.
        $form = new Application_Form_TableRecordEntry($setTable, 0);
        $confirm = $this->_addSubmitButton($form, 'Confirm');
        $cancel = $this->_addSubmitButton($form, 'Cancel');

        // Set all fields in form to read-only.
        foreach ( $form as $element )
        {
            $element->setAttrib('readOnly', 'readOnly');
        }

        // Specify the form to render.
        $this->view->form = $form;

        // Is this the initial display or the callback with confirmation?
        if ( $this->_thisIsInitialDisplay() )
        {
            // Retrieve the table entry to display
            // based on the key(s); assign to view for confirmation.
            $keyValuePairs = $this->_getUserDataFields();
            $entry = $setTable->getOneTableEntry($keyValuePairs);
            $form->populate($entry);
        }
        else        // Callback: Confirmation has been provided or denied.
        {
            // Process the posted delete confirmation information. 
            // Delete only if request is confirmed.
            $formData = $this->getRequest()->getPost();
            if ( $form->isValid($formData) && $confirm->isChecked() )
            {
                $rows = $setTable->deleteTableEntry($form->getValues());
                // TODO: report to user that entry was deleted!
            }

            // Whether delete happens or not, return to initial page.
            $this->_helper->redirector('index', 'table', null,
                                       $this->_baseParams);
        }

    }

    /**
     * Controls the Table delete action, presenting a confirmation page
     * before deleting an existing entry from the table.
     * TODO: Confirmation might be better handled on the client side, so 
     * that only confirmed delete requests get to the server.  In that 
     * case, processing of deletion requests would probably move to the 
     * appropriate display actions.
     *
    public function deleteAction()
    {
        $setTable = $this->_getSetTable(self::DISPLAY_ONE);

        // Is this the initial display or the callback with confirmation?
        if ( $this->_thisIsInitialDisplay() )
        {
            // Retrieve the table entry that would be affected
            // based on the key(s); assign to view for confirmation.
            $keyValuePairs = $this->_getUserDataFields();
            $entry = $setTable->getOneTableEntry($keyValuePairs);
            $this->view->retrievedTableEntryData = $entry;
        }
        else        // Callback: Confirmation has been provided or denied.
        {
            // Process the posted delete confirmation information. 
            // Delete only if request is confirmed.
            $del = $this->getRequest()->getPost('delSubmitValue');
            if ($del == 'Confirm')
            {
                // Delete the entry with the data specified.
                $dataFromPost = $this->getRequest()->getPost();
                unset($dataFromPost['delSubmitValue']);
                $setTable->deleteTableEntry($dataFromPost);
                // TODO: report to user that entry was deleted!
            }

            // Whether delete happens or not, return to initial page.
            $this->_forward('index');
        }
    }
     */

    /**
     * Gets a table viewing sequence for the given sequence or table 
     * setting name.
     *
     * @param string $name    name of the sequence
     * @return Application_Model_TableViewSequence
     *
     */
    protected function _getViewingSequence($name)
    {
        // Get the sequence.
        $allSequences = Zend_Registry::get('rampTableViewingSequences');
        if ( isset($allSequences[$name]) )
            { $sequence = $allSequences[$name]; }
        else
        {
            // Sequence not in registry; construct and register it.
            $sequence = new Application_Model_TableViewSequence($name);
            $allSequences[$name] = $sequence;
            Zend_Registry::set('rampSetTables', $allSequences);
        }

        // Return the sequence.
        return $sequence;
    }

    /**
     * Gets the set table indicated by $whichSettingType 
     * (self::DISPLAY_ONE or self::DISPLAY_MULT).
     *
     */
    protected function _getSetTable($whichSettingType)
    {
        $setTable = $whichSettingType == self::DISPLAY_ONE ?
                        $this->_tableViewingSequence->getDetailSetting() :
                        $this->_tableViewingSequence->getMultDisplaySetting();
        $this->view->tableInfo = $setTable;
//TODO: Should this check be done later, since it requires a call out to 
//the database?  Or is this function only being called when a call to 
//the database is necessary anyway?  Question 2: should the message and 
//exception be checked, set, etc. in TVS or SetTable, not here?
        $this->view->undefinedFieldsErrorMsg =
            $this->_getExtraneousFieldsErrorMsg($setTable);

        if ( count($setTable->getVisibleFields()) == 0 )
        {
            $this->view->errMsgs[] =
                    "This table setting has no visible fields.";
        }

        return $setTable;
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
     * Adds a submit button to the given $form with the given label,
     * and using the same label as the name.  If there are spaces in 
     * the given label, they will be preserved in the label on the 
     * button, although the button name will have the spaces removed.
     *
     * @param $form     the form to add submit button to
     * @param $label    the label to put on the button
     */
    protected function _addSubmitButton($form, $label)
    {
        $submit = new Zend_Form_Element_Submit($label, $label);
        $form->addElement($submit);
        return $submit;
    }

    /**
     * Adds a submit button to the given $form with the given label,
     * and using the same label as the name.  If there are spaces in 
     * the given label, they will be preserved in the label on the 
     * button, although the button name will have the spaces removed.
     *
     * @param $form     the form to add submit button to
     * @param $label    the label to put on the button
     */
    protected function _searchAddSubmitButton($form, $label)
    {
        $submit = new Zend_Form_Element_Submit($label, $label);
        $submit->setDecorators(array(
            array('Tooltip'),
            array('ViewHelper'),
            array('HtmlTag', array('tag' => 'li', 'class' => 'submit'))
        ));
        $form->addElement($submit);
        return $submit;
    }

    /**
     * Adds a reset button to the given $form with the given label,
     * and using the same label as the name.  If there are spaces in 
     * the given label, they will be preserved in the label on the 
     * button, although the button name will have the spaces removed.
     *
     * @param $form     the form to add reset button to
     * @param $label    the label to put on the button
     */
    protected function _searchAddResetButton($form, $label)
    {
        $reset = new Zend_Form_Element_Reset($label, $label);
        $reset->setDecorators(array(
            array('Tooltip'),
            array('ViewHelper'),
            array('HtmlTag', array('tag' => 'li', 'class' => 'reset'))
        ));
        $form->addElement($reset);
        return $reset;
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
     * Builds a set of parameters to pass that could be used later to 
     * return to the current page.
     *
    protected function _buildFromParams()
    {
        return array(self::FROM => $this->getRequest()->getParams());
    }
     */

    /**
     * Gets the user data from request parameters as name=>value pairs
     * to use in database queries.
     *
     * @return array    name=>value pairs passed as params
     *
     */
    protected function _getUserDataFields()
    {
        $request = $this->getRequest();

        // Determine parameters to remove (Zend parameters and
        // TableController parameters).
        // $moduleKey = $request->getModuleKey();
        // $controllerKey = $request->getControllerKey();
        // $actionKey = $request->getActionKey();
        // $condensedMatchAll = str_replace(' ', '', self::MATCH_ALL);
        // $condensedMatchAny = str_replace(' ', '', self::MATCH_ANY);

        $paramsToRemove = array( // $request->getModuleKey() => null,
                                // $request->getControllerKey() => null,
                                // $request->getActionKey() => null,
                                self::SUBMIT_BUTTON => null,
                                self::SETTING_NAME => null,
                                // self::FROM => null,
                                self::SEARCH_RESULTS => null,
                                self::FIELDS => null);

        // Return an array that does not include those parameters.
        return array_diff_key($request->getUserParams(), $paramsToRemove);
    }


}

