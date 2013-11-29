<?php
/**
 * TwitterBootstrap_View_Helper_FlashMessages
 *
 * @package    TwitterBootstrap
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

/**
 * view helper which displays all flash messages
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class TwitterBootstrap_View_Helper_FlashMessages extends Zend_View_Helper_Abstract
{
    /**
     * id of the html container element
     * @var string 
     */
    const CONTAINER_ID = 'messages';

    /**
     * messenger
     * @var Zend_Controller_Action_Helper_FlashMessenger|null 
     */
    private $messenger = null;
    
    /**
     * get the flash messenger action helper
     * 
     * @return Zend_Controller_Action_Helper_FlashMessenger
     */
    protected function getFlashMessenger()
    {
        if ($this->messenger === null) {
            return Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        }
        
        return $this->messenger;
    }

    /**
     * gathers all messages (current and those displayed later)
     * 
     * @return array 
     */
    protected function getAllMessages()
    {
        /* @var $messages array */
        $messages = array();

        $flashMessenger = $this->getFlashMessenger();
        $namespaces = array(
            'info'    => 'info',
            'success' => 'success',
            'error'   => 'error',
            'warning' => 'warning',
            'default' => 'info'
        );

        foreach ($namespaces as $namespace => $targetNamespace) {
            $flashMessenger->setNamespace($namespace);
            $messages[$targetNamespace] = array_unique(
                array_merge($flashMessenger->getCurrentMessages(), $flashMessenger->getMessages())
            );
        }

        return $messages;
    }

    /**
     * checks if the array elements are not empty
     * 
     * @param array $messages
     * 
     * @return boolean 
     */
    protected function containsMessages(array $messages)
    {
        foreach ($messages as $messages) {
            if (!empty($messages)) {
                return true;
            }
        }
        return false;
    }

    /**
     * public helper accessor method
     * 
     * @return string 
     */
    public function flashMessages(Zend_Controller_Action_Helper_FlashMessenger $messenger = null)
    {
        if ($messenger !== null) {
            $this->messenger = $messenger;
        }
        
        return $this;
    }
    
    /**
     * render the flash messages including a close button
     * 
     * @param string $text
     * 
     * @return string
     */
    public function withCloseButton($text)
    {
        $text = $this->view->translate($text);
        $closeButton = '<button class="' . TwitterBootstrap_Css::CLOSE 
            . '" tabindex="" data-dismiss="alert" type="button" title="' . $text . '>
            <i class="icon-remove">' . $text . '</i>
            </button>';
        
        return $this->displayMessages($closeButton);
    }
    
    /**
     * renders the output
     * 
     * @param string $closeButton
     * 
     * @return string 
     */
    protected function displayMessages($closeButton = '')
    {
        $buffer = '';
        $messages = $this->getAllMessages();
        if (!$this->containsMessages($messages)) {
            return $buffer;
        }
        
        foreach ($messages as $namespace => $namespaceMessages) {
            if (empty($namespaceMessages)) {
                continue;
            }
            $this->getFlashMessenger()->setNamespace($namespace)->clearCurrentMessages();
            $buffer .= '<div class="' . TwitterBootstrap_Css::ALERT . ' alert-' . $namespace . '">';
            $buffer .= $closeButton;
            
            foreach ($namespaceMessages as $message) {
                $buffer .= $this->view->translate($message) . '<br />';
            }
            $buffer .= '</div>' . PHP_EOL;
        }
        //reset namespace
        $this->getFlashMessenger()->setNamespace();

        return '<div id="' . self::CONTAINER_ID . '">' . $buffer . '</div>' . PHP_EOL;
    }

    /**
     * toString causes rendering of the messages
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->displayMessages();
    }
}