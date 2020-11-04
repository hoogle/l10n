<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * [json description]
     * @param  array   $data [description]
     * @param  integer $code [description]
     * @return [type]        [description]
     */
    protected function json($data = [], $code = 200) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($code);
        echo json_encode($data);
    }
}

/* End of file MY_Controller.php */
/* Location: ./app/core/MY_Controller.php */
