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
 * @package    Ramp_Activity
 * @copyright  Copyright (c) 2012 Alyce Brady (http://www.cs.kzoo.edu/~abrady)
 * @license    http://www.cs.kzoo.edu/ramp/LICENSE.txt   Simplified BSD License
 *
 */

/**
 * A Ramp_Activity_Gateway object gets activity 
 * specifications from associated external sources.
 *
 */
class Ramp_Activity_Gateway
{
    // Keywords
    const ACTIVITY      = "activity";
    const ACT_LIST      = "list";
    const ACT_TITLE     = "activityListHeading";

    /** @var array */
    protected $_listOfActivityLists = array();   // list of lists

    /**
     * Gets the named activity list, reading it in from an external 
     * source if necessary.
     *
     * @param $name  internal name of an activity list file or 
     *               activity list within a file
     *               e.g., actList.act, actList.ini, al.act/actList
     * @return a list of activity specifications
     *
     */
    public function getActivityList($name)
    {
        // Determine whether $name is a filename or internal activity list name
        $actList = basename($name);

        // Check whether activity list has already been read in.
        if ( ! isset($this->_listOfActivityLists[$name][self::ACT_LIST]) &&
             ! isset($this->_listOfActivityLists[$actList][self::ACT_LIST]) )
        {
            // If not, do so, and add to the overall list of activity lists.
            // (This may involve reading in multiple activity lists, if 
            // the file contains them.)
            $this->_importActivitySpecs($name);
        }

        return $this->_listOfActivityLists[$name][self::ACT_LIST];
    }

    /**
     * Gets the title or heading for the named activity list; returns 
     * null if no title or heading was specified.
     *
     * @param $name  internal name of an activity list file or 
     *               activity list within a file
     *               e.g., actList.act, actList.ini, al.act/actList
     * @return a user-specified title or heading for this list
     */
    public function getActivityListTitle($name)
    {
        // Ensure that activity list has been read in.
        $temp = $this->getActivityList($name);

        // Get the title.
        return $this->_listOfActivityLists[$name][self::ACT_TITLE];
    }

    /**
     * Reads one or more activity lists, with their associated individual 
     * activity specifications, from an external source into the list of 
     * activity lists.
     *
     * The specification of an activity list may include the full
     * specifications for its activities "in place", or it may have
     * references to activity specifications defined elsewhere in the
     * file, in which case the specification references in the activity
     * list must be resolved to the actual specifications.
     *
     * @param string $name  internal name of an activity list file or 
     *                      activity list within a file
     *                      e.g., actList.act, actList.ini, al.act/actList
     * @return array        contains activity specifications
     * @throws Exception error reading activity information from a file
     *
     */
    protected function _importActivitySpecs($name)
    {

        // Import all activity specifications provided in the external
        // source associated with $name.
        $reader = new Ramp_Activity_Config_IniReader();
        $importedSpecs = $reader->importActivitySpecs($name)->toArray();
        $filename = $reader->getActivityListFilename($name);

        // Identify activity list(s) and separate activity specifications,
        // validating the activity specifications along the way.
        $newLists = $this->_getActivityLists($filename, $importedSpecs);
        $activitySpecs = $this->_getActivitySpecs($importedSpecs);

        // Resolve activity specification references in activity lists 
        // to the actual specifications.
        $this->_resolveActivityReferences($newLists, $activitySpecs);
        $this->_expandActListRefs($newLists, $filename);

        if ( ! isset($this->_listOfActivityLists[$name]) )
        {
            if ( $name == $filename )
            {
                throw new Exception("Error: File \"$name\" does not contain " .
                    "a top-level activity or a section named \"$name\".");
            }
            else
            {
                $actListName = basename($name);
                throw new Exception("Error: File \"$filename\" does not " .
                    "contain a section named \"$actListName\".");
            }
        }
    }

    /**
     * Identifies activity lists within the raw data provided, along 
     * with their nested activity specifications.
     * NOTE: this function modifies the $rawData parameter, removing 
     * the found activity lists from it.
     *
     * @param string $name    original name provided to getActivityList
     * @param array $rawData  array containing a mix of activity lists 
     *                        and separate activity specifications
     *                        (modified by function!)
     * @preturn array         a list of the NAMES of the activity lists found
     */
    protected function _getActivityLists($name, &$rawData)
    {
        $foundLists = array();

        // Validate and save the top-level activity list, if there is one.
        if ( array_key_exists(self::ACTIVITY, $rawData) )
        {
            $this->_saveValidatedList($name, $rawData, true);
            $foundLists[] = $name;
            unset($rawData[self::ACTIVITY]);
        }

        // Validate and save any other named activity lists that may 
        // exist in the raw data.
        foreach ( $rawData as $entryName => $entryValue )
        {
            // Is this an activity list?
            if ( is_array($entryValue) &&
                 array_key_exists(self::ACTIVITY, $entryValue) )
            {
                // Yes, so validate and save it.
                $this->_saveValidatedList($entryName, $entryValue, false);
                $foundLists[] = $entryName;
                unset($rawData[$entryName]);
            }
        }

        // Check for the unusual case that the activity specification 
        // file contains nothing but a single, unnamed activity
        // specification, in which case the file represents an activity
        // list with a single activity.
        if ( empty($this->_listOfActivityLists) )
        {
            $this->_listOfActivityLists[$name][self::ACT_LIST] =
                        array($this->_validateSpec($name, $rawData));
            $foundLists[] = $name;
        }

        return $foundLists;
    }

    /**
     * Validates the given activity list.
     *
     * @param string $name     name of activity list (file or section name)
     * @param string $contents activity list (array format) to validate
     * @param bool   $topLevel true if this is a top-level activity list
     * @throws Exception    if the activity list is not valid.
     */
    protected function _saveValidatedList($name, $contents, $topLevel)
    {
        $listOfSpecs = $this->_getKeyVal($contents, self::ACTIVITY);

        // Check that this is not a duplicate activity list name.
        if ( isset($this->_listOfActivityLists[$name]) )
        {
            throw new Exception("Error: multiple activity lists named \"" .
                "$name\"");
        }

        // Check that section does not have extraneous properties.
        $title = $this->_getKeyVal($contents, self::ACT_TITLE);
        $expectedCount = ($title == null) ? 1 : 2;
        if ( ! $topLevel && count($contents) != $expectedCount )
        {
            throw new Exception("Error: activity list \"$name\" has " .
                "an invalid property; should only contain " .
                self::ACTIVITY . " properties and an optional " .
                self::ACT_TITLE . " property");
        }

        // Make sure that every entry in the list is an activity 
        // specification (or specification reference).
        $objectList = array();
        foreach ( $listOfSpecs as $specName => $activitySpec )
        {
            $validSpecObj = $this->_validateSpec($specName,
                                                 $activitySpec);
            $objectList[$specName] = $validSpecObj;
        }

        $this->_listOfActivityLists[$name][self::ACT_LIST] = $objectList;
        $this->_listOfActivityLists[$name][self::ACT_TITLE] = $title;
    }

    /**
     * Validates and returns the given activity specification as a 
     * string (if the specification is merely a reference) or as an 
     * activity spec object (if the specification was a valid, full 
     * specification).
     *
     * @param string $actList name of activity list
     * @param array $spec     an array containing activity specification 
     *                        properties (valid or invalid)
     * @return mixedType      a string for a specification reference or a
     *                        Ramp_Activity_Specification object for a 
     *                        full specification
     * @throws Exception      if the activity is not valid
     */
    protected function _validateSpec($actList, $spec)
    {
        // If the specification is just a reference, return it.
        if ( is_string($spec) )
        {
            return $spec;
        }

        // Otherwise, construct a valid activity specification object 
        // (validation is performed as the object is constructed).
        return new Ramp_Activity_Specification($actList, $spec);
    }

    /**
     * Identifies and validates activity specifications within the
     * modified raw data from which activity list specifications
     * have been removed.
     *
     * @param array $rawData  array containing activity specifications
     * @return array          a list of Activity Spec records
     */
    protected function _getActivitySpecs($rawData)
    {
        $specs = array();

        // Everything left in rawData should be an activity specification.
        foreach ( $rawData as $name => $specAsArray )
        {
            $spec = $this->_validateSpec($name, $specAsArray);
            $specs[$name] = $spec;
        }

        return $specs;
    }

    /**
     * Resolves any unresolved activity specification references in the
     * $actLists activity list(s) to an activity specification in the
     * $actSpecs list of activity specifications.  Throws an error if 
     * there is an unresolved activity specification reference that 
     * cannot be resolved.
     *
     * @param array $actListNames names of latest additions to list of lists
     * @param array $actSpecs list of activity specifications (object format)
     * throws Exception       if a reference cannot be resolved
     *
     */
    protected function _resolveActivityReferences($actListNames, $actSpecs)
    {
        foreach ( $actListNames as $listName )
        {
            // Get activity list associated with $listName.
            // Look for unresolved references in the activity list.
            $actList = &$this->_listOfActivityLists[$listName][self::ACT_LIST];
            foreach ( $actList as $actName => $spec )
            {
                if ( is_string($spec) )
                {
                    // Resolve reference.
                    if ( ! array_key_exists($spec, $actSpecs) )
                    {
                        throw new Exception("Activity List Error: " .
                            "there is no activity specification " .
                            "for $spec (referenced in $listName)");
                    }
                    $actList[$actName] = $actSpecs[$spec];
                }
            }
        }
    }

    /**
     * Replaces any references to internal activity list specifications
     * with fully-qualified versions that include the name of the 
     * current file.
     *
     * @param array $newActListNames names of latest additions to list of lists
     * @param string $filename    name of this activity list file
     *
     */
    protected function _expandActListRefs($newActListNames, $filename)
    {
        $namesToChange = array();
        $allActListNames = array_keys($this->_listOfActivityLists);
        foreach ( $newActListNames as $listName )
        {
            // Get activity list associated with $listName.
            $actList = $this->_listOfActivityLists[$listName][self::ACT_LIST];
            foreach ( $actList as $actName => $spec )
            {
                // Is this activity an activity list?
                if ( $spec->isActivityList() )
                {
                    // Is it an internally-defined activity list that 
                    // is not already fully qualified?
                    $source = $spec->getSource();
                    if ( in_array($source, $allActListNames) &&
                         strpos($source, $filename) === FALSE )
                    {
                        // Yes, needs to be fully qualified.  Change the 
                        // name in the activity spec now, and record it 
                        // to change the actual activity list name later.
                        $fullyQual = $filename . DIRECTORY_SEPARATOR .
                                     $source;
                        $namesToChange[$source] = $fullyQual;
                        $spec->setSource($fullyQual);
                    }
                }
            }
        }

        // Now change the names of the internal activity lists.
        foreach ( $namesToChange as $source => $fullyQualified )
        {
            $this->_listOfActivityLists[$fullyQualified][self::ACT_TITLE] =
                        $this->_listOfActivityLists[$source][self::ACT_TITLE];
            $this->_listOfActivityLists[$fullyQualified][self::ACT_LIST] =
                        $this->_listOfActivityLists[$source][self::ACT_LIST];
            unset($this->_listOfActivityLists[$source]);
        }
    }

    /**
     * Gets the value in $theArray associated with $key.  Returns null 
     * if $key is not in $theArray (or if it is, but its value is null).
     *
     */
    protected function _getKeyVal($theArray, $key)
    {
        return isset($theArray[$key]) ? $theArray[$key] : null;
    }

}

