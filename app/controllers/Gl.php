<?php defined('BASEPATH') OR exit('No direct script access allowed');

use Astra\Services\Logger;

class Gl extends MY_Controller {

    /**
     * [__construct description]
     */
    public function __construct() {
        parent::__construct();

        $this->load->model("l10n_model");
    }

    /**
     * [callback description]
     * @return function [description]
     */
    public function callback() {

        $code = $this->input->get('code');
        $scope = $this->input->get('scope');
        $authuser = $this->input->get('authuser');
        $session_state = $this->input->get('session_state');
        $prompt = $this->input->get('prompt');

        if (empty($code)) {
            Logger::getInstance()->warning("empty code by gl login");
            redirect('/?error=miss_code');
        }

        $gclient = new Google_Client();
        $gclient->setAuthConfig(GL_OAUTH2_SECRET);
        $gclient->addScope([Google_Service_Oauth2::USERINFO_EMAIL, Google_Service_Oauth2::USERINFO_PROFILE]);
        $gclient->setRedirectUri($this->config->item('gl_redirect'));
        if ( ! $gclient->authenticate($code)) {
            Logger::getInstance()->warning("error authenticate by google login");
            redirect('/?error=error_authenticate');
        }
        $token = $gclient->getAccessToken();
        $gclient->setAccessToken($token);

        $oauth = new Google_Service_Oauth2($gclient);
        $profile = $oauth->userinfo->get();

        list($_, $domain) = explode('@', $profile->email);
        if ( ! in_array($domain, ALLOW_EMAIL_DOMAIN)) {
            redirect('/?error=not_support_domain');
        }

        $data = [];
        $data["uid"] = $profile->id;
        $data["email"] = $profile->email;
        $data["name"] = $profile->name;
        $data["picture_url"] = $profile->picture;
        $user_langs_arr = $this->l10n_model->get_user_data($data["email"])["using_lang"];
        $data["user_langs"] = json_decode($user_langs_arr, TRUE);
        if ( ! $this->saveSession($data)) {
            Logger::getInstance()->error("session write error", $data);
            redirect('/?error=session_error');
        }

        $_SESSION["email"] = $profile->email;
        $upd_data = [
            "email" => $profile->email,
            "last_login_at" => date("Y-m-d H:i:s"),
        ];
        $this->l10n_model->update_user_data($upd_data);

        Logger::getInstance()->info("gl account login", $data);

        redirect("/trans");
    }

}

/* End of file Gl.php */
/* Location: ./app/controllers/Gl.php */
