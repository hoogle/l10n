<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends MY_Controller {

	/**
	 * [index description]
	 * @return [type] [description]
	 */
	public function index() {
		if ($this->is_login()) {
			$this->cleanSession();
		}
        $resp = [
            "status" => "ok",
        ];
        echo json_encode($resp, TRUE);
	}

}
/* End of file Logout.php */
/* Location: ./app/controllers/Logout.php */
