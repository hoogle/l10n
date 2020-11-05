<?php defined("BASEPATH") OR exit("No direct script access allowed");

class Index extends MY_Controller {

    /**
     * [__construct description]
     */
    public function __construct() {
        parent::__construct();

        $this->load->helper(["url", "password_helper"]);
        $this->load->model(["l10n_model"]);
        session_start();
    }

    public function index() {
        $data = [
            "title" => "Translation tool",
        ];
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
            $layout["content"] = $this->load->view("/list", $data, TRUE);
            $this->load->view("layout/layout_l10n", ["layout" => $layout, "data" => $data]);
        }
    }

    public function page($page = 1) {
        $platform = $this->input->get_post("platform");
        if ( ! $per_page = $this->input->get_post("per_page")) {
            $this->config->load("pagination");
            $per_page = $this->config->item("per_page");
        }
        $config["per_page"] = $per_page;
        $config["base_url"] = $this->config->item("base_url") . "/index/page/";
        $limit_start = ($page - 1) * $per_page;
        $key = $this->input->get_post("key");
        $db_data = $this->l10n_model->get_l10n_by_platform($limit_start, $per_page, $platform, $key);
        $config["total_rows"] = isset($db_data["rows"]) ? $db_data["rows"] : 0;
        $this->pagination->initialize($config); 
        $data = $db_data ? $db_data["data"] : [];

        echo json_encode([
            "data"      => $data,
            "pages"     => (int) ceil($config["total_rows"] / $per_page),
            "rows"      => $config["total_rows"],
            "curr_page" => $page,
            "links"     => $this->pagination->create_links(),
        ], JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    public function update() {
        $id = $this->input->post("id");
        $lang_en = $this->input->post("enus");
        $lang_ja = $this->input->post("jajp");
        $lang_zh = $this->input->post("zhtw");
        $lang_id = $this->input->post("idid");
        $lang_ms = $this->input->post("msmy");
        $platform = $this->input->post("platform");
        $db_data = $this->l10n_model->get($id);
        $data = [
            "`en-US`"  => $lang_en,
            "`ja-JP`"  => $lang_ja,
            "`zh-TW`"  => $lang_zh,
            "`id-ID`"  => $lang_id,
            "`ms-MY`"  => $lang_ms,
            "edit_by"  => $_SESSION["l10n_email"],
        ];
        $resp = [];
        if ($this->l10n_model->update_translate($id, $data)) {
            $last_updated_arr = ["platform" => $platform];
            if (strcmp($lang_en, $db_data["en-US"])) {
                $last_updated_arr["en-US"] = [$db_data["en-US"] => $lang_en];
            }
            if (strcmp($lang_ja, $db_data["ja-JP"])) {
                $last_updated_arr["ja-JP"] = [$db_data["ja-JP"] => $lang_ja];
            }
            if (strcmp($lang_zh, $db_data["zh-TW"])) {
                $last_updated_arr["zh-TW"] = [$db_data["zh-TW"] => $lang_zh];
            }
            if (strcmp($lang_id, $db_data["id-ID"])) {
                $last_updated_arr["id-ID"] = [$db_data["id-ID"] => $lang_id];
            }
            if (strcmp($lang_ms, $db_data["ms-MY"])) {
                $last_updated_arr["ms-MY"] = [$db_data["ms-MY"] => $lang_ms];
            }
            $arr = ["last_update" => json_encode($last_updated_arr)];
            $this->l10n_model->user_last_update($_SESSION["l10n_email"], $arr);
            $resp["status"] = "ok";
        } else {
            $resp["status"] = "fail";
        }
        echo json_encode($resp, TRUE);
    }

    public function add() {
        $lang_en = $this->input->post("en");
        $lang_ja = $this->input->post("jp");
        $lang_zh = $this->input->post("zh");
        $lang_id = $this->input->post("id");
        $lang_ms = $this->input->post("ms");
        $keyword = $this->input->post("keyword");
        $platform = $this->input->post("platform");
        $data = [
            "platform" => $platform,
            "keyword"  => $keyword,
            "`en-US`"  => $lang_en,
            "`ja-JP`"  => $lang_ja,
            "`zh-TW`"  => $lang_zh,
            "`id-ID`"  => $lang_id,
            "`ms-MY`"  => $lang_ms,
            "edit_by"  => $_SESSION["l10n_email"],
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s"),
        ];
        $resp = [];
        if ($this->l10n_model->add_translate($data)) {
            $last_updated_arr = ["platform" => $platform];
            if (strcmp($lang_en, $db_data["en-US"])) {
                $last_updated_arr["en-US"] = [$db_data["en-US"] => $lang_en];
            }
            if (strcmp($lang_ja, $db_data["ja-JP"])) {
                $last_updated_arr["ja-JP"] = [$db_data["ja-JP"] => $lang_ja];
            }
            if (strcmp($lang_zh, $db_data["zh-TW"])) {
                $last_updated_arr["zh-TW"] = [$db_data["zh-TW"] => $lang_zh];
            }
            if (strcmp($lang_id, $db_data["id-ID"])) {
                $last_updated_arr["id-ID"] = [$db_data["id-ID"] => $lang_id];
            }
            if (strcmp($lang_ms, $db_data["ms-MY"])) {
                $last_updated_arr["ms-MY"] = [$db_data["ms-MY"] => $lang_ms];
            }
            $arr = ["last_update" => json_encode($last_updated_arr)];
            $this->l10n_model->user_last_update($_SESSION["l10n_email"], $arr);
            $resp["status"] = "ok";
        } else {
            $resp["status"] = "fail";
        }
        echo json_encode($resp, TRUE);
    }

    public function login() {
        $email = $this->input->post("email");
        $passwd = trim($this->input->post("passwd", FALSE));
        $redir = $this->input->post("redir");
        $db_pwd = $this->l10n_model->get_user_pwd($email);

        if ( ! password_verify($passwd, $db_pwd)) {
            $resp = [
                "status" => "error",
                "data" => 4210
            ];
        } else {
            $resp = [
                "status" => "ok",
                "data" => $redir ?: "/",
            ];
            $_SESSION["l10n_email"] = $email;
        }
        $upd_data = [
            "email" => $email,
            "last_login_at" => date("Y-m-d H:i:s"),
        ];
        $this->l10n_model->update_user_data($upd_data);
        echo json_encode($resp, TRUE);
    }

    public function logout() {
        unset($_SESSION["l10n_email"]);
        $resp = [
            "status" => "ok",
        ];
        echo json_encode($resp, TRUE);
    }

}

/* End of file index.php */
/* Location: ./app/controllers/index.php */
