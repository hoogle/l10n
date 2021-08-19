<?php defined("BASEPATH") OR exit("No direct script access allowed");

use Astra\Services\AwsS3;

class Email extends MY_Controller {

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
            "title" => "Astra Translation tool",
        ];
        if ( ! isset($_SESSION["l10n_email"])) {
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
            $data["email"] = $_SESSION["l10n_email"];
            if ( ! $p = $this->input->get("p")) {
                $layout["content"] = $this->load->view("/home", $data, TRUE);
            } else {
                $item = $this->input->get("item");
                list($data["production"], $data["platform"]) = explode("_", $p);
                $data["lang_arr"] = $this->_lang_arr;
                $data["user_data"] = $this->l10n_model->get_user_data($data["email"]);
                $data["pf_modified"] = $data["pf_stat"][$p]["modified"];
                $data["email_contents"] = $this->translate_model->get_email_contents($data["production"], $data["platform"], $item);
                $layout["content"] = $this->load->view("/email_contents", $data, TRUE);
            }
            $this->load->view("layout/layout_l10n", ["layout" => $layout, "data" => $data]);
        }
    }
}

/* End of file email.php */
/* Location: ./app/controllers/email.php */
