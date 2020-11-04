<?php defined('BASEPATH') OR exit('No direct script access allowed');

function genPassword($length = 6) {
    $pool = '23456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ~!@#$%^&*_-+=`|\(){}[]:;"';
    return substr(str_shuffle(str_repeat($pool, ceil($length / strlen($pool)))), 0, $length);
}

/* End of file password_helper.php */
/* Location: ./app/helpers/password_helper.php */
