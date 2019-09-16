<?php

namespace SilverPHPMail;


/**
 * Class IMAP
 * @package SilverPHPMail
 */
class IMAP extends MailAbstract
{

    protected $_connection = 'imap';

    protected function getNewEmailObject($header, $uid){
        return new IMAPMail($this->_resource, $header, $uid, $this->getFolder());
    }
    protected function getRowSetClass($data){
        return new IMAPIterator($data);
    }







}