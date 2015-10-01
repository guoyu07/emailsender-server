<?php
/**
 * job interface
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-09-28
 */

namespace Service\Task;

interface JobInterface {

    public function __construct($data);

    public function run();

    public function finish();

}