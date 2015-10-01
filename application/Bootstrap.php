<?php
/**
 * Bootstrap类中, 以_init开头的方法, 都会按顺序执行
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2014-04-15
 * @see    http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 */
class Bootstrap extends Yaf_Bootstrap_Abstract{

    /**
     * Initialize const.
     * @param  \Yaf\Dispatcher $dispatcher
     * @return void
     */
    public function _initConst( Yaf_Dispatcher $dispatcher ) {
        define('APPLICATION_VIEWS_PATH',    APPLICATION_PATH . '/views');
        define('APPLICATION_CONFIG_PATH',   APPLICATION_PATH . '/config');
        define('APPLICATION_CORES_PATH',    APPLICATION_PATH . '/cores');
        define('APPLICATION_SERVICES_PATH', APPLICATION_PATH . '/services');
        define('APPLICATION_LIBRARY_PATH',  APPLICATION_PATH . '/library');
        define('APPLICATION_MODULES_PATH',  APPLICATION_PATH . '/modules');
        define('APPLICATION_ENVIRON_LOCAL',   YAF_ENVIRON === 'local');
        define('APPLICATION_ENVIRON_TEST',    YAF_ENVIRON === 'test');
        define('APPLICATION_ENVIRON_PRODUCT', YAF_ENVIRON === 'product');
    }

    /**
     * Initialize autoload library, like Core_Controller, Http_Request_Curl.
     * @param  \Yaf\Dispatcher $dispatcher
     * @return void
     */
    public function _initAutoload( Yaf_Dispatcher $dispatcher) {
        Yaf_Loader::getInstance()->import(APPLICATION_CORES_PATH . '/ClassLoader.php');
        $autoload = new \Core\ClassLoader();
        $autoload->addClassMap(array(
            'Service' => APPLICATION_SERVICES_PATH,
            'Core'    => APPLICATION_CORES_PATH,
        ));
        spl_autoload_register(array($autoload, 'loader'));
        $dispatcher->autoload = $autoload;
    }

    /**
     * Initialize custom exception handler.
     * @param  \Yaf\Dispatcher $dispatcher
     * @return void
     */
    public function _initException( Yaf_Dispatcher $dispatcher ) {
        // 抛出异常，不使用\Yaf\ErrorController接收，通过\Core\ExceptionHandler处理
        $dispatcher->throwException(true);
        $dispatcher->catchException(false);
        new \Core\ExceptionHandler();
    }

    /**
     * Initialize library.
     * @param  \Yaf\Dispatcher $dispatcher
     * @return void
     */
    public function _initLibrary( Yaf_Dispatcher $dispatcher ) {
        // Yaf_Loader::getInstance()->registerLocalNameSpace(array('Util', 'Ip'));
    }

    /**
     * Initialize view.
     * @param  \Yaf\Dispatcher $dispatcher
     * @return void
     */
    public function _initView( Yaf_Dispatcher $dispatcher ) {
        $dispatcher->disableView();
    }
}
