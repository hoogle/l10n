<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * S3 config
 */
$config['s3']['region']  = 'ap-southeast-1';
$config['s3']['profile'] = '';
$config['s3']['bucket']  = [
    'L10N' => 'l10n-ap-southeast-1',
];
$config['s3']['hostname'] = [
    'S3_HOSTNAME' => 'https://s3-ap-southeast-1.amazonaws.com'
];

