<?php namespace Astra\Services;

use \Monolog\Logger as MonoLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\JsonFormatter;
use Monolog\Processor\UidProcessor;

final class Logger
{
    private static $instance = null;
    private $_logger         = false;

    /**
     * [__construct description]
     */
    public function __construct() {

        $log_level = MonoLogger::DEBUG;
        if (ENVIRONMENT == 'production') {
            $log_level = MonoLogger::INFO;
        }

        try {
            $handler = new StreamHandler(APPPATH . 'logs/monolog/' . date('Y-m-d') .  '.log', $log_level);
            $handler->setFormatter(new JsonFormatter());
            $this->_logger = new MonoLogger('api', [$handler]);
            $this->_logger->pushProcessor(new UidProcessor(32));
        } catch (Exception $e) {
            exit($e->getMessage());
        }
    }

    /**
     * [getInstance description]
     * @return [type] [description]
     */
    public static function getInstance() {
        if (null === static::$instance) {
            static::$instance = new self();
        }
        return static::$instance;
    }

    /**
     * [info description]
     * @param  [type] $msg  [description]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public function info($msg, $data = []) {
        $this->_logger->info($msg, $data);
    }

    /**
     * [debug description]
     * @param  [type] $msg  [description]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public function debug($msg, $data = []) {
        $this->_logger->debug($msg, $data);
    }

    /**
     * [warning description]
     * @param  [type] $msg  [description]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public function warning($msg, $data = []) {
        $this->_logger->warning($msg, $data);
    }

    /**
     * [error description]
     * @param  [type] $msg  [description]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public function error($msg, $data = []) {
        $this->_logger->error($msg, $data);
    }

    /**
     * [critical description]
     * @param  [type] $msg  [description]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public function critical($msg, $data = []) {
        $this->_logger->critical($msg, $data);
    }

}
/* End of file Logger.php */
/* Location: ./app/provider/Services/Logger.php */
