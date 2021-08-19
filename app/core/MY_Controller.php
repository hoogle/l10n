<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once __DIR__ . "/Admin_Controller.php";

/*
 *         CI_Controller
 *               |
 *         MY_Controller
 *               |
 *        Admin_Controller
 *
 */

class MY_Controller extends CI_Controller {

    protected $uid         = '';
    protected $name        = '';
    protected $email       = '';
    protected $picture_url = '';
    protected $user_langs  = '';

    public function __construct() {
        parent::__construct();

        ini_set('session.cookie_lifetime', 0);
        ini_set('session.gc_maxlifetime', 3600 * 8);

        session_start();

        (isset($_SESSION['uid'])) && $this->uid = $_SESSION['uid'];
        (isset($_SESSION['name'])) && $this->name = $_SESSION['name'];
        (isset($_SESSION['email'])) && $this->email = $_SESSION['email'];
        (isset($_SESSION['picture_url'])) && $this->login_time = $_SESSION['picture_url'];
        (isset($_SESSION['user_langs'])) && $this->user_langs = $_SESSION['user_langs'];

        $this->load->helper('url');
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
     * [response description]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    protected function response($data = array()) {
        if (count($data) == 0) {
            $data = new stdClass();
        }
        $response = [
            'error' => [
                'code' => '0',
                'message' => '',
            ],
            'data' => $data,
        ];
        $this->json($response);
        exit();
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
        $this->user_langs = $_SESSION["user_langs"] = $data["user_langs"];
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
