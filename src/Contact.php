<?php
/**
 * Created by PhpStorm.
 * User: hossein
 * Date: 5/21/2017
 * Time: 9:36 PM
 */

namespace SilverPHPMail;


class Contact
{
    /**
     * @var String
     */
    private $_personal;

    /**
     * @var String
     */
    private $_mailbox;

    /**
     * @var String
     */
    private $_host;

    /**
     * @return String
     */
    public function getPersonal()
    {
        return $this->_personal;
    }

    /**
     * @param String $personal
     */
    public function setPersonal($personal)
    {
        $this->_personal = $personal;
    }

    /**
     * @return String
     */
    public function getMailbox()
    {
        return $this->_mailbox;
    }

    /**
     * @param String $mailbox
     */
    public function setMailbox($mailbox)
    {
        $this->_mailbox = $mailbox;
    }

    /**
     * @return String
     */
    public function getHost()
    {
        return $this->_host;
    }

    /**
     * @param String $host
     */
    public function setHost($host)
    {
        $this->_host = $host;
    }


    function __construct(\stdClass $config)
    {
        if(isset($config->personal))
            $this->setPersonal($config->personal);
        if(isset($config->mailbox))
            $this->setMailbox($config->mailbox);
        if(isset($config->host))
            $this->setHost($config->host);
    }

    public function __toString()
    {
        return $this->getPersonal().' <'.$this->getMailbox().'@'.$this->getHost().'>';
    }
}