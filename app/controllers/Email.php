<?php defined("BASEPATH") OR exit("No direct script access allowed");

use Astra\Services\AwsS3;

class Email extends Admin_Controller {

    /**
     * [__construct description]
     */
    public function __construct() {
        parent::__construct();

        $this->load->helper("password_helper");
        $this->load->model(["translate_model", "l10n_model"]);
        $this->_production = "";
        $this->_platform = "";
    }

    public function index() {
        $data = [];
        $data["platform_arr"] = $this->translate_model->get_platforms();
        $data["pf_stat"] = $this->translate_model->get_platform_stat();
        $data["email"] = $_SESSION["email"];
        if ( ! $p = $this->input->get("p")) {
            $layout["content"] = $this->load->view("/home", $data, TRUE);
        } else {
            $item = $this->input->get("item");
            list($data["production"], $data["platform"]) = explode("_", $p);
            $data["lang_arr"] = $this->translate_model::LANG_ARR;
            $data["pf_modified"] = $data["pf_stat"][$p]["modified"];
            $data["user_langs"] = $_SESSION["user_langs"];
            $data["item"] = $item;
            $data["email_contents"] = $this->translate_model->get_email_contents_by_lang($data["production"], $data["platform"], $item, $data["user_langs"]);
            $layout["content"] = $this->load->view("/email_contents", $data, TRUE);
        }
        $this->load->view("layout/layout_l10n", ["layout" => $layout, "data" => $data]);
    }

    public function update() {
        $id = $this->input->post("id");
        $lang = $this->input->post("lang");
        $contents = $this->input->post("contents");
        $prod_plat = $this->input->post("prod_plat");
        list($production, $platform) = explode("_", $prod_plat);
        $db_data = $this->translate_model->get($id);
        $data = [
            $lang => $contents,
        ];
        if ($this->translate_model->update_translate($id, $data, 1)) {
            $last_updated_arr = [
                $lang => [$db_data[$lang] => $contents],
                "platform" => $platform,
            ];
            $arr = ["last_update" => json_encode($last_updated_arr)];
            $this->translate_model->user_last_update($_SESSION["email"], $arr);
            $this->translate_model->update_platform($prod_plat, "update");
            $resp["status"] = "ok";
        } else {
            $resp["status"] = "fail";
        }
        echo json_encode($resp);
    }

    public function preview() {
        $data = [];
        $p = $this->input->get("p");
        $lang = $this->input->get("lang");
        $item = $this->input->get("item");
        list($data["production"], $data["platform"]) = explode("_", $p);

        //???S3 ???????????????
        $this->load->config("aws");
        $s3_config = $this->config->item("s3");
        $filepath = "goface/email/email_template.html";
        $template_url = $s3_config["hostname"]["S3_HOSTNAME"] . "/" . $s3_config["bucket"]["L10N"] . "/" . $filepath;
        $template_html = file_get_contents($template_url);

        //??????template ????????????link ???style
        preg_match('/<!--a style="(.*?)"><\/a-->/i', $template_html, $link_style);

        //??????????????????
        $email_contents = $this->translate_model->get_email_contents_by_lang($data["production"], $data["platform"], $item, $_SESSION["user_langs"]);

        foreach ($email_contents[$lang] as $key => $row) {
            $val = nl2br($row["val"]);
            //????????????link name ?????????
            if (preg_match("/<a\s+\[(.*?)\]>(.*?)<\/a>/i", $val, $link_url)) {
                $url_str = trim($email_contents[$lang][$link_url[1]]["val"]);
                $a_html = "<a href=\"{$url_str}\" style=\"{$link_style[1]}\">{$link_url[2]}</a>";
                $val = preg_replace("/<a\s+\[(.*?)\]>(.*?)<\/a>/i", $a_html, $val);
            }
            //?????????link name ?????????
            if (preg_match("/\[(.*)_LINK_URL\]/i", $val, $just_url)) {
                $url_str = trim($email_contents[$lang][$just_url[1]."_LINK_URL"]["val"]);
                $a_html = "<a href=\"{$url_str}\" style=\"{$link_style[1]}\">{$url_str}</a>";
                $val = preg_replace("/\[(.*?)_LINK_URL\]/i", $a_html, $val);
            }
            $template_html = str_replace("{{COPYRIGHT_TO}}", date("Y"), $template_html);
            $template_html = str_replace("{{{$key}}}", $val, $template_html);
        }
        echo $template_html;
    }
}

/* End of file email.php */
/* Location: ./app/controllers/email.php */
