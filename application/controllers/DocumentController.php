<?php

require_once('Michelf/Markdown.php');
use \Michelf\Markdown;

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

class DocumentController extends Zend_Controller_Action
{
    protected $_document;
    protected $_contents;

    protected $_debugging = false;

    /**
     * Initializes attributes for this object.
     */
    public function init()
    {
        // Get the document associated with the name passed as a parameter.
        $docName =
            Ramp_Controller_KeyParameters::getKeyParam($this->getRequest());
        $this->_document = $this->_getDocumentFilename($docName);
        $this->_contents = file_get_contents($this->_document);
    }

    /**
     * Acts on a Document.
     */
    public function indexAction()
    {
        // Determine whether this is an HTML file (which does not need 
        // special processing), a Markdown file (which needs to be 
        // transformed to HTML), or plain text (which needs to be put in 
        // <PRE> tags in case line breaks are important).
        // Assumption:  all files are either HTML, Markdown, or plain text.
        if ( $this->_isHtml($this->_document) )
        {
            // do nothing
        }
        else if ( $this->_isMarkdown($this->_document) )
        {
            $this->_contents = Markdown::defaultTransform($this->_contents);
        }
        else
        {
            $this->_contents = "<PRE>\n" . $this->_contents . "\n</PRE>\n";
        }

        // Make the document contents available to the View Renderer.
        $this->view->pageContents = $this->_contents;

// $this->_debugging = true;
        $this->_debug();

    }

    /**
     * Adds debugging information to the  basic view renderer information.
     */
    protected function _debug()
    {
        if ( $this->_debugging )
        {
            $errMsg = "<pre>DEBUGGING INFO:  Request params are: "
                        . print_r($this->getRequest()->getParams(), true);
            $acl = new Ramp_Acl();
            $errMsg .= "<h5>Roles:</h5>" . var_export($acl->getRoles(), true);
            $errMsg .= "<h5>Roles:</h5>"
                        . var_export($acl->getResources(), true);
            $errMsg .= "<h5>Roles:</h5>" . var_export($acl->getRules(), true);
            $errMsg .= "</pre>";
            $this->view->errMsg = $errMsg;
        }
    }

    /**
     * Resolves the document filename to a pathname within the document
     * root directory.
     *
     * @param  string $name name passed to the Document Controller
     * @return string       the file's full pathname
     */
    protected function _getDocumentFilename($name)
    {
        // Determine the filename, based on system path and $name.
        $configs = Ramp_RegistryFacade::getInstance();
        $dir = $configs->getDocumentRoot();
        $docFile = $dir .  DIRECTORY_SEPARATOR .  $name;
        if ( ! file_exists($docFile) )
        {
            throw new Exception('Missing file: ' .  $docFile);
        }
        return $docFile;
    }

    /**
     * Determine whether this is an HTML file.
     */
    protected function _isHtml($filename)
    {
        $path_parts = pathinfo($filename);
        $fileExtension = isset($path_parts['extension']) ?
                                    $path_parts['extension'] : "";
        $fileExtension = strtolower($fileExtension);
        return ( $fileExtension == 'htm' || $fileExtension == 'html' );
    }

    /**
     * Determine whether this is a Markdown file.
     */
    protected function _isMarkdown($filename)
    {
        $path_parts = pathinfo($filename);
        $fileExtension = isset($path_parts['extension']) ?
                                    $path_parts['extension'] : "";
        $fileExtension = strtolower($fileExtension);
        return ( $fileExtension == 'md' || $fileExtension == 'mdown' ||
                 $fileExtension == 'markdown' || $fileExtension == 'mkd' );
    }

}

