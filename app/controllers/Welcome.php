<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller {

	/**
	 * [index description]
	 * @return [type] [description]
	 */
	public function index() {

		if ($this->is_login()) {
			redirect('/trans');
		}

		$gclient = new Google_Client();
        $gclient->setAuthConfig(GL_OAUTH2_SECRET);
        $gclient->addScope([Google_Service_Oauth2::USERINFO_EMAIL, Google_Service_Oauth2::USERINFO_PROFILE]);
		$gclient->setRedirectUri($this->config->item('gl_redirect'));

		$data = [];
        $data['gl_login_url'] = $gclient->createAuthUrl();
        $redir = $this->input->get("redir");
        $data["redir"] = $redir;
        $this->load->view('login', $data);
	}

}
/* End of file Welcome.php */
/* Location: ./app/controllers/Welcome.php */
