<?php
/**
 * 解析TCP Packet
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-09-28
 */
namespace Service\Packet;

use Core\Exception\RequestParameterException;

class Parse {

    protected $_packet, $_args, $_cmd;

    /**
     * construct
     */
    public function __construct($packet) {
        $this->_packet = $packet;
        $this->_parse();
    }

    protected function _parse() {
        $this->_args = preg_split("/[\s]+/", substr($this->_packet, 0, -2) );
        $this->_cmd  = array_shift($this->_args);
    }

    public function dispatcher(\swoole_server $server) {
        $class_cmd = "\Service\Packet\Command" . ucwords($this->_cmd);
        if ( ! class_exists($class_cmd) )
            throw new RequestParameterException("Command not found.");

        $Command = new $class_cmd($this->_args);
        $Command->validate();
        return $Command->process($server);
    }
}