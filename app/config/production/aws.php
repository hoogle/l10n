<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * S3 config
 */
$config['s3']['region']  = 'ap-southeast-1';
$config['s3']['profile'] = '';
$config['s3']['bucket']  = [
    'GENESIS_SOUTHEAST' => 'genesis-ap-southeast-1',
    'PORTAL_ASTRA_CLOUD' => 'portal.astra.cloud',
    'WAREHOUSE_ASTRA_CLOUD' => 'warehouse.astra.cloud',
    'PORTAL_GOFACE_ME' => 'portal.goface.me',
];
$config['s3']['folder'] = [
    'DEVICE_REPORT_FILES' => 'device_report_files',
    'FACE_FOLDER' => 'face'
];
$config['s3']['hostname'] = [
    'S3_HOSTNAME' => 'https://s3-ap-southeast-1.amazonaws.com'
];

/**
 * SQS config
 */
$config['sqs']['region']     = 'ap-southeast-1';
$config['sqs']['profile']    = '';
$config['sqs']['queue_urls'] = [
    'COMMON'  => 'https://sqs.ap-southeast-1.amazonaws.com/478205036267/common',
    'GENESIS' => 'https://sqs.ap-southeast-1.amazonaws.com/478205036267/genesis',
    'CV-SERVICE' => 'https://sqs.ap-southeast-1.amazonaws.com/478205036267/cv_service',
    'CV-SERVICE-HEAVY' => 'https://sqs.ap-southeast-1.amazonaws.com/478205036267/cv_service_heavy'
];
