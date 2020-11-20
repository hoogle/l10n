<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        session_start();
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

    /**
     * [saveSession description]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    protected function saveSession(array $data) {
        if ( ! isset($data['uid']) || ! isset($data['name']) ||
             ! isset($data['email']) || ! isset($data['picture_url'])) {
            return false;
        }
        $this->admin_id = $_SESSION['uid'] = $data['uid'];
        $this->name = $_SESSION['name'] = $data['name'];
        $this->email = $_SESSION['email'] = $data['email'];
        $this->picture_url = $_SESSION['picture_url'] = $data['picture_url'];
        return true;
    }

    /**
     * [cleanSession description]
     * @return [type] [description]
     */
    protected function cleanSession() {
        if (is_array($_SESSION) && count($_SESSION) > 0) {
            foreach ($_SESSION as $key=>$val) {
                unset($_SESSION[$key]);
            }
        }
        $this->admin_id = $this->name = $this->email = $this->picture_url = '';
        return true;
    }

    /**
     * [is_login description]
     * @return boolean [description]
     */
    protected function is_login() {
        return ( ! empty($this->uid)) ? true : false;
    }

}

/* End of file MY_Controller.php */
/* Location: ./app/core/MY_Controller.php */
