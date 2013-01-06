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
 * @version    $Id: ReportController.php 1 2012-12-12 alyce $
 *
 */

class ReportController extends TableController
{
    protected $_controllerName = 'report';

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
     * @param $searchType whether to match any specified field (OR), all
     *                    specified fields (AND), or no specified fields
     */
    protected function _executeSearch($setTable, $data, $searchType)
    {
        // Execute the search and decide how to display the results.
        $matchAbbrev = $this->_matchAbbrevs[$searchType];
        $rows = $setTable->getTableEntries($data, $matchAbbrev);
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
            $params = $data + array(self::SEARCH_TYPE => $matchAbbrev);
            $this->_goTo('table-view', $params);
        }
    }

    /**
     * Processes search call-back after a button has been pressed.
     *
     * @param $setTable   table setting for the table in which to search
     * @param $form       the form used for the search
     */
    protected function _processSearchCallBack($setTable, $form)
    {
        if ( $this->_buttonAction == self::DISPLAY_ALL )
        {
            $this->_goTo('table-view');
        }
        else    // Searching ...
        {
            $formData = $this->getRequest()->getPost();
            if ( $form->isValid($formData) )
            {
                $nonNullData = $this->_filledFields($form->getValues());

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
     * Provides a report view of table records (a table view with a
     * a customized output format specific to the table setting).
     * If there is no customized view script helper for the table
     * setting, an error message will be displayed.
     *
     */
    public function tableViewAction()
    {
        $setTable = $this->_tblViewingSeq->getSetTableForViewing();
        $this->view->sequenceName = str_replace("%2F", "_",
                                                $this->_encodedSeqName);

        // Let view renderer know the table and data form to use.
        $this->_multiRecordInitDisplay($setTable);

    }

}
