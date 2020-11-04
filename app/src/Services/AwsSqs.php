<?php namespace Astra\Services;


use Aws\Sqs\SqsClient;


final class AwsSqs
{

    private static $instance = NULL;
    private $_client = NULL;
    private $_config = NULL;

    /**
     * [__construct description]
     */
    public function __construct() {
        $ci =& get_instance();
        $this->_config = $ci->config->item('sqs');

        if (ENVIRONMENT == 'development') {
            $this->_client = new SqsClient([
                'version' => 'latest',
                'region'  => $this->_config['region'],
                'profile' => $this->_config['profile']
            ]);
        } else {
            $this->_client = new SqsClient([
                'version'     => 'latest',
                'region'      => $this->_config['region'],
            ]);
        }
    }

    /**
     * [getInstance description]
     * @return [type] [description]
     */
    public static function get_instance() {
        if (NULL === static::$instance) {
            static::$instance = new self();
        }
        return static::$instance;
    }

    /**
     * [get_client description]
     * @return [type] [description]
     */
    public function get_client() {
        return $this->_client;
    }

    /**
     * [get_config description]
     * @return [type] [description]
     */
    public function get_config() {
        return $this->_config;
    }

    /**
     * [enqueue description]
     * @param  [type] $queue_url [description]
     * @param  Array  $job       [description]
     * @return [type]            [description]
     */
    public function enqueue($queue_url, Array $job) {
        try {
            return $this->_client->sendMessage([
                'MessageBody' => json_encode($job),
                'QueueUrl' => $queue_url
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }

}
