<?php
/**
 * Task creator
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-09-28
 */

namespace Service\Task;

class Creator {

    public static function create(\swoole_server $server, $task, $data) {
        $class_task = "\Service\Task\Job" . $task;
        if ( ! class_exists($class_task) ) {
            trigger_error("$class_task not found.", E_USER_ERROR);
        }
        $server->task(new $class_task($data));
    }
}