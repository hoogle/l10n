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
    }

    public function compare($platform = "goface", $use_lang = "en-US") {
        switch ($platform) {
            case "goface":
            default:
                $bucket = "portal.goface.me";
                break;
        }
        $url_org = "https://s3-ap-southeast-1.amazonaws.com/{$bucket}/assets/lang/src/{$use_lang}.json?" . time();
        $url_trg = "https://s3-ap-southeast-1.amazonaws.com/{$bucket}/assets/json/{$use_lang}.json?" . time();
        $json_str = file_get_contents($url_org);
        $arr1 = json_decode($json_str, TRUE);

        echo "Original : {$url_org}<br>Translated : {$url_trg}<br>Different <b>{$use_lang}</b> :<br>";
        ksort($arr1);
        $json_str = file_get_contents($url_trg);
        $arr2 = json_decode($json_str, TRUE);
        echo "<table border=1><tr><td>Keyword</td><td>Original</td><td>Translated</td></tr>";
        foreach ($arr1 as $k => $v) {
            if (isset($arr2[$k]) && $v != $arr2[$k]) {
                echo "<tr><td>{$k}</td><td>{$v}</td><td>{$arr2[$k]}</td></tr>"; 
            }
        }
        echo "</table>";
    }

    public function exportt() {
        $p = $this->input->get("p");
        switch ($p) {
        case "api_error_code" :
            $this->_api_error_code();
            break;
        case "goface_portal" :
            $this->_goface_portal();
            break;
        }
        echo "OK";
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

    public function export() {
        $p = $this->input->get("p");
        list($production, $platform) = explode("_", $p);
        $db_data = $this->l10n_model->get_translate("goface_portal");
        $json_arr = $resp = [];
        foreach ($db_data as $row) {
            $json_arr["en-US"][$row["keyword"]] = $row["en-US"];
            $json_arr["ja-JP"][$row["keyword"]] = $row["ja-JP"];
            $json_arr["zh-TW"][$row["keyword"]] = $row["zh-TW"];
            $json_arr["id-ID"][$row["keyword"]] = $row["id-ID"];
            $json_arr["ms-MY"][$row["keyword"]] = $row["ms-MY"];
        }

        foreach (["en-US", "ja-JP", "zh-TW", "id-ID", "ms-MY"] as $use_lang) {
            $this->load->config("aws");
            $s3_config = $this->config->item("s3");
            $filename = $use_lang . ".json";
            $filepath = "{$production}/{$platform}/" . $filename;
            $json_str = json_encode($json_arr[$use_lang]);
            try
            {
                if ($s3_result = AwsS3::get_instance()->pub_object($s3_config["bucket"]["L10N"], $filepath, $json_str)) {
                    $resp["status"] = "ok";
                }
            }
            catch (Exception $e)
            {
                echo "AWS Error: " . $e->getMessage() . "\n";
                log_message("INFO", "AWS S3 Error: " . $e->getMessage());
                $resp["status"] = "fail";
                break;
            }
        }

        echo json_encode($resp, TRUE);
    }
}

/* End of file Tool.php */
/* Location: ./app/controllers/tool.php */
