<?php defined("BASEPATH") OR exit("No direct script access allowed");

class User extends MY_Controller {

	/**
	 * [__construct description]
	 */
	public function __construct() {
		parent::__construct();

		$this->load->library("form_validation");
		$this->load->model("l10n_model");
        session_start();
    }

	/**
	 * [index description]
	 * @return [type] [description]
	 */
	public function password() {
        if ( ! isset($_SESSION["l10n_email"])) {
            $redir = $this->input->get("redir");
            $data["redir"] = $redir;
            $layout["content"] = $this->load->view("login", $data, TRUE);
			$this->load->view("layout/layout_l10n_box", ["layout" => $layout]);
        } else {
            $platform = $this->input->get("platform");
            $platform_arr = $this->l10n_model->get_platforms();
            if ( ! in_array($platform, $platform_arr)) {
                $platform = $platform_arr[0];
            }
            $data["platform"] = $platform;
            $data["platform_arr"] = $platform_arr;
            $data["title"] = "Change user password";
            $data["email"] = $_SESSION["l10n_email"];
            $this->layout["js_files"] = [
                "/assets/plugins/parsleyjs/parsley.min.js",
                "/assets/plugins/bootstrap-sweetalert/sweet-alert.js",
            ];
		    $this->layout["css_files"] = ["/assets/plugins/bootstrap-sweetalert/sweet-alert.css"];
            $this->layout["content"] = $this->load->view("passwd", $data, true);
            $this->load->view("layout/layout_l10n", ["layout" => $this->layout]);
        }
	}

	/**
	 * [update description]
	 * @return [type] [description]
	 */
	public function update_pwd() {
		$this->form_validation->set_data($_POST);
        $this->form_validation->set_rules("email", "email", ["required"]);
		$this->form_validation->set_rules("passwd", "passwd", ["required", "min_length[5]", "max_length[20]"]);
		if ($this->form_validation->run() == false) {
			return $this->json(["errno" => 400, "errmsg" => "invalid rule parameter"]);
		}

		$vo = [];
		$vo["passwd"] = password_hash($this->input->post("passwd"), PASSWORD_BCRYPT);
        $vo["email"] = $this->input->post("email");
		if ($this->l10n_model->update_user_data($vo)) {
			return $this->json(["errno" => 0, "errmsg" => ""]);
		} else {
			return $this->json(["errno" => 500, "errmsg" => "failed to update data"]);
		}
	}

}
/* End of file User.php */
/* Location: ./app/controllers/User.php */
