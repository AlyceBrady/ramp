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

/*
 * FYI: Meta Info contains: 
 *      [SCHEMA_NAME]
 *      [TABLE_NAME]
 *      [COLUMN_NAME]
 *      [COLUMN_POSITION]
 *      [DATA_TYPE]
 *      [DEFAULT]
 *      [NULLABLE]
 *      [LENGTH]
 *      [SCALE]
 *      [PRECISION]
 *      [UNSIGNED]
 *      [PRIMARY]
 *      [PRIMARY_POSITION]
 *      [IDENTITY]
 */

class Ramp_Form_Table_TableRecordEntry extends Zend_Form
{
    const VIEW   = TableController::VIEW;
    const ADD    = TableController::ADD;
    const EDIT   = TableController::EDIT;
    const SEARCH = TableController::SEARCH;
    const DEL    = TableController::DEL_BUTTON;

    const ANY_VAL_LABEL = "ANY VALUE";
    const ANY_VAL = Ramp_Table_SetTable::ANY_VAL;

    const AUTO_INCR_EXPL = "Auto-incremented by default";
    const REQUIRED_EXPL = "Required field";
    const RECOMMENDED_EXPL = "Recommended field";
    const REFERENCE_EXPL = "Reference to field in ";
    const EXTERNAL_REF_EXPL = "From %s table";

    // Set of supported search comparators:
    const CONTAINS          = 'HAS';
    const EQUAL             = '=';
    const LESS_THAN         = '<';
    const LT_OR_EQUAL       = '<=';
    const GREATER_THAN      = '>';
    const GT_OR_EQUAL       = '>=';
    const NOT_EQUAL         = '!=';
    const SQL_IS_NULL       = 'IS NULL';
    const SQL_NOT_NULL      = 'IS NOT NULL';
    const LIKE              = 'LIKE';
    const DEFAULT_COMPARATOR   = self::EQUAL;

    // Keywords used to store search request information.
    const FIELD_VALUES          = 'fieldVals';
    const FULL_SEARCH_RESULTS   = 'fullSearchResults';
    const VALUE                 = 'searchValue';
    const COMP                  = 'comparator';

    const SEARCH_COMP_SUFFIX = "_comparator";

    const FIELD_WIDTH           = 'span3';
    const COMPARATOR_CLASS_INFO = 'comparator span1';
    const SMALL_FIELD_WIDTH     = 'span2';

    protected $_setTable;
    protected $_formSuffix;
    protected $_formType;
    protected $_makeSmall;
    protected $_searchCompElts = array();
    protected $_fieldElts = array();

    /**
     * Returns a list of the valid search comparators.
     */
    public static function validComparators()
    {
        return array(
            self::CONTAINS => self::CONTAINS, self::EQUAL => self::EQUAL,
            self::LESS_THAN => self::LESS_THAN,
            self::LT_OR_EQUAL => self::LT_OR_EQUAL,
            self::GREATER_THAN => self::GREATER_THAN,
            self::GT_OR_EQUAL => self::GT_OR_EQUAL,
            self::NOT_EQUAL => self::NOT_EQUAL,
            self::SQL_IS_NULL => self::SQL_IS_NULL,
            self::SQL_NOT_NULL => self::SQL_NOT_NULL,
            self::LIKE => self::LIKE
        );
    }

    /**
     * Returns a list of the unary search comparator values;
     */
    public static function unaryComparators()
    {
        return array( self::SQL_IS_NULL, self::SQL_NOT_NULL);
    }

    /**
     * Constructor
     *
     * @param Ramp_Table_SetTable $setTable the table setting
     * @param string $formType     specifies type of form (VIEW, ADD, 
     *                                  EDIT, or SEARCH)
     * @param string $makeSmall    make buttons smaller
     * @param bool   $formSuffix   a suffix to make form name unique on page
     *                             e.g., a row number
     */
    public function __construct(Ramp_Table_SetTable $setTable,
                                $formType = self::VIEW, $makeSmall = false,
                                $formSuffix = null)
    {
        $this->_setTable = $setTable;
        $this->_formSuffix = $formSuffix;
        $this->_formType = $formType;
        $this->_makeSmall = $makeSmall;
        parent::__construct();
    }

    public function init()
    {
        // TODO: Should add a filter specifying type.

        $this->setName('tableEntry' . ($this->_formSuffix ? : ""));

        // Create the form elements for all visible and primary key fields.
        $fields = $this->_setTable->getRelevantFields();
        foreach ( $fields as $field )
        {
            // Get name to use for new text element and determine label.
            $name = $field->getDbFieldName() . ($this->_formSuffix ? : "");
            $label = $field->getLabel();

            // Is this a visible field or, for example, a hidden primary key?
            if ( $field->isVisible() )
            {
                $fieldElement = $this->_createVisibleElement($field, $name,
                                                             $label);
            }
            else    // Hidden field.
            {
                $fieldElement = $this->_createHiddenElement($name, $label);
            }

            // Add element to the form and to local list of field elements.
            $this->addElement($fieldElement);
            $this->_fieldElts[$name] = $fieldElement;
        }

        // Fill in defaults if this is a modifying form.
        if ( $this->_formType == self::ADD || $this->_formType == self::EDIT )
            { $this->setDefaults($this->_setTable->getDefaults()); }
    }

    /**
     * Gets the type of this form (VIEW, ADD, EDIT, SEARCH, DEL).
     */
    public function getFormType()
    {
        return $this->_formType;
    }

    /**
     * Gets the field elements included in this form.
     */
    public function getFieldElements()
    {
        return $this->_fieldElts;
    }

    /**
     * Gets the search comparator elements included in this form (if any).
     */
    public function getComparatorElements()
    {
        return $this->_searchCompElts;
    }

    /**
     * Gets the field values from this form.
     */
    public function getFieldValues()
    {
        $results = array();

        foreach ( $this->_fieldElts as $name => $fieldElt )
        {
            $results[$name] = $fieldElt->getValue();
        }

        return $results;
    }

    /**
     * Gets the search comparator choices from this form.
     */
    public function getComparators()
    {
        $results = array();

        foreach ( $this->_fieldElts as $name => $fieldElt )
        {
            $results[$name] = $this->getComparatorFor($name);
        }

        return $results;
    }

    /**
     * Gets the chosen search comparator associated with the
     * given field element.
     *
     * @param fieldElementName
     */
    public function getComparatorFor($fieldElementName)
    {
        if ( empty($this->_searchCompElts) ||
             empty($this->_searchCompElts[$fieldElementName]) )
        {
            return self::DEFAULT_COMPARATOR;
        }
        return $this->_searchCompElts[$fieldElementName]->getValue();
    }

    /**
     * Returns true if the given field is read-only.
     * Expression fields are always read-only.
     * Everything is read-only for viewing and deletion pages.
     * Nothing is read-only for searches.
     * Imported fields are read-only when adding new records.
     * Imported fields, primary keys, and explicit read-only fields
     *      are read-only for editing.
     */
    public function fieldIsReadOnly($field)
    {
        return $field->isExpression() ||
               $this->_formType == self::VIEW ||
               $this->_formType == self::DEL ||
               ( $field->isImported() && $this->_formType != self::SEARCH ) ||
               ( $this->_formType == self::ADD && 
                   $field->initFromAnotherTable() ) ||
               ( $this->_formType == self::EDIT && 
                   (  $field->isReadOnly() || $field->isPrimaryKey()  )
               );
    }

    /**
     * Creates a visible field element, with its label and, if this is a 
     * search, with a drop-down menu of search comparator types.
     */
    protected function _createVisibleElement($field, $name, $label)
    {
        // Determine whether field is read-only or acquiring input.
        $readOnly = $this->fieldIsReadOnly($field);

        // If this is a search, generate drop-down for search comparators.
        if ( $this->_formType == self::SEARCH )
        {
            $this->_createComparatorDropDown($name);
        }

        // Should element be a drop-down menu?
        if ( ! $readOnly && $this->_valRangeDefinedExternally($field) )
        {
            $fieldElement = $this->_createFieldDropDown($field, $name);
        }
        else        // Text input (textarea or text field)
        {
            $fieldElement = $this->_createTextElement($field, $name, $readOnly);
        }

        // Get class, title, labelTitle, and required attributes.
        // They share some information.
        $required = $this->_fieldShouldBeRequired($field);
        $reqRecDecs = $this->_getReqRecDecorations($field, $required);
        $class = $this->_buildClass($field, $readOnly, $reqRecDecs);
        $title = $this->_buildTitle($field, $reqRecDecs);
        $labelTitle = $this->_getLabelTitle($field);

        $fieldElement->setLabel($label);
        $fieldElement = $this->_buildDecorators($fieldElement, $labelTitle);

        // Set conditional attributes.
        if ( $readOnly )
            { $fieldElement->readOnly = 'readOnly'; }
        if ( $required )
            { $fieldElement->setRequired($required); }
        if ( ! empty($class) )
            { $fieldElement->class = $class; }
        if ( ! empty($title) )
            { $fieldElement->title = $title; }


        return $fieldElement;
    }

    /**
     * Determines whether the field's range of values is defined externally.
     */
    protected function _valRangeDefinedExternally($field)
    {
        return $field->isEnum() || $field->validValsDefinedInExtTable();
    }

    /**
     * Creates a drop-down menu of search comparator types.
     */
    protected function _createComparatorDropDown($name)
    {
        $ddName = "$name" . self::SEARCH_COMP_SUFFIX;
        $comparatorElement = new Zend_Form_Element_Select($ddName);
        $comparatorOptions = self::validComparators();
        $comparatorElement->setMultiOptions($comparatorOptions);
        $comparatorElement = $this->_buildDecorators($comparatorElement);
        $comparatorElement->class = self::COMPARATOR_CLASS_INFO;

        // Add element to the form and to local list of
        // elements representing search comparators.
        $this->addElement($comparatorElement);
        $this->_searchCompElts[$name] = $comparatorElement;
    }

    /**
     * Creates a drop-down menu for fields with a defined set of values.
     */
    protected function _createFieldDropDown($field, $name)
    {
        // Get the set of values to choose from; if this is
        // a search, include ability to search for any value.
        $validVals = $this->_getValueRange($field);
        $options = $this->_formType == self::SEARCH
                      ? array(self::ANY_VAL => self::ANY_VAL_LABEL) +
                              $validVals
                      : $validVals;

        // Create drop-down menu with its options.
        $fieldElement = new Zend_Form_Element_Select($name);
        $fieldElement->setMultiOptions($options);

        return $fieldElement;
    }

    /**
     * Gets the field's externally-defined range of values.
     */
    protected function _getValueRange($field)
    {
        return $field->isEnum() ? $field->getEnumValues()
                                : $field->getValidVals();
    }

    /**
     * Creates a text element (textarea or text field), with appropriate 
     * validators if acquiring input.
     */
    protected function _createTextElement($field, $name, $readOnly)
    {
        // Create text input (textarea or text field).
        if ( $this->_isBlock($field->getDataType()) )
        {
            $fieldElement = new Zend_Form_Element_Textarea($name);
        }
        else
        {
            $fieldElement = new Zend_Form_Element_Text($name);
        }

        // Add appropriate filters and validators.
        $fieldElement->addFilter('StripTags')
                     ->addFilter('StringTrim');
        if ( $this->_formType != self::SEARCH && ! $readOnly )
        {
            $fieldElement->addValidators($this->_getValidators($field));
        }

        return $fieldElement;
    }

    /**
     * Builds decorators for visible fields.
     *
     * @param element   the form element to which to add decorators
     */
    protected function _buildDecorators($element, $labelTitle = "")
    {
        // Add a ViewHelper decorator with the alias "Elem".
        $element->addDecorator(array('Elem' => 'ViewHelper'),
                               array('separator' => ''));

        // Add a decorator for errors.
        $element->addDecorator('Errors', array('separator' => ''));

        // Add a decorator for the label.
        if ( ! empty($labelTitle) )
        {
            $element->addDecorator('Label', array('separator' => '',
                                                  'title' => $labelTitle));
        }

        return $element;
    }

    /**
     * Checks whether the given type is a large data type (e.g., "text"
     * rather than "varchar").
     */
    protected function _isBlock($type)
    {
        return $this->_endsIn($type, "blob") ||
               $this->_endsIn($type, "text");
    }

    /**
     * Checks whether the given full string has the given end pattern at 
     * the end, e.g., _endsIn("varchar", "char") is true.
     */
    protected function _endsIn($fullString, $endPattern)
    {
        return substr($fullString, -strlen($endPattern)) == $endPattern;
    }

    /**
     * Gets validator(s) depending on the field's data type.
     *
     * TODO: Enforce the number of characters specified for integer
     *          types as a maximum, rather than just assuming the 
     *          number is a display hint.
     *
     * TODO: Need to determine correct validators for other types:
     *          Fixed-Point, Floating Point, Bit-Value, Set
     *       Need to test Time and Year.
     *
     * Don't need anything for enum, because handled with pull-down
     * selections.
     *
     * Do not add locale handling to date/int check:  using yyyy-MM-dd
     * allows for '<' and '>' comparisons.
     */
    protected function _getValidators($field)
    {
        $validators = array();
        $date = "yyyy-MM-dd";
        $time = "HH:MM:SS";

        // Categorize the field's data type.
        $type = $field->getDataType();
        if ( $this->_endsIn($type, "int") )
            $typeCat = "intType";
        else if ( $this->_endsIn($type, "char") ||
                  $this->_endsIn($type, "binary") ||
                  $this->_endsIn($type, "blob") ||
                  $this->_endsIn($type, "text") )
            $typeCat = "stringType";
        else
            $typeCat = $type;
        $maxLength = $field->getLength();

        switch ( $typeCat )
        {
            case "intType":
                $validators[] = array('digits');
                break;
            case "stringType":
                if ( $maxLength != 0 )
                {
                    $validators[] = array('stringlength', false,
                                            array('max' => $maxLength));
                }
                break;
            case "date":
                $validators[] = array('date');
                break;
            case "time":
                $validators[] = array('date', false,
                                        array('format' => $time));
                break;
            case "datetime":
            case "timestamp":
                $validators[] = array('date', false,
                                        array('format' => "$date $time"));
                break;
                break;
            case "year":  // should validate for 'YY' or 'YYYY'
                $length = $maxLength == 2 ? 2 : 4;
                $validators[] = array('digits');
                $validators[] = array('stringlength', false,
                                        array('min' => $length,
                                              'max' => $length));
                break;
            default:
        }

        return $validators;
    }

    /**
     * Returns true if the given field should be required.
     */
    protected function _fieldShouldBeRequired($field)
    {
        // "Required" fields do not have to be marked as required on 
        // search forms, nor on modifying forms if a default is being 
	// provided or if the field should be initialized by data from
	// another table.
        return $this->_formType != self::SEARCH &&
               $field->valueNecessaryForAdd();
    }

    /**
     * Gets appropriate class and title notations for 
     * the given $field if it is an identity (auto-incremented field)
     * or a required or recommended field.
     *
     * @param $field      the field needing decoration
     * @param $required   whether the field is required
     * @returns array     an array with label, class, and title information
     */
    protected function _getReqRecDecorations($field, $required)
    {
        // Draw attention to required and recommended fields unless this 
        // is a Search request.
        $reqRecDecs['class'] = "";
        $reqRecDecs['title'] = "";

        if ( $this->_formType == self::SEARCH )
            { return $reqRecDecs; }

        if ( $field->isAutoIncremented() )
        {
            $reqRecDecs['class'] = "discouraged";
            $reqRecDecs['title'] = self::AUTO_INCR_EXPL;
        }
        elseif ( $required )
        {
            $reqRecDecs['class'] = "required";
            $reqRecDecs['title'] = self::REQUIRED_EXPL;
        }
        elseif ( $field->isRecommended() )
        {
            $reqRecDecs['class'] = "recommended";
            $reqRecDecs['title'] = self::RECOMMENDED_EXPL;
        }

        if ( $field->isDiscouraged() )
        {
            $reqRecDecs['class'] = "discouraged";
        }

        return $reqRecDecs;
    }

    /**
     * Gets the class attribute for the field element.
     */
    protected function _buildClass($field, $readOnly, $reqRecDecs)
    {
        $class = $this->_makeSmall
                    ? self::SMALL_FIELD_WIDTH : self::FIELD_WIDTH;
        $class .= " " . $reqRecDecs['class'];
        if ( $readOnly )
            { $class .= " readonly"; }
        return $class;
    }

    /**
     * Gets the title attribute for the field element.
     */
    protected function _buildTitle($field, $reqRecDecs)
    {
        $title = $reqRecDecs['title'];
        $footnote = $field->getFieldFootnote();
        if ( $footnote != "" )
        {
            if ( strlen($title) > 0 )
                { $title .= ": "; }
            $title .= $footnote;
        }
        return $title;
    }

    /**
     * Gets the label's title.
     */
    protected function _getLabelTitle($field)
    {
        // Provide a tooltip title if field is imported from another table,
        // is a reference to an external table, or has a footnote,
        $title = "";
        if ( $field->isImported() )
        {
            $title = sprintf(self::EXTERNAL_REF_EXPL,
                             $field->getImportTable());
        }
        if ( $field->isExternalTableLink() )
        {
            if ( strlen($title) > 0 )
                { $title .= ": "; }
            $title .= self::REFERENCE_EXPL . $field->getLinkedTableTitle();
        }
        $footnote = $field->getFieldFootnote();
        if ( $footnote != "" )
        {
            if ( strlen($title) > 0 )
                { $title .= ": "; }
            $title .= $footnote;
        }

        return $title;
    }

    /**
     * Creates a hidden field element.
     */
    protected function _createHiddenElement($name, $label)
    {
        $fieldElement = new Zend_Form_Element_Hidden($name);
        $fieldElement->setLabel($label)
                     ->setAttrib('class', 'hidden');

        // Add a ViewHelper decorator with the alias "Elem", a decorator 
        // for the label, and a decorator for errors.  These decorators 
        // hide an element, its label, and errors.
        $hiddenFieldDecParams =
                    array('separator'=>'', 'tag'=>'div', 'class'=>'hidden');
        $fieldElement->addDecorator(array('Elem' => 'ViewHelper'),
                               $hiddenFieldDecParams);
        $fieldElement->addDecorator('Label', $hiddenFieldDecParams);
        $fieldElement->addDecorator('Errors', $hiddenFieldDecParams);

        return $fieldElement;
    }

}

