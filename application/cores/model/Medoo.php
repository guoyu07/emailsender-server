<?php
/**
 * MySQL Model
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2014-11-01
 */

namespace Core\Model;

use Core\Exception\DatabaseWriteException;

class Medoo {

    public static $Medoo = null;

    protected $_pk = 'id';

    protected $_table = "";

    protected function _create() {
        $config = (new \Yaf_Config_Ini( APPLICATION_CONFIG_PATH . '/database.ini', YAF_ENVIRON));

        return new \Medoo(array(
            'database_type' => $config->database_type,
            'database_name' => $config->database_name,
            'server'        => $config->server,
            'username'      => $config->username,
            'password'      => $config->password,
            'port'          => $config->port,
            'charset'       => $config->charset,
        ));
    }

    public function medoo($singleton=true) {
        if ( ! $singleton ) 
            return $this->_create();

        if ( ! is_null( self::$Medoo ) ) {
            return self::$Medoo;
        }
        self::$Medoo = $this->_create();
        return self::$Medoo;
    }
    
    public function fetchRowByPk($pk) {
        $rows = $this->medoo()->select($this->_table, '*', array($this->_pk => $pk));
        return isset($rows[0]) ? $rows[0] : array();
    }

    public function fetchRowsByCondition($condition) {
        return $this->medoo()->select($this->_table, '*', $condition);
    }

    public function fetchCountByCondition($condition) {
        return $this->medoo()->count($this->_table, $condition);
    }

    public function updateRowByPk($pk, $row) {
        if ( ! $affect = $this->medoo()->update($this->_table, $row, array($this->_pk => $pk)) ) {
            throw new DatabaseWriteException();
        }
        return $affect;
    }

    public function insertRow($row) {
        if ( $last_id = $this->medoo()->insert($this->_table, $row) ) 
            return $last_id;

        $error = $this->medoo()->error();
        if ( ! is_null( $error[1] ) )
            throw new DatabaseWriteException();
    }
}