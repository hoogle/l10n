<?php defined("BASEPATH") OR exit("No direct script access allowed");

use Astra\Services\AwsS3;

class User extends MY_Controller {

    /**
     * [__construct description]
     */
    public function __construct() {
        parent::__construct();

        $this->load->helper("password_helper");
        $this->load->model(["translate_model", "l10n_model"]);
        $this->_lang_arr = ["en-US", "zh-TW", "ja-JP", "id-ID", "ms-MY"];
    }

    public function index() {
        $data = [
            "title" => "Translation tool",
        ];
        if ( ! isset($_SESSION["email"])) {
            $gclient = new Google_Client();
            $gclient->setAuthConfig(GL_OAUTH2_SECRET);
            $gclient->addScope([Google_Service_Oauth2::USERINFO_EMAIL, Google_Service_Oauth2::USERINFO_PROFILE]);
            $gclient->setRedirectUri($this->config->item("gl_redirect"));
            $data["gl_login_url"] = $gclient->createAuthUrl();
            $redir = $this->input->get("redir");
            $data["redir"] = $redir;
            $layout["content"] = $this->load->view("login", $data, TRUE);
			$this->load->view("layout/layout_l10n_box", ["layout" => $layout]);
        } else {
            $data["platform_arr"] = $this->translate_model->get_platforms();
            $data["pf_stat"] = $this->translate_model->get_platform_stat();
            $data["email"] = $_SESSION["email"];
            $data["user_data"] = $this->l10n_model->get_user_data($data["email"]);
            $data["lang_arr"] = $this->_lang_arr;
            $layout["content"] = $this->load->view("/user_edit", $data, TRUE);
            $this->load->view("layout/layout_l10n", ["layout" => $layout, "data" => $data]);
        }
    }

    public function update() {
        $email = $this->input->post("email");
        $using_lang = $this->input->post("using_lang");
        $resp = [];
        if (empty($email)) {
            $resp["status"] = "fail";
        } elseif ( ! $using_lang) {
            $resp["status"] = "fail";
            $resp["message"] = "At least one language to been select";
        } else {
            $data = [
                "email" => $email,
                "using_lang" => json_encode($using_lang),
            ];
            $this->translate_model->update_user_data($data);
            $_SESSION["user_langs"] = $using_lang;
            $resp["status"] = "ok";
        }
        $this->response($resp);
    }

}

/* End of file user.php */
/* Location: ./app/controllers/user.php */
