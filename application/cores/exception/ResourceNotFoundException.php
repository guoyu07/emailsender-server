<?php
/**
 * 核心异常类 请求参数异常
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2014-10-23
 */
namespace Core\Exception;

class ResourceNotFoundException extends \Core\Exception {

    protected $code    = 930;

    protected $message = "资源未找到";

}