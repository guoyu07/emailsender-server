<?php
/**
 * TCP Packet Processor interface
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-09-28
 */

namespace Service\Packet;

interface CommandInterface {

    public function __construct($args);

    public function validate();

    public function process(\swoole_server $server);

}