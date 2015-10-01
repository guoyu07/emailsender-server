<?php
/**
 * TCP Packet Processor
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-09-28
 */
namespace Service\Packet;

use Core\Exception\RequestValidateException;
use Service\Packet\CommandInterface;
use Service\Entity\Mail;
use Service\Task\Creator as TaskCreator;

class CommandSend implements CommandInterface {

    protected $_packet = [];

    public function __construct($args) {
        $this->_packet['subject'] = isset($args[0]) ? $args[0] : '';
        $this->_packet['body']    = isset($args[1]) ? $args[1] : '';
        $this->_packet['to']      = isset($args[2]) ? $args[2] : '';
        $this->_packet['cc']      = isset($args[3]) ? $args[3] : '';
    }

    public function validate() {
        if ( ! $this->_packet['subject'] )
            throw new RequestValidateException('Field subject is emptyn');
        if ( ! $this->_packet['body'] )
            throw new RequestValidateException('Field body is emptyn');
        if ( ! $this->_packet['to'] )
            throw new RequestValidateException('Field to is emptyn');
    }

    public function process(\swoole_server $server) {
        $Mail = new Mail();
        $Mail->setSubject(rawurldecode($this->_packet['subject']));
        $Mail->setBody(rawurldecode($this->_packet['body']));
        $Mail->setTo(rawurldecode($this->_packet['to']));
        $Mail->setCc(rawurldecode($this->_packet['cc']));
        $Mail->create();
        TaskCreator::create($server, 'SendMail', $Mail);
        return null;
    }
}