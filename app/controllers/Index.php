<?php defined("BASEPATH") OR exit("No direct script access allowed");

use Astra\Services\AwsS3;

class Index extends MY_Controller {

    /**
     * [__construct description]
     */
    public function __construct() {
        parent::__construct();

        $this->load->helper(["url", "password_helper"]);
        $this->load->model(["translate_model"]);
    }

    public function index() {
        $data = [
            "title" => "Translation tool",
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
            $data["email"] = $_SESSION["l10n_email"];
            if ( ! $p = $this->input->get("p")) {
                $layout["content"] = $this->load->view("/home", $data, TRUE);
            } else {
                list($data["production"], $data["platform"]) = explode("_", $p);
                if ( ! in_array($data["platform"], ["Android", "iOS"])) {
                    $this->load->config("aws");
                    $bucket = $this->config->item("s3")["bucket"]["L10N"];
                    $s3_key = str_replace("_", "/", $p);
                    $data["s3_link"] = AwsS3::get_instance()->get_object($bucket, $s3_key . "/all_lang.json");
                }
                $layout["content"] = $this->load->view("/list", $data, TRUE);
            }
            $this->load->view("layout/layout_l10n", ["layout" => $layout, "data" => $data]);
        }
    }

    public function page($page = 1) {
        $order = $this->input->get_post("order");
        $by = $this->input->get_post("by");
        $order_arr = ["keyword", "en-US", "zh-TW", "ja-JP", "id-ID", "ms-MY"];
        $by_arr = ["ASC", "DESC"];
        if ( ! in_array($order, $order_arr)) $order = "keyword";
        if ( ! in_array($by, $by_arr)) $by = "ASC";
        $orderby_arr = [$order, $by];
        $p = $this->input->get_post("p");
        list($production, $platform) = explode("_", $p);
        if ( ! $per_page = $this->input->get_post("per_page")) {
            $this->config->load("pagination");
            $per_page = $this->config->item("per_page");
        }
        $config["per_page"] = $per_page;
        $config["base_url"] = $this->config->item("base_url") . "/index/page/";
        $limit_start = ($page - 1) * $per_page;
        $key = $this->input->get_post("key");
        $db_data = $this->translate_model->get_translate_by_page($limit_start, $per_page, $platform, $orderby_arr, $key);
        $config["total_rows"] = isset($db_data["rows"]) ? $db_data["rows"] : 0;
        $data = $db_data ? $db_data["data"] : [];

        echo json_encode([
            "data"      => $data,
            "pages"     => (int) ceil($config["total_rows"] / $per_page),
            "rows"      => $config["total_rows"],
            "curr_page" => $page,
        ], JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    public function update() {
        $id = $this->input->post("id");
        $lang_en = $this->input->post("enus");
        $lang_ja = $this->input->post("jajp");
        $lang_zh = $this->input->post("zhtw");
        $lang_id = $this->input->post("idid");
        $lang_ms = $this->input->post("msmy");
        $keyword = $this->input->post("keyword");
        $platform = $this->input->post("platform");
        $db_data = $this->translate_model->get($id);

        $data = [
            "last_editor"  => $_SESSION["l10n_email"],
        ];
        strlen($keyword) && $data["keyword"] = $keyword;
        strlen($lang_en) && $data["`en-US`"] = $lang_en;
        strlen($lang_ja) && $data["`ja-JP`"] = $lang_ja;
        strlen($lang_zh) && $data["`zh-TW`"] = $lang_zh;
        strlen($lang_id) && $data["`id-ID`"] = $lang_id;
        strlen($lang_ms) && $data["`ms-MY`"] = $lang_ms;

        $resp = [];
        if ($this->translate_model->update_translate($id, $data)) {
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
            $this->translate_model->user_last_update($_SESSION["l10n_email"], $arr);
            $resp["status"] = "ok";
        } else {
            $resp["status"] = "fail";
        }
        echo json_encode($resp, TRUE);
    }

    public function add() {
        $lang_en = $this->input->post("enus");
        $lang_ja = $this->input->post("jajp");
        $lang_zh = $this->input->post("zhtw");
        $lang_id = $this->input->post("idid");
        $lang_ms = $this->input->post("msmy");
        $keyword = $this->input->post("keyword");
        $d4str   = $this->input->post("d4str");
        $production = $this->input->post("production");
        $platform = $this->input->post("platform");
        $data = [
            "production" => $production,
            "platform" => $platform,
            "keyword"  => $keyword,
            "default_str" => $d4str,
            "`en-US`"  => $lang_en,
            "`ja-JP`"  => $lang_ja,
            "`zh-TW`"  => $lang_zh,
            "`id-ID`"  => $lang_id,
            "`ms-MY`"  => $lang_ms,
            "last_editor"  => $_SESSION["l10n_email"],
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s"),
        ];
        $resp = [];
        if ($this->translate_model->add_translate($data)) {
            $last_updated_arr = ["platform" => $platform];
            if (strlen($lang_en)) {
                $last_updated_arr["en-US"] = ["" => $lang_en];
            }
            if (strlen($lang_ja)) {
                $last_updated_arr["ja-JP"] = ["" => $lang_ja];
            }
            if (strlen($lang_zh)) {
                $last_updated_arr["zh-TW"] = ["" => $lang_zh];
            }
            if (strlen($lang_id)) {
                $last_updated_arr["id-ID"] = ["" => $lang_id];
            }
            if (strlen($lang_ms)) {
                $last_updated_arr["ms-MY"] = ["" => $lang_ms];
            }
            $arr = ["last_update" => json_encode($last_updated_arr)];
            $this->translate_model->user_last_update($_SESSION["l10n_email"], $arr);
            $resp["status"] = "ok";
        } else {
            $resp["status"] = "fail";
        }
        echo json_encode($resp, TRUE);
    }

    public function get_last_id() {
        echo $this->translate_model->get_last_id();
    }

    public function login() {
        $email = $this->input->post("email");
        $passwd = trim($this->input->post("passwd", FALSE));
        $redir = $this->input->post("redir");
        $db_pwd = $this->translate_model->get_user_pwd($email);

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
        $this->translate_model->update_user_data($upd_data);
        echo json_encode($resp, TRUE);
    }

    public function logout() {
        unset($_SESSION["l10n_email"]);
        $resp = [
            "status" => "ok",
        ];
        echo json_encode($resp, TRUE);
    }

    /**
     * DEPRECATED
    public function trans($platform = "goface") {
        $db_data = $this->translate_model->get_l10n_old($platform);
        foreach ($db_data as $row) {
            $data = [
                "production" => "goface",
                "platform" => "portal",
                "keyword" => $row["keyword"],
                "default_str" => $row["default_str"],
                "`en-US`" => $row["en-US"],
                "`ja-JP`" => $row["ja-JP"],
                "`zh-TW`" => $row["zh-TW"],
                "`id-ID`" => $row["id-ID"],
                "`ms-MY`" => $row["ms-MY"],
                "last_editor" => "system",
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ];
            $this->translate_model->add_translate($data);
        }
    }
     */
}

/* End of file index.php */
/* Location: ./app/controllers/index.php */
