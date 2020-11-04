<?php namespace Astra\Services;


use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;


final class AwsS3
{

    private static $instance = NULL;
    private $_client = NULL;
    private $_config = NULL;

    /**
     * [__construct description]
     */
    public function __construct()
    {
        $ci =& get_instance();
        $ci->config->load('aws');
        $this->_config = $ci->config->item('s3');

        if (ENVIRONMENT == 'development') {
            $this->_client = new S3Client([
                'version' => 'latest',
                'region'  => $this->_config['region'],
                'profile' => $this->_config['profile']
            ]);
        } else {
            $this->_client = new S3Client([
                'version'     => 'latest',
                'region'      => $this->_config['region'],
            ]);
        }
    }

    /**
     * [getInstance description]
     * @return [type] [description]
     */
    public static function get_instance()
    {
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
     * [create_presigned_url description]
     * @param  [type] $file_url [description]
     * @return [type]           [description]
     */
    public function create_presigned_url($file_url) {
        try {
            $cmd = $this->_client->getCommand('PutObject', [
                'Bucket' => $this->_config['bucket'],
                'Key'    => $file_url
            ]);
            $request = $this->_client->createPresignedRequest($cmd, '+5 minutes');
            return (string) $request->getUri();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * [pub_object description]
     * @param  [type] $bucket       [description]
     * @param  [type] $keyname      [description]
     * @param  [type] $filepath     [description]
     * @param  [type] $content_type [description]
     * @return [type]               [description]
     */
    public function pub_object($bucket, $keyname, $body = "", $filepath = "", $content_type = "") {
        $config_arr = [
            "Bucket"       => $bucket,
            "Key"          => $keyname,
            "ACL"          => "public-read"
        ];

        if ( ! empty($body)) {
            $config_arr["Body"] = $body;
        } else {
            $config_arr["SourceFile"] = $filepath;
        }
        $config_arr["ContentType"] = $content_type;
        $config_arr["CacheControl"] = "public, max-age=600";

        try {
            $result = $this->_client->putObject($config_arr);
        } catch (S3Exception $e) {
            throw $e;
        }
        return $result;
    }

}
