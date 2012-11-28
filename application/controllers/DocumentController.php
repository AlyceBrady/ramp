<?php

// This is a quick first pass at a controller that will control access 
// to static documents (e.g., plain text or documents in HTML, 
// Markdown, or other formats).

// This controller is not currently used -- instead, static documents 
// must be created as activity lists with a single activity whose type 
// is "html" and then the ActivityController is handling those.

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
 * @version    $Id: DocumentController.php 1 2012-11-28 alyce $
 *
 */

class DocumentController extends Zend_Controller_Action
{
    const DOCUMENT_KEYWORD = 'document';

    public function init()
    {
    }

    public function indexAction()
    {
        // Get the document file associated with the name passed as a 
        // parameter.
        $rawDocFilename = $this->_getParam(self::DOCUMENT_KEYWORD);
        $documentFilename = urldecode($rawDocFilename);
        // TODO: Should use a different gateway and different access function.
        $gateway = new Application_Model_ActivityGateway();
        $contents = $gateway->getActivityList($documentFilename);

        // This is not a dynamic page, so don't have to differentiate 
        // between the initial display or a callback from a button action.

        // Make the document contents available to the View Renderer.
        $this->view->pageContents = $contents;
    }

}

