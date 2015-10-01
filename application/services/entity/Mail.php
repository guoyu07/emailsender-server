<?php
/**
 * Mail entity.
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-09-28
 */

namespace Service\Entity;

use Core\Model\Medoo;

class Mail {

    public $subject;

    public $body;

    public $to;

    public $cc;

    public $id;

    public function setSubject($subject) {
        $this->subject = trim($subject);
    }

    public function setBody($body) {
        $this->body = $body;
    }

    public function setTo($to) {
        $this->to = explode(";", $to);
    }

    public function setCc($cc) {
        $this->cc = explode(";", $cc);
    }

    public function create() {
        $this->id = ( new Medoo() )->medoo()->insert('mail', [
            'subject' => $this->subject,
            'body'    => $this->body,
            'to'      => json_encode($this->to),
            'cc'      => json_encode($this->cc),
            'ctime'   => time(),
        ]);
    }

    public function complete() {
        return ( new Medoo() )->medoo()->update('mail', ['status' => 3, 'ftime' => time()], ['id' => $this->id]);
    }

    public function pending() {
        return ( new Medoo() )->medoo()->update('mail', ['status' => 2], ['id' => $this->id]);
    }

    public function send() {
        $config = \Yaf_Registry::get('config')->phpmail;
        $Mail = new \PHPMailer();
        $Mail->isSMTP();
        $Mail->CharSet    = $config->charset;            // Set Mailer to use SMTP
        $Mail->Host       = $config->host;               // Specify main and backup SMTP servers
        $Mail->SMTPAuth   = $config->smtpauth;           // Enable SMTP authentication
        $Mail->Username   = $config->username;           // SMTP username
        $Mail->Password   = $config->password;           // SMTP password
        $Mail->SMTPSecure = $config->smtpsecure;         // Enable TLS encryption, `ssl` also accepted
        $Mail->Port       = $config->port;
        $Mail->From       = $config->from;
        $Mail->FromName   = $config->fromname;
        $Mail->WordWrap   = $config->wordwrap;           // Set word wrap to 50 characters
        $Mail->isHTML(true);                             // Set eMail format to HTML
        foreach ($this->to as $name => $address) 
            $Mail->addAddress($address, (is_string($name) ? $name : '') );

        foreach ($this->cc as $name => $address) 
            $Mail->addCC($address, (is_string($name) ? $name : '') );

        $Mail->Subject = $this->subject;
        $Mail->Body    = $this->body;
        return $Mail->send();
    }
}