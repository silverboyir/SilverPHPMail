<?php


namespace SilverPHPMail;


abstract class MailAbstract
{
    protected $_server = null;
    protected $_port = 0;
    protected $_connection = '';
    protected $_secure_mode = '';
    protected $_validate_cert = false;
    protected $_folder = 'INBOX';
    protected $_username  = '';
    protected $_password = '';
    protected $_error = '';
    protected $_numberOfMessages = null;
    public $maxFetch = 100;

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->_username = $username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->_password = $password;
    }

    /**
     * @return string
     */
    public function getFolder()
    {
        return $this->_folder;
    }

    /**
     * @param string $folder
     */
    public function setFolder($folder)
    {
        $this->_folder = $folder;
    }

    /**
     * @var
     */
    protected $_resource = null;

    function __construct($options = array())
    {
        if(isset($options['server']))
            $this->setServer($options['server']);
        if(isset($options['port']))
            $this->setPort($options['port']);
        if(isset($options['connection']))
            $this->setConnection($options['connection']);
        if(isset($options['secure_mode']))
            $this->setSecureMode($options['secure_mode']);

        if(isset($options['validate_cert']))
            $this->setValidateCert($options['validate_cert']);

        if(isset($options['folder']))
            $this->setFolder($options['folder']);
        if(isset($options['username']))
            $this->setUsername($options['username']);
        if(isset($options['password']))
            $this->setPassword($options['password']);

        $this->_resource = imap_open($this->generateUrl(), $this->getUsername(), $this->getPassword());
        if(empty($this->_resource)){
            $error = imap_last_error();
            throw new \Exception($error, 500);
        }

    }

    public function getNumberOfMessages(){
        if($this->_numberOfMessages === null)
            $this->_numberOfMessages = imap_num_msg($this->_resource);
        return $this->_numberOfMessages;
    }

    function __destruct()
    {
        imap_close($this->_resource);
    }

    /**
     * @param int $count
     * @return IMAPIterator
     */
    public function getLastMessages($count = 0){
        $numMessages = imap_num_msg($this->_resource);
        $start = $numMessages-$count;
        $data = array();
        $fetched = 0;
        for($start;$start <= $numMessages;$start++){
            $header = imap_header($this->_resource, $start);
            $uid = imap_uid($this->_resource, $start);
            if($header == false){
                $fetched++;
                continue;
            }
            $obj = $this->getNewEmailObject($header, $uid);
            $data[] = $obj;
            $fetched++;
            if($fetched >= $this->maxFetch)
                break;

        }
        $rowSet = $this->getRowSetClass(array('data' => $data));
        return $rowSet;

    }

    /**
     * @param $lastId
     * @return IMAPIterator
     */
    public function getNewMessages($lastId = 1){
        if(empty($lastId))
            $lastId = 1;
        if($this->getNumberOfMessages() <= $lastId)
            return array();
        return $this->getLastMessages($this->getNumberOfMessages()-$lastId);
    }


    private function generateUrl(){
        $url = '{'.$this->getServer().':'.$this->getPort().'/'.$this->getConnection();
        if($this->getSecureMode()){
            $url .= '/'.$this->getSecureMode();
        }
        if(!$this->isValidateCert())
            $url .= '/novalidate-cert';
        $url .= '}';


        if($this->getFolder())
            $url .= $this->getFolder();

        return $url;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }


    /**
     * @return string
     */
    public function getServer()
    {
        return $this->_server;
    }

    /**
     * @param string $server
     */
    public function setServer($server)
    {
        $this->_server = $server;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->_port;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->_port = (int) $port;
    }

    /**
     * @return string
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * @param string $connection
     */
    public function setConnection($connection)
    {
        $connection = strtolower($connection);
        if(!in_array($connection, array('imap', 'pop3')))
            throw new \Exception('This Library Only Supports IMAP and POP3', 500);
        $this->_connection = $connection;
    }

    /**
     * @return string
     */
    public function getSecureMode()
    {
        return $this->_secure_mode;
    }

    /**
     * @param string $secure_mode
     */
    public function setSecureMode($secure_mode)
    {
        $this->_secure_mode = strtolower($secure_mode);
    }

    /**
     * @return bool
     */
    public function isValidateCert()
    {
        return $this->_validate_cert;
    }

    /**
     * @param bool $validate_cert
     */
    public function setValidateCert($validate_cert)
    {
        $this->_validate_cert = $validate_cert;
    }

    abstract protected function getNewEmailObject($header, $uid);
    abstract protected function getRowSetClass($data);

}