<?php
/**
 * 发送邮件 工作
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-09-28
 */
 
namespace Service\Task;

use Service\Task\JobInterface;

class JobSendMail implements JobInterface {

    protected $_Mail;

    protected $_send_result;

    public function __construct($data) {
        $this->_Mail = $data;
    }

    public function run() {
        if ( $this->_Mail->send() ) {
            $this->_Mail->complete();
            $this->_send_result = 'E-Mail send completed.';
        } else {
            $this->_Mail->pending();
            $this->_send_result = 'E-Mail send pending';
        }
    }

    public function finish() {
        return $this->_send_result;
    }
}