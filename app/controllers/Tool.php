<?php defined("BASEPATH") OR exit("No direct script access allowed");

use Astra\Services\AwsS3;

class Tool extends MY_Controller {

    /**
     * [__construct description]
     */
    public function __construct() {
        parent::__construct();

        $this->load->helper("directory");
        $this->load->model(["l10n_model", "translate_model"]);
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

    public function parse_xml($pf, $use_lang) {
        if ( ! in_array($pf, ["Android", "iOS"])) {
            echo "'pf' have to be Android or iOS";
            exit;
        }

        if ($pf == "Android") {
            $string_file = "strings.xml";
        } else {
            $string_file = "string.txt";
        }

        $string_file = APPPATH . "import/goface_{$pf}/{$use_lang}/{$string_file}";
        if ( ! file_exists($string_file)) {
            echo "goface {$pf} {$use_lang} string file not exists!";
            exit;
        }
        $str_arr = explode("\n", file_get_contents($string_file));
        $arr = [];
        foreach ($str_arr as $line) {
            $matches = [];
            if ($pf == "Android") {
                if (strstr($line, "<string name=\"")) {
                    preg_match("/^<string name=\"(.*?)\">(.*)<\/string>$/", trim($line), $matches);
                }
            } else {
                if (strstr($line, "\" = \"")) {
                    preg_match("/^\"(.*)\" = \"(.*)\";$/", $line, $matches);
                }
            }

            if ($matches) {
                $trans_data = [
                    "production" => "goface",
                    "platform" => $pf,
                    "keyword" => $matches[1],
                    "`{$use_lang}`" => $matches[2],
                    "last_editor" => "system",
                ];
                $arr[$matches[1]] = $trans_data;
            }
        }
        ksort($arr);
        foreach ($arr as $ary) {
            $data = [
                "production" => $ary["production"],
                "platform" => $ary["platform"],
                "keyword" => $ary["keyword"],
            ];
            if ( ! $this->translate_model->get_pfk_exists($data)) {
                $this->translate_model->add_translate($ary);
            } else {
                $this->translate_model->update($ary);
            }
        }
        echo "<pre>";print_r($arr);exit;
    }

    public function download($p) {
        list($production, $platform) = explode("_", $p);
        if ( ! $db_data = $this->l10n_model->get_translate($p)) {
            echo "There is no any {$production} {$platform} translation string yet.";
            exit;
        }
        $json_arr = $resp = [];
        foreach ($db_data as $row) {
            $json_arr["en-US"][$row["keyword"]] = $row["en-US"];
            $json_arr["ja-JP"][$row["keyword"]] = $row["ja-JP"];
            $json_arr["zh-TW"][$row["keyword"]] = $row["zh-TW"];
            $json_arr["id-ID"][$row["keyword"]] = $row["id-ID"];
            $json_arr["ms-MY"][$row["keyword"]] = $row["ms-MY"];
        }

        //Android
        if ($platform == "Android") {
            $this->_goface_android($json_arr, $production, $platform);
            echo "Done!";
        } else {
            //iOS
            $this->_goface_ios($json_arr, $production, $platform);
            echo "Done!";
        }
        $this->_remove_folder(APPPATH . "tmp");
    }

    private function _goface_ios($json_arr, $production, $platform) {
        $path = APPPATH . "tmp/{$production}_{$platform}";
        if ( ! is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $header_str = "/*\n  Localizable.strings\n  GoFace\n\n  Created by Shrimp Hsieh on 2020/1/7.\n  Copyright © 2020 Shrimp Hsieh. All rights reserved.\n*/\n\n";
        $lang_arr = array_keys($json_arr);
        $filename = [];
        foreach ($lang_arr as $curr_lang) {
            if ($curr_lang == "en-US") continue;
            switch ($curr_lang) {
                case "zh-TW":
                    $filename["zh-TW"] = "Chinese.txt";
                    break;
                case "ja-JP":
                    $filename["ja-JP"] = "Japanese.txt";
                    break;
                case "id-ID":
                    $filename["id-ID"] = "Indonesian.txt";
                    break;
                case "ms-MY":
                    $filename["ms-MY"] = "Malaysia.txt";
                    break;
            }

            //prepare
            $body_str = "";
            foreach ($json_arr[$curr_lang] as $key => $val) {
                $body_str.= "\"{$key}\" = \"{$val}\";\n";
            }
            $contents = $header_str . $body_str . "\n";

            //write
            $fp = fopen($path . "/" . $filename[$curr_lang], "w");
            fwrite($fp, $contents);
            fclose($fp);
        }

        //zip
        $zip = new ZipArchive();
        $zipfile = $path . "/{$platform}_strings.zip";
        if ($zip->open($zipfile, ZipArchive::CREATE) === TRUE) {
            foreach ($lang_arr as $lang) {
                if ($lang != "en-US") {
                    $zip->addFile($path . "/" . $filename[$lang], $filename[$lang]);
                }
            }
        }
        $zip->close();

        //Download file
        $this->_download_file($zipfile);
    }

    private function _goface_android($json_arr, $production, $platform) {
        $header_str = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<resources>\n";
        $end_str = "</resources>\n";
        $lang_arr = array_keys($json_arr);
        $path = APPPATH . "tmp/{$production}_{$platform}";

        $lang_folder = [];
        foreach ($lang_arr as $curr_lang) {
            switch ($curr_lang) {
                case "zh-TW":
                    $lang_folder["zh-TW"] = "values-zh-rTW";
                    break;
                case "ja-JP":
                    $lang_folder["ja-JP"] = "values-ja";
                    break;
                case "id-ID":
                    $lang_folder["id-ID"] = "values-in";
                    break;
                case "ms-MY":
                    $lang_folder["ms-MY"] = "values-ms";
                    break;
                case "en-US":
                    $lang_folder["en-US"] = "values";
                    break;
            }

            //mkdir
            $filepath = $path . "/" . $lang_folder[$curr_lang];
            if ( ! is_dir($filepath)) {
                mkdir($filepath, 0777, true);
            }

            //prepare
            $body_str = "";
            foreach ($json_arr[$curr_lang] as $key => $val) {
                if ( ! empty($val)) {
                    $body_str.= "    <string name=\"{$key}\">{$val}</string>\n";
                }
            }
            $contents = $header_str . $body_str . $end_str;

            //write
            $fp = fopen($filepath . "/strings.xml", "w");
            fwrite($fp, $contents);
            fclose($fp);
        }

        //zip
        $zip = new ZipArchive();
        $zipfile = $path . "/{$platform}_strings.zip";
        if ($zip->open($zipfile, ZipArchive::CREATE) === TRUE) {
            foreach ($lang_folder as $lang => $folder) {
                $addedfile = $folder . "/strings.xml";
                $zip->addFile($path . "/" . $addedfile, $addedfile);
            }
        }
        $zip->close();

        //Download file
        $this->_download_file($zipfile);
    }

    private function _download_file($file) {
        if (file_exists($file)) {
            header("Content-Description: File Transfer");
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=" . basename($file));
            header("Expires: 0");
            header("Cache-Control: must-revalidate");
            header("Pragma: public");
            header("Content-Length: " . filesize($file));
            readfile($file);
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

    public function export() {
        $p = $this->input->get("p");
        list($production, $platform) = explode("_", $p);
        $db_data = $this->l10n_model->get_translate($p);
        $json_arr = $resp = [];
        foreach ($db_data as $row) {
            $json_arr["en-US"][$row["keyword"]] = $row["en-US"];
            $json_arr["ja-JP"][$row["keyword"]] = $row["ja-JP"];
            $json_arr["zh-TW"][$row["keyword"]] = $row["zh-TW"];
            $json_arr["id-ID"][$row["keyword"]] = $row["id-ID"];
            $json_arr["ms-MY"][$row["keyword"]] = $row["ms-MY"];
        }

        $this->load->config("aws");
        $s3_config = $this->config->item("s3");
        foreach (["en-US", "ja-JP", "zh-TW", "id-ID", "ms-MY"] as $use_lang) {
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
        if ($resp["status"] == "ok") {
            AwsS3::get_instance()->pub_object($s3_config["bucket"]["L10N"], "{$production}/{$platform}/all_lang.json", json_encode($json_arr));
        }

        echo json_encode($resp, TRUE);
    }

    private function _remove_folder($path) {
        $files = glob($path . '/*');
        foreach ($files as $file) {
            is_dir($file) ? $this->_remove_folder($file) : unlink($file);
        }
        rmdir($path);
        return;
    }
}

/* End of file Tool.php */
/* Location: ./app/controllers/tool.php */
