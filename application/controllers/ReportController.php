<?php

require_once("TableController.php");

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
 *
 */

class ReportController extends TableController
{
    protected $_controllerName = 'report';

    /**
     * Initializes the attributes for this object as well as some
     * values commonly used by the associated view scripts.
     */
    public function init()
    {
        parent::init();

        // Set the default view for "Display All" actions.
        $this->_displayAllView = "table-view";
    }

    /**
     * Reports always start with a search, from which Display All 
     * Entries is an option.
     *
     */
    public function indexAction()
    {
        $this->_forward("search");
    }

    /**
     * Executes the search and goes to the appropriate display page.  
     * Only returns if the search failed.
     *
     * @param $setTable   table setting for the table in which to search
     * @param $data       column/value pairs on which to search
     * @param $comparators column/comparator pairs of search comparators
     * @param $buttonLabel button specifying whether to match any specified
     *                     field (OR) or all specified fields (AND)
     */
    protected function _executeSearch($setTable, $data, $comparators,
                                      $buttonLabel)
    {
        // Execute the search and decide how to display the results.
        $matchAbbrev = $this->_matchAbbrevs[$buttonLabel];
        $rows = $setTable->getTableEntries($data, $comparators, $matchAbbrev);
        $numResults = count($rows);
        if ( $numResults == 0 )
        {
            // Search failed.
            $this->view->errMsgs[] = "No matching results were found.";
            $this->view->dataEntryForm->populate($data);
        }
        else
        {
            // Matches found.
            $this->_goTo($this->_displayAllView, $data, $comparators,
                         $matchAbbrev);
        }
    }


    // ===> Redefining processSearchCallBack no longer necessary because 
    //      now have _displayAllView instance variable?

    /**
     * Processes search call-back after a button has been pressed.
     *
     * @param $setTable   table setting for the table in which to search
     * @param $form       the form used for the search
    protected function _processSearchCallBack($setTable, $form)
    {
        if ( $this->_submittedButton == self::DISPLAY_ALL )
        {
            $this->_goTo('table-view');
        }
        else    // Searching ...
        {
            $formData = $this->getRequest()->getPost();
            if ( $form->isValid($formData) )
            {
                $fieldVals = $form->getFieldValues();
                $nonNullData = $this->_getFilledFields($fieldVals);

                // Searching for any or all matches. Display based on 
                // number of results.
                $this->_executeSearch($setTable, $nonNullData,
                            $form->getComparitors(), $this->_submittedButton);

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
     */

    /**
     * Provides a report view of table records (a table view with a
     * a customized output format specific to the table setting).
     * If there is no customized view script helper for the table
     * setting, an error message will be displayed.
     *
     */
    public function tableViewAction()
    {
        $setTable = $this->_tblViewingSeq->getSetTableForTabularView();

    // ===> Why is this necessary here but not in TableController?
        $this->view->sequenceName = str_replace("%2F", "_",
                                                $this->_encodedSeqName);

        // Let view renderer know the table and data form to use.
        $this->_multiRecordInitDisplay($setTable);

    }

}
