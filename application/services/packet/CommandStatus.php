<?php
/**
 * Status  E-Mail 查看邮件发送状态
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-09-28
 */
namespace Service\Packet;

use Core\Exception\RequestValidateException;
use Service\Packet\CommandInterface;
use Service\Entity\Mail;

class CommandStatus implements CommandInterface {

    protected $_id;

    public function __construct($args) {
        $this->_id = isset($args[0]) ? $args[0] : 0;
    }

    public function validate() {
        if ( ! $this->_id )
            throw new RequestValidateException('Field E-Mail id is empty.');
    }

    public function process(\swoole_server $server) {
        $Mail = new Mail();
        $Mail->query($this->_id);
        return $Mail->status;
    }
}