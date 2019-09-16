<?php
/**
 * Created by PhpStorm.
 * User: hossein
 * Date: 5/21/2017
 * Time: 9:07 PM
 */

namespace SilverPHPMail;


class AbstractMail implements Mail
{
    /**
     * @var String
     */
    private $date;
    /**
     * @var String
     */
    private $subject;

    /**
     * @var String
     */
    private $messageId;

    /**
     * @var Contact
     */
    private $to;

    /**
     * @var Contact
     */
    private $reply_to;

    /**
     * @var Contact
     */
    private $sender;

    /**
     * @var int
     */
    private $_messageNumber;

    /**
     * @var String
     */
    private $_size;

    /**
     * @var
     */
    private $_resource;

    /**
     * @var String
     */
    private $_body = null;

    /**
     * @var String
     */
    private $_uid;

    private $_folder;


    function __construct($resource, \stdClass $obj, $uid, $folder)
    {

        $this->_resource = $resource;
        $this->_folder = $folder;
        $this->_uid = $uid;

        $dateParsed = explode(' ', $obj->date);
        $timezone = array_pop($dateParsed);
        $time =  strtotime(implode(' ', $dateParsed));


        $original = new \DateTime('now', new \DateTimeZone('UTC'));
        $original->setTimestamp($time);

        try {
            $hour = substr($timezone, 0, 2);
            $minutes = substr($timezone, 2, 2);
            $hour = (int)$hour;
            $minutes = (int) $minutes;

            $timezone = ($hour*3600)+($minutes*60);
            if($timezone > 0){
                $timezoneName = timezone_name_from_abbr("", $timezone, false);
                $modified = $original->setTimezone(new \DateTimezone($timezoneName));
                $this->setDate($modified);
            }
            else
                $this->setDate($original);
        }
        catch (\Exception $e){

        }





        try {
            $this->setSubject($obj->subject);
            $this->setMessageId($obj->message_id);
            $this->setMessageNumber($obj->Msgno);
            if(isset($obj->from))
                $this->setTo(new Contact($obj->from[0]));
            if(isset($obj->reply_to))
                $this->setReplyTo(new Contact($obj->reply_to[0]));
            if(isset($obj->sender))
                $this->setSender(new Contact($obj->sender[0]));
            $this->setSize($obj->Size);
        }
        catch (\Error $e){
        }



    }

    /**
     * @return String
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param String $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return String
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param String $subject
     */
    public function setSubject($subject)
    {
        $this->subject = iconv_mime_decode($subject,
            0, "UTF-8");
    }

    /**
     * @return String
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * @param String $messageId
     */
    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;
    }

    /**
     * @return Contact
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param Contact $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     * @return Contact
     */
    public function getReplyTo()
    {
        return $this->reply_to;
    }

    /**
     * @param Contact $reply_to
     */
    public function setReplyTo($reply_to)
    {
        $this->reply_to = $reply_to;
    }

    /**
     * @return Contact
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param Contact $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     * @return int
     */
    public function getMessageNumber()
    {
        return $this->_messageNumber;
    }

    /**
     * @param int $messageNumber
     */
    public function setMessageNumber($messageNumber)
    {
        $this->_messageNumber = trim($messageNumber);
    }

    /**
     * @return String
     */
    public function getSize()
    {
        return $this->_size;
    }

    /**
     * @param String $size
     */
    public function setSize($size)
    {
        $this->_size = $size;
    }

    /**
     * @return String
     */
    public function getBody()
    {
        if($this->_body === null){
            $body = $this->get_part("TEXT/HTML");
            // if HTML body is empty, try getting text body
            if ($body == "") {
                $body = $this->get_part("TEXT/PLAIN");
            }
            $this->_body = $body;
        }
        return $this->_body;
    }

    public function get_part($mimetype, $structure = false, $partNumber = false) {
        if (!$structure) {
            $structure = imap_fetchstructure($this->_resource, $this->_uid, FT_UID);
        }
        if ($structure) {
            if ($mimetype == $this->get_mime_type($structure)) {
                if (!$partNumber) {
                    $partNumber = 1;
                }
                $text = imap_fetchbody($this->_resource, $this->_uid, $partNumber, FT_UID);
                switch ($structure->encoding) {
                    case 3: return imap_base64($text);
                    case 4: return imap_qprint($text);
                    default: return $text;
                }
            }

            // multipart
            if ($structure->type == 1) {
                foreach ($structure->parts as $index => $subStruct) {
                    $prefix = "";
                    if ($partNumber) {
                        $prefix = $partNumber . ".";
                    }
                    $data = $this->get_part($mimetype, $subStruct, $prefix . ($index + 1));
                    if ($data) {
                        return $data;
                    }
                }
            }
        }
        return false;
    }
    public function get_mime_type($structure) {
        $primaryMimetype = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");

        if ($structure->subtype) {
            return $primaryMimetype[(int)$structure->type] . "/" . $structure->subtype;
        }
        return "TEXT/PLAIN";
    }

    /**
     * @param String $body
     */
    public function setBody($body)
    {
        $this->_body = $body;
    }


    public function delete(){
        $ret = imap_delete($this->_resource, $this->getMessageNumber());
        imap_expunge($this->_resource);

    }




}