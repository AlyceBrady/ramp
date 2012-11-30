<?php

/**
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

class Application_Form_TableRecordEntry extends Zend_Form
{
    const VIEW   = 'view';
    const ADD    = TableController::ADD;
    const EDIT   = TableController::EDIT;
    const SEARCH = TableController::SEARCH;
    const DEL    = TableController::DEL_BUTTON;
    const ANY_VAL = TableController::ANY_VAL;
    const ANY_VAL_LABEL = "ANY VALUE";
    const AUTO_INCR_EXPL = "Auto-incremented by default";
    const REQUIRED_EXPL = "Required field";
    const RECOMMENDED_EXPL = "Recommended field";
    const REFERENCE_EXPL = "Reference to field in ";
    const EXTERNAL_REF_EXPL = "From %s table";

    protected $_setTable;
    protected $_formSuffix;
    protected $_formType;

    /**
     * Constructor
     *
     * @param Application_Model_TableSetting $setTable the table setting
     * @param bool   $formSuffix   a suffix to make form name unique on page
     *                             e.g., a row number
     * @param string $formType     specifies type of form (VIEW, ADD, 
     *                                  EDIT, or SEARCH)
     */
    public function __construct(Application_Model_SetTable $setTable,
                                    $formSuffix = 0,
                                    $formType = self::VIEW)
    {
        $this->_setTable = $setTable;
        $this->_formSuffix = $formSuffix;
        $this->_formType = $formType;
        parent::__construct();
    }

    public function init()
    {
        // should add a filter specifying type

        $this->setName('tableEntry' . $this->_rowNum);

        $visibleFieldDecorators = array(
            array(array('Elem' => 'ViewHelper'), array('separator' => '')),
            array('Errors', array('separator' => '')),
        );
        $hiddenFieldDecParams =
                    array('separator'=>'', 'tag'=>'div', 'class'=>'hidden');
        $hiddenFieldDecorators = array(
            array(array('Elem' => 'ViewHelper'), $hiddenFieldDecParams),
            array('Label', $hiddenFieldDecParams),
            array('Errors', $hiddenFieldDecParams),
        );

        // Create the form elements for all visible and primary key fields.
        $fields = $this->_setTable->getRelevantFields();
        foreach ( $fields as $field )
        {
            $name = $field->getDbFieldName();
            if ( $field->isVisible() )
            {
                // Get identity/required/recommended decorations.
                $reqRecDecs = $this->_getReqRecDecorations($field);
                $visibleFieldDecorators[] = $this->_getLabelDecs($field);

                // Determine label and class/required/read-only attributes.
                $label = $this->_getLabel($field, $reqRecDecs);
                $class = $this->_getClass($field, $reqRecDecs);
                $required = $this->_isRequired($class);
                $readOnly = $this->_fieldIsReadOnly($field);

                if ( $field->isEnum() && ! $readOnly )
                {
                    // If this is a search, add ability to search for any value
                    $options = ( $this->_formType == self::SEARCH ) ?
                                  array(self::ANY_VAL => self::ANY_VAL_LABEL) +
                                        $field->getEnumValues() :
                                  $field->getEnumValues();
                    $fieldElement = new Zend_Form_Element_Select($name);
                    $fieldElement->setLabel($label)
                                 ->setMultiOptions($options)
                                 ->setAttrib('class', $class)
                                 ->setRequired('required', $required)
                                 ->setDecorators($visibleFieldDecorators);
                    $this->addElement($fieldElement);
                }
                else
                {
                    // Create text input.
                    $this->addElement('text', $name, array(
                        'class' => $class,
                        'label' => $label,
                        'required' => $required,
                        'decorators' => $visibleFieldDecorators,
                    ));
                    $fieldElement = $this->getElement($name);

                    // Add appropriate filters.
                    $fieldElement->addFilter('StripTags')
                                 ->addFilter('StringTrim');
                    if ( $this->_formType != self::SEARCH && ! $readOnly )
                    {
                        $fieldElement->addValidators(
                                        $this->_getValidators($field));
                    }
                }

                // Set conditional attributes.
                if ( $readOnly )
                    { $fieldElement->setAttrib('readOnly', 'readOnly'); }

                $title = $this->_getTitle($field, $reqRecDecs);
                if ( strlen($title) > 0 )
                    { $fieldElement->setAttrib('title', $title); }

            }
            else    // Hidden primary key.
            {
                $fieldElement = new Zend_Form_Element_Hidden($name);
                $fieldElement->setLabel($field->getLabel())
                             ->setAttrib('class', 'hidden')
                             ->setDecorators($hiddenFieldDecorators);
                $this->addElement($fieldElement);
            }
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
     * Returns true if the given field is read-only.
     * Everything is read-only for viewing and deletion pages.
     * Nothing is read-only for searches.
     * Imported fields are read-only when adding new records.
     * Imported fields, primary keys, and explicit read-only fields
     *      are read-only for editing.
     */
    protected function _fieldIsReadOnly($field)
    {
        return $this->_formType == self::VIEW ||
               $this->_formType == self::DEL ||
               ( $field->isImported() && $this->_formType != self::SEARCH ) ||
               ( $this->_formType == self::ADD && 
                   $field->initFromAnotherTable() ) ||
               ( $this->_formType == self::EDIT && 
                   (  $field->isReadOnly() || $field->isPrimaryKey()  )
               );
    }

    /**
     * Gets appropriate label prefixes and class and title notations for 
     * the given $field if it is an identity (auto-incremented field)
     * or a required or recommended fields.
     *
     * @param $field   the field needing decoration
     * @returns array  an array with label, class, and title information
     */
    protected function _getReqRecDecorations($field)
    {
        // Draw attention to required and recommended fields unless this 
        // is a Search request.
        $reqRecDecs['label'] = "";
        $reqRecDecs['class'] = "";
        $reqRecDecs['title'] = "";

        if ( $this->_formType == self::SEARCH )
            { return $reqRecDecs; }

        if ( $field->isAutoIncremented() )
        {
            $reqRecDecs['class'] = "discouraged";
            $reqRecDecs['title'] = self::AUTO_INCR_EXPL;
        }
        elseif ( $this->_fieldShouldBeRequired($field) )
        {
            // $reqRecDecs['label'] .= "**";
            $reqRecDecs['class'] = "required";
            $reqRecDecs['title'] = self::REQUIRED_EXPL;
        }
        elseif ( $field->isRecommended() )
        {
            // $reqRecDecs['label'] .= ">";
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
     * Returns true if the given field should be required.
     */
    protected function _fieldShouldBeRequired($field)
    {
        // "Required" fields do not have to be marked as required on 
        // search forms, nor on modifying forms if a default is being 
	// provided or if the field should be initialized by data from
	// another table.
        return $this->_formType != self::SEARCH &&
               $field->isRequired() &&
               $field->getDefault() == null && 
               ! $field->initFromAnotherTable();
    }

    /**
     * Returns true if the given field is required.
     */
    protected function _isRequired($class)
    {
        return $class == "required";
    }

    /**
     * Get's the field's label.
     */
    protected function _getLabel($field, $reqRecDecs)
    {
        // Get label for field.
        return $reqRecDecs['label'] . $field->getLabel();
    }

    /**
     * Get's the field's label decorators.
     */
    protected function _getLabelDecs($field)
    {
        $labelDecs['separator'] = '';

        // Provide a tooltip title if field has a footnote, is a reference
        // to an external table, or is imported from another table.
        $title = "";
        if ( $field->isExternalTableLink() )
        {
            $table = $field->getLinkedTable();
            $title = self::REFERENCE_EXPL . "$table";
        }
        $footnote = $field->getFieldFootnote();
        if ( $footnote != "" )
        {
            if ( strlen($title) > 0 )
                { $title .= ": "; }
            $title .= $footnote;
        }
        if ( $field->isImported() )
        {
            $title = sprintf(self::EXTERNAL_REF_EXPL,
                             $field->getImportTable());
        }

        // Add tooltip title to decorators.
        if ( strlen($title) > 0 )
        {
            $labelDecs['title'] = $title;
        }
        return array('Label', $labelDecs);
    }

    /**
     * Gets the class attribute for the field element.
     */
    protected function _getClass($field, $reqRecDecs)
    {
        $class = $reqRecDecs['class'];
        if ( $this->_fieldIsReadOnly($field) )
            { $class .= " readonly"; }
        return $class;

        if ( strlen($class) > 0 )
            { $fieldElement->setAttrib('class', $class); }

        return $fieldElement;
    }

    /**
     * Gets the title attribute for the field element.
     */
    protected function _getTitle($field, $reqRecDecs)
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

        if ( strlen($title) > 0 )
            { $fieldElement->setAttrib('title', $title); }

        return $fieldElement;
    }

    /**
     * Gets validator(s) depending on the field's data type.
     * TODO: Enforce the number of characters specified for integer
     *          types as a maximum, rather than just assuming the 
     *          number is a display hint.
     * TODO: Need to determine correct validators for other types:
     *          Fixed-Point, Floating Point, Bit-Value, Set
     *       Need to test Time and Year.
     * (Don't need anything for enum, because handled with pull-down
     * selections.)
     * TODO: May want to add locale handling to date/int check.
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
     * Checks whether the given full string has the given end pattern at 
     * the end, e.g., _endsIn("varchar", "char") is true.
     */
    protected function _endsIn($fullString, $endPattern)
    {
        return substr($fullString, -strlen($endPattern)) == $endPattern;
    }


}

