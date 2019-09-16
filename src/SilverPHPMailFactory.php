<?php
namespace SilverPHPMail;

class SilverPHPMailFactory {
    public static $IMAP = 'IMAP';
    public static $POP3 = 'POP3';
    /**
     * $config = array(
            'server' => string "server IP",
            'port' => int "Port",
            'secure_mode' => boolean
            'validate_cert' => 1,
            'username' => String,
            'password' => String,
        );
     * @param array $config
     * @return IMAP|POP3
     * @throws \Exception
     */
    public static function getMailReader($config){
        if($config['connection'] === self::$IMAP){
            return new IMAP($config);
        }
        return new POP3($config);
    }
}