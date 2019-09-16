# SilverPHPMail

Fetch Email From IMAP - POP3 Server

## Installation

```
php composer.phar require silverboyir/silverphpmail
```



## Usage

Pass the config for make connection to the server and get new or all messages by calling getNewMessages() method
```
SilverPHPMailFactory::getMailReader($config)->getNewMessages($lastMessageId)
```

this will return an iterator of last emails
## Config

| property | type | default | Description |
| --- | --- | --- | --- |
| `connection` | string | POP3 | IMAP or POP3 
| `server` | string | null | IMAP or POP3 server address
| `port` | int | 0 | port of IMAP or POP3 server
| `secure_mode` | string | '' | pass SSL for secure connection
| `validate_cert` | boolean | false | 
| `folder` | string | 'INBOX' | 
| `username ` | string | '' | 
| `password ` | string | '' | 

## TODO
* writing test




