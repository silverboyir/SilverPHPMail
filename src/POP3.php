<?php
/**
 * Created by PhpStorm.
 * User: hossein
 * Date: 5/21/2017
 * Time: 9:12 PM
 */

namespace SilverPHPMail;
/**
 * Class POP3
 * @method POP3Iterator getLastMessages
 * @method POP3Iterator getNewMessages
 * @package SilverPHPMail
 */

class POP3 extends MailAbstract
{
    protected $_connection = 'pop3';

    protected function getNewEmailObject($header, $uid){
        return new POP3Mail($this->_resource, $header, $uid, $this->getFolder());
    }
    protected function getRowSetClass($data){
        return new POP3Iterator($data);
    }
}