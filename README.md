EmailSender Server
============

## Introduction

EmailSender Server 是一个基于 phpmailer 与 swoole 扩展的邮件服务，通过tcp方式连接进行异步发送邮件的服务。

Requirements
------------

**PHP5.4.0 or later**

Setup Database And Application
-----------

### Create database
```
CREATE DATABASE `mail` CHARACTER SET utf8 COLLATE utf8_general_ci; 
USE `mail`;
CREATE TABLE `mail` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) NOT NULL,
  `body` text,
  `to` varchar(2000) NOT NULL,
  `cc` varchar(2000) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1: waiting, 2: pending, 3:complete',
  `ctime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'create time',
  `ftime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Finish time',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

### Setup application
```
cd yourproject/
composer install
# make a symbol link.
ln -s /usr/local/php-5.4.44/bin/php /usr/local/bin/php
# create database config file.
echo '[common]
database_type = mysql
port          = 3306
charset       = utf8
[local : common]
server        = 127.0.0.1
username      = root
password      = root
database_name = mail

[test : common]
[product : common]' > application/config/database.ini
# create mail config file.
echo '[common]
server.log_file        = /tmp/output.log
server.max_request     = 1000
server.worker_num      = 2
server.task_worker_num = 2
server.user            = nobody
server.group           = nobody

phpmail.charset    = "utf-8"
phpmail.host       = "smtp.gmail.com"
phpmail.smtpauth   = 1
phpmail.username   = {your_username}
phpmail.password   = {your_password}
phpmail.smtpsecure = tls
phpmail.port       = 587
phpmail.from       = "no-reply@example.com"
phpmail.fromname   = "No Reply Example"
phpmail.wordwrap   = 100

[local : common]
[test : common]
[product : common]' > application/config/mail.ini
```

Usage
-----------
### start server.
```
./server.sh start
```

### telnet

协议采用空格或者制表符做分隔，命令格式：CMD ARG1 ARG2 ARG3 ...
返回格式：CODE RESPONSE

注：若ARG存在空格，需要通过 rawurlencode

```
[lancer@Lancer-MacBookPro ~]$ telnet 127.0.0.1 8001
Trying 127.0.0.1...
Connected to localhost.
Escape character is '^]'.
send subject emailbody user1@example.com;user2@examle.com user3@example.com
0 129
```

上例发送主题：subject，主体内容：emailbody，发送邮件：user1;user2，抄送：user3  
CODE=0表示成功，129表示Email Id，可以通过 `status 129` 查询邮件是否异步发送完成  
CODE>0表示失败，message 通过协议方式追加状态码后。

### use php in client

```php
class Util_Mail {

    public static function send($subject, $body, $to, $cc='') {
        $client = new swoole_client(SWOOLE_SOCK_TCP | SWOOLE_KEEP);
        $client->set(['open_eof_check' => true, 'package_eof' => "\r\n"]);
        if ( ! $client->connect('127.0.0.1', 8001))
            return false;
        $to = is_array($to) ? implode(";", $to) : $to;
        $cc = is_array($cc) ? implode(";", $cc) : $cc;
        $packet = sprintf("send %s %s %s %s\r\n", 
            rawurlencode($subject),
            rawurlencode($body),
            rawurlencode($to),
            rawurlencode($cc)
        );
        if ( ! $client->send( $packet ) )
            return false;

        $response = preg_split("/[\s]+/", substr($client->recv(), 0, -2) );
        $client->close();
        return $response;
    }
}

Util_Mail::send("subject", "body", "user1@example.com");
Util_Mail::send("subject", "body", ["user1@example.com", "user2@example.com"]);
Util_Mail::send("subject", "body", ["user1@example.com", "user2@example.com"], "user3@example.com");
Util_Mail::send("subject", "body", ["user1@example.com", "user2@example.com"], ["user3@example.com", "user4@example.com"]);
```