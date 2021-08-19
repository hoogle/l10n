<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_Controller extends MY_Controller {

    /**
     * [__construct description]
     */
    public function __construct() {
        parent::__construct();

        if ( ! $this->is_login()) {
            if ($this->input->is_ajax_request()) {
                $response = [
                    'error' => [
                        'code' => 403,
                        'message' => 'No Permission to Access',
                    ],
                    'data' => [],
                ];
                $this->json($response);
            } else {
                header('Location: /');
            }
            exit();
        }
    }

}
/* End of file Admin_Controller.php */
/* Location: ./app/core/Admin_Controller.php */
