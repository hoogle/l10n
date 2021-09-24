<?php defined("BASEPATH") OR exit("No direct script access allowed");

use Astra\Services\AwsS3;

class Trans extends Admin_Controller {

    /**
     * [__construct description]
     */
    public function __construct() {
        parent::__construct();

        $this->load->helper("url");
        $this->load->helper("password_helper");
        $this->load->model(["translate_model", "l10n_model"]);
    }

    public function index() {
        $data = [];
        $data["platform_arr"] = $this->translate_model->get_platforms();
        $data["pf_stat"] = $this->translate_model->get_platform_stat();
        $data["email"] = $_SESSION["email"];
        if ( ! $p = $this->input->get("p")) {
            $layout["content"] = $this->load->view("/home", $data, TRUE);
        } else {
            list($data["production"], $data["platform"]) = explode("_", $p);
            $data["p"] = $p;
            $data["lang_arr"] = $this->translate_model::LANG_ARR;
            $data["user_langs"] = $_SESSION["user_langs"];
            if ( ! in_array($data["platform"], ["Android", "iOS"])) {
                $this->load->config("aws");
                $bucket = $this->config->item("s3")["bucket"]["L10N"];
                $s3_key = str_replace("_", "/", $p);
                $data["s3_link"] = AwsS3::get_instance()->get_object($bucket, $s3_key . "/all_lang.json");
            }
            $data["pf_modified"] = $data["pf_stat"][$p]["modified"];
            if ($data["platform"] == "email") {
                $data["email_data"] = $this->translate_model->get_all_email_info($data["production"], $data["platform"]);
                $layout["content"] = $this->load->view("/email_list", $data, TRUE);
            } else {
                $layout["content"] = $this->load->view("/list", $data, TRUE);
            }
        }
        $this->load->view("layout/layout_l10n", ["layout" => $layout, "data" => $data]);
    }

    public function page($page = 1) {
        $id = $this->input->get_post("id");
        if ($id) {
            $db_data = $this->translate_model->get_translate_by_id($id);
            $total_rows = 1;
            $total_pages = 1;
        } else {
            $order = $this->input->get_post("order");
            $by = $this->input->get_post("by");
            $order_arr = array_merge($this->translate_model::LANG_ARR, ["id", "updated_at", "ui_key"]);
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
            $config["base_url"] = $this->config->item("base_url") . "/trans/page/";
            $limit_start = ($page - 1) * $per_page;
            $key = $this->input->get_post("key");
            $db_data = $this->translate_model->get_translate_by_page($limit_start, $per_page, $production, $platform, $orderby_arr, $key);
            $config["total_rows"] = isset($db_data["rows"]) ? $db_data["rows"] : 0;
            $total_pages = (int)ceil($config["total_rows"] / $per_page);
            $total_rows = $config["total_rows"];
        }
        $data = $db_data ? $db_data["data"] : [];

        echo json_encode([
            "data"      => $data,
            "pages"     => $total_pages,
            "rows"      => $total_rows,
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
        $ui_key = $this->input->post("uikey");
        $keyword = $this->input->post("keyword");
        $production = $this->input->post("production");
        $platform = $this->input->post("platform");
        $db_data = $this->translate_model->get($id);

        $data = [
            "last_editor"  => $_SESSION["email"],
        ];
        strlen($ui_key) && $data["ui_key"] = $ui_key;
        strlen($keyword) && $data["keyword"] = $keyword;
        strlen($lang_en) && $data["`en-US`"] = $lang_en;
        strlen($lang_ja) && $data["`ja-JP`"] = $lang_ja;
        strlen($lang_zh) && $data["`zh-TW`"] = $lang_zh;
        strlen($lang_id) && $data["`id-ID`"] = $lang_id;
        strlen($lang_ms) && $data["`ms-MY`"] = $lang_ms;

        $need_specialchar = in_array($platform, ["portal"]) ? 1 : 0;

        $resp = [];
        if ($this->translate_model->update_translate($id, $data, $need_specialchar)) {
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
            $this->translate_model->user_last_update($_SESSION["email"], $arr);
            $this->translate_model->update_platform($production . "_" . $platform, "update", "");
            $resp["status"] = "ok";
        } else {
            $resp["status"] = "fail";
        }
        //$this->response($resp);
        echo json_encode($resp);
    }

    public function add() {
        $lang_en = $this->input->post("enus");
        $lang_ja = $this->input->post("jajp");
        $lang_zh = $this->input->post("zhtw");
        $lang_id = $this->input->post("idid");
        $lang_ms = $this->input->post("msmy");
        $keyword = $this->input->post("keyword");
        $ui_key  = $this->input->post("uikey");
        $production = $this->input->post("production");
        $platform = $this->input->post("platform");
        $data = [
            "production" => $production,
            "platform" => $platform,
            "keyword"  => $keyword,
            "`en-US`"  => $lang_en,
            "`ja-JP`"  => $lang_ja,
            "`zh-TW`"  => $lang_zh,
            "`id-ID`"  => $lang_id,
            "`ms-MY`"  => $lang_ms,
            "last_editor"  => $_SESSION["email"],
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s"),
        ];
        strlen($ui_key) && $data["ui_key"] = $ui_key;
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
            if (strlen($ui_key)) {
                $last_updated_arr["ui_key"] = ["" => $ui_key];
            }
            $arr = ["last_update" => json_encode($last_updated_arr)];
            $this->translate_model->user_last_update($_SESSION["email"], $arr);
            $this->translate_model->update_platform($production . "_" . $platform, "update", "");
            $resp["status"] = "ok";
        } else {
            $resp["status"] = "fail";
        }
        //$this->response($resp);
        echo json_encode($resp);
    }

    public function get_last_id() {
        echo $this->translate_model->get_last_id();
    }
}

/* End of file trans.php */
/* Location: ./app/controllers/trans.php */
