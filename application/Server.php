<?php
/**
 * Server 服务
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-09-27
 */

define('APPLICATION_NOT_RUN', true);

// Import application and bootstrap.
\Yaf_Loader::import( dirname(__FILE__) . '/../public/index.php' );
\Yaf_Registry::set('config', new \Yaf_Config_Ini(APPLICATION_CONFIG_PATH . '/mail.ini', \YAF_ENVIRON) );

class Server {

    public function console($output) {
        echo sprintf("[%s]\t%s", date('Y-m-d H:i:s'), $output ) . PHP_EOL;
    }

    public function response($code, $message) {
        return sprintf("%d %s", $code, rawurlencode( $message ) ) . PHP_EOL;
    }

    public function __construct(){
        $server = new swoole_server('0.0.0.0', 8001);
        $config = \Yaf_Registry::get('config')->server;
        $server->set([
            'worker_num'      => $config->worker_num,
            'task_worker_num' => $config->task_worker_num,
            'daemonize'       => true,
            'log_file'        => $config->log_file,
            'max_request'     => $config->max_request,
            'dispatch_mode'   => 2,
            'open_eof_check'  => true, //打开EOF检测
            'package_eof'     => "\r\n", //设置EOF
            'user'            => $config->user,
            'group'           => $config->group,
            'debug_mode'      => 1,
        ]);

        $server->on('Start',   [$this, 'onStart']);
        $server->on('Connect', [$this, 'onConnect']);
        $server->on('Receive', [$this, 'onReceive']);
        $server->on('Task',    [$this, 'onTask']);
        $server->on('Finish',  [$this, 'onFinish']);

        $server->start();
    }

    public function onStart() {
        $this->console("E-mail Sender Server start...");
    }

    public function onConnect($server, $fd_id, $from_id){
        $this->console("TCP connection id($fd_id) has connected, worker process id($from_id) work for it.");
    }

    public function onClose($server, $fd_id, $from_id) {
        $this->console("TCP connection id($fd_id) has closed, worker process id($from_id) work for it.");
    }

    public function onReceive(swoole_server $server, $fd_id, $from_id, $packet) {
        $this->console("TCP connection id($fd_id) send a packet, worker process id($from_id) work for it, packet: $packet");

        $PacketParse = new \Service\Packet\Parse($packet);
        try {
            $response = $PacketParse->dispatcher($server);
        } catch (\Core\Exception $e) {
            return $server->send($fd_id, $this->response( $e->getCode(), $e->getMessage() ) );
        }
        return $server->send( $fd_id, $this->response( 0, $response ) );
    }

    public function onTask($server, $task_id, $from_id, $Task) {
        $this->console("Worker process id($from_id) has start a task, task process id($task_id) work for it, task job: " . get_class($Task) );
        $Task->run();
        return $Task->finish();
    }

    public function onFinish($server, $task_id, $data) {
        $this->console("Task process id($task_id) has finish a task, result: $data" );
    }
}

$server = new Server();