<?php defined("BASEPATH") OR exit("No direct script access allowed");

use Astra\Services\AwsS3;

class Tool extends MY_Controller {

    /**
     * [__construct description]
     */
    public function __construct() {
        parent::__construct();

        $this->load->helper("directory");
        $this->load->model(["l10n_model"]);
        session_start();
    }

    public function compare($project = "warehouse", $use_lang = "en-US") {
        switch ($project) {
            case "goface":
                $bucket = "portal.goface.me";
                break;
            case "warehouse":
                $bucket = "warehouse.astra.cloud";
                break;
            case "portal":
            default:
                $bucket = "portal.astra.cloud";
                break;
        }
        $url_org = "https://s3-ap-southeast-1.amazonaws.com/{$bucket}/assets/lang/src/{$use_lang}.json?" . time();
        $url_trs = "https://s3-ap-southeast-1.amazonaws.com/{$bucket}/assets/json/{$use_lang}.json?" . time();
        $json_str = file_get_contents($url_org);
        $arr1 = json_decode($json_str, TRUE);

        echo "Original : {$url_org}<br>Translated : {$url_trs}<br>Different <b>{$use_lang}</b> :<br>";
        ksort($arr1);
        $json_str = file_get_contents($url_trs);
        $arr2 = json_decode($json_str, TRUE);
        echo "<table border=1><tr><td>Keyword</td><td>Original</td><td>Translated</td></tr>";
        foreach ($arr1 as $k => $v) {
            if (isset($arr2[$k]) && $v != $arr2[$k]) {
                echo "<tr><td>{$k}</td><td>{$v}</td><td>{$arr2[$k]}</td></tr>"; 
            }
        }
        echo "</table>";
    }

    /**
     * [index description]
     * @return [type] [description]
     */
    public function parsing($js_php = "php", $use_lang = "en-US") {
        if ($js_php == "php") {
            $view_path = APPPATH . "language/" . $use_lang;
            $dir_tree = directory_map($view_path);
            foreach ($dir_tree as $dir) {
                echo $view_path . "/" .  $dir . "<pre>";
                $contents = file_get_contents($view_path . "/" . $dir);
                $contents = str_replace("<?php\n", "", $contents);
                $contents = str_replace("?>", "", $contents);
                $contents = str_replace("\\\"", "\"", $contents);
                $lang_arr = explode("\n", $contents);
                foreach ($lang_arr as $line) {
                    $pattern = "/lang\[\"(.*?)\"\] = \"(.*?)\";$/";
                    preg_match_all($pattern, $line, $matches);
                    if ( ! empty($matches[0][0])) {
                        echo "key: {$matches[1][0]}\tval: {$matches[2][0]}\n";
                        $trans_data = [
                            "platform" => "genesis_msp_php",
                            "view_path" => str_replace(".php", "", $dir),
                            "keyword" => $matches[1][0],
                            "`{$use_lang}`" => $matches[2][0],
                            "edit_by" => "system",
                        ];

                        $this->l10n_model->update($trans_data);
                    }
                }
            }
        } else {
            $view_path = APPPATH . "assets/lang/src";
            echo $view_path . "<pre>";
            $contents = file_get_contents($view_path . "/" . $use_lang . ".json");
            $lang_arr = json_decode($contents, TRUE);
            foreach ($lang_arr as $key => $val) {
                $trans_data = [
                    "platform" => "genesis_msp_js",
                    "view_path" => "",
                    "keyword" => $key,
                    "`{$use_lang}`" => $val,
                    "edit_by" => "system",
                ];

                $this->l10n_model->update($trans_data);
            }
        }
    }

    public function import_from_db($table = "error_map") {
        if (empty($table) || ! in_array($table, ["error_map", "template"])) {
            echo "Must be 'error_map' or 'template' table name";
            exit;
        }

        $db_data = $this->l10n_model->get_template_error_data($table);
        if ($table == "error_map") {
            foreach ($db_data as $row) {
                $trans_data = [
                    "platform" => "api_error_code",
                    "keyword" => $row["keyword"],
                    "default_str" => $row["default_msg"],
                    "`en-US`" => $row["us"],
                    "`ja-JP`" => $row["jp"],
                    "edit_by" => "system",
                ];

                $this->l10n_model->update($trans_data);
            }
        } else {
            foreach ($db_data as $row) {
                $platform_str = $row["tpl_type"] == "push" ? "notification" : "email";
                $trans_data = [
                    "platform" => $platform_str . "_template",
                    "keyword" => $row["action"],
                    "`en-US`" => $row["en"],
                    "`ja-JP`" => $row["ja"],
                    "edit_by" => "system",
                ];

                $this->l10n_model->update($trans_data);
            }
        }
    }

    /**
     * https://l10n.astra.cloud/tool/import_from_web/portal/en-US
     * https://l10n.astra.cloud/tool/import_from_web/warehouse/en-US
     */

    public function import_from_web($project = "portal", $use_lang = "en-US") {
        switch ($project) {
            case "goface":
                $bucket = "portal.goface.me";
                $platform = "goface";
                break;
            case "warehouse":
                $bucket = "warehouse.astra.cloud";
                $platform = "wh_msp";
                break;
            case "portal":
            default:
                $bucket = "portal.astra.cloud";
                $platform = "wh_msp";
                break;
        }
        $url = "https://s3-ap-southeast-1.amazonaws.com/{$bucket}/assets/lang/src/{$use_lang}.json?" . time();
        $json_str = file_get_contents($url);
        $arr = json_decode($json_str, TRUE);
        foreach ($arr as $key => $val) {
            $trans_data = [
                "platform" => $platform,
                "keyword" => $key,
                "`{$use_lang}`" => $val,
                "edit_by" => "system",
            ];
            $this->l10n_model->update($trans_data, $use_lang);
            $this->l10n_model->update_autoincrement();
        }
    }

    public function export() {
		if ( ! isset($_SESSION["l10n_email"])) {
            $redir = $this->input->get("redir");
            $data["redir"] = $redir;
            $layout["content"] = $this->load->view("login", $data, TRUE);
			$this->load->view("layout/layout_l10n_box", ["layout" => $layout]);
        } else {
            $platform = $this->input->get("platform");
            switch ($platform) {
                case "api_error_code" :
                    $this->_api_error_code();
                    break;
                case "genesis_msp_js" :
                    $this->_genesis_msp("js");
                    break;
                case "genesis_msp_php" :
                    $this->_genesis_msp("php");
                    break;
                case "genesis_portal" :
                    $this->_genesis_portal();
                    break;
                case "wh_msp" :
                    $this->_wh_msp();
                    break;
                case "goface" :
                    $this->_goface();
                    break;
            }
            echo "OK";
        }
    }

    private function _api_error_code() {
        $trans = $this->l10n_model->get_translate("api_error_code");
        foreach ($trans as $row) {
            $code_arr[$row["keyword"]]["en-US"] = $row["en-US"];
            $code_arr[$row["keyword"]]["ja-JP"] = $row["ja-JP"];
            $code_arr[$row["keyword"]]["zh-TW"] = $row["zh-TW"];
            $code_arr[$row["keyword"]]["id-ID"] = $row["id-ID"];
            $code_arr[$row["keyword"]]["ms-MY"] = $row["ms-MY"];
            $code_arr[$row["keyword"]]["default"] = $row["en-US"];
        }
        $json_str = json_encode($code_arr, TRUE);

        try
        {
            $s3_config = $this->config->item("s3");
            $filepath = "app/error_code/";
            $filename = "code.json";
            $s3_result = AwsS3::get_instance()->pub_object($s3_config["bucket"]["GENESIS_SOUTHEAST"], $filepath . $filename, $json_str);
            $md5 = str_replace("\"", "", $s3_result["ETag"]);
            $filename = "code.md5";
            $s3_result = AwsS3::get_instance()->pub_object($s3_config["bucket"]["GENESIS_SOUTHEAST"], $filepath . $filename, $md5);
        }
        catch (Exception $e)
        {
            echo "AWS Error: " . $e->getMessage() . "\n";
            log_message("INFO", "AWS S3 Error: " . $e->getMessage());
            return false;
        }
    }

    private function _genesis_portal() {
        $db_data = $this->l10n_model->get_translate("genesis_portal");
        $json_arr = [];
        foreach ($db_data as $row) {
            $json_arr["en-US"][$row["keyword"]] = $row["en-US"];
            $json_arr["ja-JP"][$row["keyword"]] = $row["ja-JP"];
        }

        foreach (["en-US", "ja-JP"] as $use_lang) {
            $json_str = json_encode($json_arr[$use_lang]);

            try
            {
                $s3_config = $this->config->item("s3");
                $filename = $use_lang . ".json";
                $filepath = "assets/json/" . $filename;
                $s3_result = AwsS3::get_instance()->pub_object($s3_config["bucket"]["PORTAL_ASTRA_CLOUD"], $filepath, $json_str);
            }
            catch (Exception $e)
            {
                echo "AWS Error: " . $e->getMessage() . "\n";
                log_message("INFO", "AWS S3 Error: " . $e->getMessage());
                return false;
            }
        }

    }

    private function _goface() {
        $db_data = $this->l10n_model->get_translate("goface");
        $json_arr = [];
        foreach ($db_data as $row) {
            $json_arr["en-US"][$row["keyword"]] = $row["en-US"];
            $json_arr["ja-JP"][$row["keyword"]] = $row["ja-JP"];
            $json_arr["zh-TW"][$row["keyword"]] = $row["zh-TW"];
            $json_arr["id-ID"][$row["keyword"]] = $row["id-ID"];
            $json_arr["ms-MY"][$row["keyword"]] = $row["ms-MY"];
        }

        foreach (["en-US", "ja-JP", "zh-TW", "id-ID", "ms-MY"] as $use_lang) {
            $json_str = json_encode($json_arr[$use_lang]);

            try
            {
                $s3_config = $this->config->item("s3");
                $filename = $use_lang . ".json";
                $filepath = "assets/json/" . $filename;
                $s3_result = AwsS3::get_instance()->pub_object($s3_config["bucket"]["PORTAL_GOFACE_ME"], $filepath, $json_str);
            }
            catch (Exception $e)
            {
                echo "AWS Error: " . $e->getMessage() . "\n";
                log_message("INFO", "AWS S3 Error: " . $e->getMessage());
                return false;
            }
        }

    }

    private function _wh_msp() {
        $db_data = $this->l10n_model->get_translate("wh_msp");
        $json_arr = [];
        foreach ($db_data as $row) {
            $json_arr["en-US"][$row["keyword"]] = $row["en-US"];
            $json_arr["ja-JP"][$row["keyword"]] = $row["ja-JP"];
        }

        foreach (["en-US", "ja-JP"] as $use_lang) {
            $json_str = json_encode($json_arr[$use_lang]);

            try
            {
                $s3_config = $this->config->item("s3");
                $filename = $use_lang . ".json";
                $filepath = "assets/json/" . $filename;
                $s3_result = AwsS3::get_instance()->pub_object($s3_config["bucket"]["WAREHOUSE_ASTRA_CLOUD"], $filepath, $json_str);
            }
            catch (Exception $e)
            {
                echo "AWS Error: " . $e->getMessage() . "\n";
                log_message("INFO", "AWS S3 Error: " . $e->getMessage());
                return false;
            }
        }

    }

    private function _upload_to_s3($filename, $body) {
        try
        {
            $s3_config = $this->config->item("s3");
            $filepath = "msp/lang/" . $filename;
            $s3_result = AwsS3::get_instance()->pub_object($s3_config["bucket"]["GENESIS_SOUTHEAST"], $filepath, $body);
        }
        catch (Exception $e)
        {
            echo "AWS Error: " . $e->getMessage() . "\n";
            log_message("INFO", "AWS S3 Error: " . $e->getMessage());
            return false;
        }
    }

    private function _genesis_msp($type = "php") {
        $db_data = $this->l10n_model->get_translate("genesis_msp_" . strtolower($type));
        $arr = array_column($db_data, "view_path");

        if ($type == "php") {
            $view_path = "";
            foreach ($db_data as $row) {
                if ($view_path != $row["view_path"]) {
                    if ($view_path != "") {
                        $content_ja.= "?>";
                        $this->_upload_to_s3("php/ja-JP/" . $view_path . ".php", $content_ja);
                        echo $view_path . "<br>";
                        $content_en.= "?>";
                        $this->_upload_to_s3("php/en-US/" . $view_path . ".php", $content_en);
                        echo $view_path . "<br>";
                    }
                    $view_path = $row["view_path"];
                    $content_ja = "<?php\n";
                    $content_en = "<?php\n";
                }
                $trans_str_jp = str_replace('"', '\"', $row["ja-JP"]);
                $trans_str_en = str_replace('"', '\"', $row["en-US"]);
                $content_ja.= "\$lang[\"{$row["keyword"]}\"] = \"{$trans_str_jp}\";\n";
                $content_en.= "\$lang[\"{$row["keyword"]}\"] = \"{$trans_str_en}\";\n";
            }
            $content_ja.= "?>";
            $this->_upload_to_s3("php/ja-JP/" . $view_path . ".php", $content_ja);
            echo $view_path . "<br>";
            $content_en.= "?>";
            $this->_upload_to_s3("php/en-US/" . $view_path . ".php", $content_en);
            echo $view_path . "<br>";
        } else {
            //https://s3-ap-southeast-1.amazonaws.com/genesis-ap-southeast-1/msp/lang/en-US.json
            //https://s3-ap-southeast-1.amazonaws.com/genesis-ap-southeast-1/msp/lang/ja-JP.json
            
            $json_arr = [];
            foreach ($db_data as $row) {
                $json_arr["en-US"][$row["keyword"]] = $row["en-US"];
                $json_arr["ja-JP"][$row["keyword"]] = $row["ja-JP"];
            }

            foreach (["en-US", "ja-JP"] as $use_lang) {
                $json_str = json_encode($json_arr[$use_lang]);
                $this->_upload_to_s3("js/" . $use_lang . ".json", $json_str);
            }
        }
    }

}

/* End of file Tool.php */
/* Location: ./app/controllers/tool.php */
