<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * S3 config
 */
$config['s3']['region']  = 'ap-southeast-1';
$config['s3']['profile'] = 'genesis-dev';
$config['s3']['bucket']  = [
    'L10N' => 'l10n-ap-southeast-1',
];
$config['s3']['hostname'] = [
    'S3_HOSTNAME' => 'https://s3-ap-southeast-1.amazonaws.com'
];


/**
 * SQS config
 */
$config['sqs']['region']     = 'ap-southeast-1';
$config['sqs']['profile']    = 'genesis-dev';
$config['sqs']['queue_urls'] = [
    'COMMON'  => 'https://sqs.ap-southeast-1.amazonaws.com/478205036267/common',
    'GENESIS' => 'https://sqs.ap-southeast-1.amazonaws.com/478205036267/genesis',
    'CV-SERVICE' => 'https://sqs.ap-southeast-1.amazonaws.com/478205036267/cv_service',
    'CV-SERVICE-HEAVY' => 'https://sqs.ap-southeast-1.amazonaws.com/478205036267/cv_service_heavy'
];
