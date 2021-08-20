<?php defined("BASEPATH") OR exit("No direct script access allowed");

use Astra\Services\AwsS3;

class Relation extends Admin_Controller {

    /**
     * [__construct description]
     */
    public function __construct() {
        parent::__construct();

        $this->load->helper("url");
        $this->load->model(["translate_model", "l10n_model"]);
    }

    public function index() {
        $data = [];
        $data["platform_arr"] = $this->translate_model->get_platforms();
        $data["pf_stat"] = $this->translate_model->get_platform_stat();
        $data["production"] = $this->input->get("production");
        $data["email"] = $_SESSION["email"];
        $data["lang_arr"] = $this->translate_model::LANG_ARR;
        $data["user_langs"] = $_SESSION["user_langs"];
        $layout["content"] = $this->load->view("/relation_list", $data, TRUE);
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
            $order_arr = array_merge($this->translate_model::LANG_ARR, ["id", "updated_at"]);
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
        /*
        if ($this->translate_model->update_translate($id, $data, $need_specialchar)) {
            $this->translate_model->user_last_update($_SESSION["email"], $arr);
            $this->translate_model->update_platform($production . "_" . $platform, "update");
            $resp["status"] = "ok";
        } else {
            $resp["status"] = "fail";
        }
        $this->response($resp);
         */
    }

    public function add() {
    }
}

/* End of file relation.php */
/* Location: ./app/controllers/relation.php */
