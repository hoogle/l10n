<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");

final class Translate_model extends MY_Model
{

    const LANG_ARR = ["en-US", "zh-TW", "ja-JP", "id-ID", "ms-MY"];
    /*
     * ALTER TABLE `trans_relation` ADD `ui_key` VARCHAR(20) NULL AFTER `id`;
     */

	/**
	 * [__construct description]
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table = "translate";
		$this->table_user = "l10n_user";
		$this->table_platform = "platform";
		$this->table_relation = "trans_relation";
		$this->table_l10n_old = "l10n_old";
	}

	/**
	 * [get description]
	 * @param  [type] $id [description]
	 * @return [type]          [description]
	 */
	public function get($id) {
		$db = $this->_get_db();
		$db->where("id", $id);
		$data = $db->get($this->table)->row_array();
		return $data ?: FALSE;
	}

	/**
	 * [get_all_email description]
	 * @param  [type] $production [description]
	 * @param  [type] $platform   [description]
	 * @return [type]             [description]
	 */
	public function get_all_email_info($production, $platform) {
		$db = $this->_get_db();
        $where_arr = [
            "production" => $production,
            "platform" => $platform,
        ];
        $sql = "SELECT DISTINCT(`item`), MAX(`updated_at`) AS `last_updated_at` ";
        $sql.= "FROM `translate` WHERE `production` = 'goface' AND `platform` = 'email' GROUP BY `item`";
        $data = [];
        foreach ($db->query($sql)->result_array() as $row) {
            $arr = $this->get_email_data($production, $platform, $row["item"], "SUBJECT");
            $arr["last_updated_at"] = $row["last_updated_at"];
            $data[] = $arr;
        }
		return $data;
	}

	/**
	 * [get_relation_by_page description]
	 * @return [type]             [description]
	 */
	public function get_relation_by_page($limit_start, $limit_length, $orderby_arr = ["id", "ASC"], $key = "") {
		$db = $this->_get_db();
        $db->from($this->table);
        $db->where("`ui_key` IN", "(SELECT `ui_key` FROM `trans_relation`)", FALSE);
        if ( ! empty($key)) {
            $db->group_start();
            $db->like("ui_key", $key);
            foreach ($this::LANG_ARR as $lang) {
                $db->or_like($lang, $key);
            }
            $db->group_end();
        }
        list($order, $by) = $orderby_arr;
        $tempdb = clone $db;
        $num_rows = $tempdb->count_all_results();
        $db->order_by($order, $by);
        $db->limit($limit_length, $limit_start);
        $data = $trans_data = [];
        $data = $db->get()->result_array();
        return $data ? ["data" => $data, "rows" => $num_rows] : [];
    }

	/**
	 * [get_relation_data description]
	 * @return [type]             [description]
	 */
	public function get_relation_by_page2($limit_start, $limit_length, $orderby_arr = ["id", "ASC"], $key = "") {
		$db = $this->_get_db();
        $db->from($this->table_relation);
        if ( ! empty($key)) {
            $db->like("ui_key", $key);
        }
        list($order, $by) = $orderby_arr;
        $tempdb = clone $db;
        $num_rows = $tempdb->count_all_results();
        $db->order_by($order, $by);
        $db->limit($limit_length, $limit_start);
        $data = $trans_data = [];
        if ($arr = $db->get()->result_array()) {
            foreach ($arr as $row) {
                $id_arr = json_decode($row["trans_ids"], TRUE);
                foreach ($id_arr as $os => $id) {
                    $lang_data = $this->get($id);
                    foreach ($this::LANG_ARR as $lang) {
                        $trans_data[$row["id"]][$os][$id][$lang] = $lang_data[$lang];
                    }
                }
            }
            $data = $trans_data;
        }
        return $data ? ["data" => $data, "rows" => $num_rows] : [];
    }

	/**
	 * [get_email_data description]
	 * @param  [type] $production [description]
	 * @param  [type] $platform   [description]
	 * @param  [type] $item       [description]
	 * @param  [type] [$keyword]  [description]
	 * @return [type]             [description]
	 */
	public function get_email_data($production, $platform, $item, $keyword = "") {
		$db = $this->_get_db();
        $where_arr = [
            "production" => $production,
            "platform" => $platform,
            "item" => $item,
        ];
        ! empty($keyword) && $where_arr["keyword"] = $keyword;
        $db->where($where_arr);
        if ( ! empty($keyword)) {
            $data = $db->get($this->table)->row_array();
        } else {
            $data = $db->get($this->table)->result_array();
        }
		return $data ?: FALSE;
	}

	/**
	 * [update description]
	 * @param  $data  [description]
	 * @return [type] [description]
	 */
	public function update(Array $data) {
		$data["updated_at"] = date("Y-m-d H:i:s");
        $DB = $this->_get_db();
        $DB->where([
            "production" => $data["production"],
            "platform" => $data["platform"],
            "keyword" => $data["keyword"]
        ]);
        $DB->update($this->table, $data);
        log_message("INFO", "SQL : translate_model->update() " . $DB->result_id->queryString);
        return $DB->affected_rows();
	}

    public function update_autoincrement() {
        $DB = $this->_get_db();
        $DB->select_max("id");
        $data = $DB->get($this->table)->row_array();
        $sql = "ALTER TABLE {$this->table} AUTO_INCREMENT = " . ++$data["id"];
        $DB->query($sql);
        log_message("INFO", "fix last insert id as " . $data["id"]);
    }

    public function add_translate($data) {
        $data["created_at"] = $data["updated_at"] = date("Y-m-d H:i:s");
        $DB = $this->_get_db();
        $DB->insert($this->table, $data);
        return $DB->insert_id();
    }

    public function update_translate($id, $data, $need_specialchar = 0) {
        $data["updated_at"] = date("Y-m-d H:i:s");
        if ($need_specialchar) {
            foreach ($this::LANG_ARR as $use_lang) {
                if (isset($data[$use_lang])) {
                    $data[$use_lang] = str_replace("'", "&#39;", $data[$use_lang]);
                }
            }
        }
        $DB = $this->_get_db();
        $DB->where("id", $id);
        $DB->update($this->table, $data);
        return $DB->affected_rows();
    }

    public function user_last_update($email, $data) {
        $DB = $this->_get_db();
        $DB->where("email", $email);
        $DB->update($this->table_user, $data);
        return $DB->affected_rows();
    }

    public function get_platform_stat() {
        $DB = $this->_get_db();
        $data = $DB->get($this->table_platform)->result_array();
        $return_data = [];
        foreach ($data as $pf) {
            $pf["modified"] = $pf["published_at"] < $pf["updated_at"] ? 1 : 0;
            $return_data[$pf["production_platform"]] = $pf;
        }
        return $return_data;
    }

    public function update_platform($prod_pf, $type = "update") {
        if ($type == "publish") {
            $data = ["published_at" => date("Y-m-d H:i:s")];
        } else {
            $data = ["updated_at" => date("Y-m-d H:i:s")];
        }
        $DB = $this->_get_db();
        $DB->where("production_platform", $prod_pf);
        $DB->update($this->table_platform, $data);
        return $DB->affected_rows();
    }

    public function get_pfk_exists($data) {
        $DB = $this->_get_db();
        $DB->where($data);
        return $DB->get($this->table)->result_array() ?: [];
    }

    public function get_template_error_data($table = "template") {
        $DB = $this->_get_db();
        $data = $DB->get($table)->result_array();
        return $data ?: FALSE;
    }

    public function get_user_pwd($email) {
        $DB = $this->_get_db();
        $DB->select("passwd");
        $data = $DB->get_where($this->table_user, ["email" => $email])->row_array();
        return $data ? $data["passwd"] : FALSE;
    }

    public function update_user_data($data) {
        $DB = $this->_get_db();
        $DB->where("email", $data["email"]);
        $DB->update($this->table_user, $data);
        return $DB->affected_rows() ? TRUE : FALSE;
    }

    public function get_platforms()
    {
        $DB = $this->_get_db();
        $db_data = $DB->get($this->table_platform)->result_array();
        $data = [];
        foreach ($db_data as $raw) {
            list($prod, $pf) = explode("_", $raw["production_platform"]);
            $data[] = [
                "production" => $prod,
                "platform"   => $pf
            ];
        }
        return $data ?: FALSE;
    }

    public function get_l10n_old($platform = "goface") {
        $DB = $this->_get_db();
        $DB->where("platform", $platform);
        $DB->order_by("id", "ASC");
        $data = $DB->get($this->table_l10n_old)->result_array();
        return $data ?: FALSE;
    }

    public function get_translate_by_page($limit_start, $limit_length, $prod, $pf, $orderby_arr = ["keyword", "DESC"], $key = "") {
        $p = $prod . "_" . $pf;
        $pf_stat = $this->get_platform_stat();
        $pf_published_at = $pf_stat[$p]["published_at"];
        $DB = $this->_get_db();
        $DB->from($this->table);
        $DB->where("production", $prod);
        $DB->where("platform", $pf);
        list($order, $by) = $orderby_arr;
        if ( ! empty($key)) {
            $DB->group_start();
            $DB->like("keyword", $key);
            foreach ($this::LANG_ARR as $lang) {
                $DB->or_like($lang, $key);
            }
            $DB->group_end();
        }
        $tempdb = clone $DB;
        $num_rows = $tempdb->count_all_results();
        $DB->order_by($order, $by);
        $DB->limit($limit_length, $limit_start);
        $data = $DB->get()->result_array();
        foreach ($data as &$row) {
            $row["dot"] = $row["updated_at"] > $pf_published_at ? "1" : "0";
        }
        log_message("INFO", "SQL : translate_model->get_translate_by_page() " . $DB->result_id->queryString);
        return $data ? ["data" => $data, "rows" => $num_rows] : [];
    }

    public function get_translate_by_id($id) {
        $DB = $this->_get_db();
        $DB->where("id", $id);
        $data = $DB->get($this->table)->result_array();
        return $data ? ["data" => $data, "rows" => 1] : [];
    }

    public function get_email_contents($prod, $plat, $item) {
        $DB = $this->_get_db();
        $where_arr = [
            "production" => $prod,
            "platform" => $plat,
            "item" => $item,
        ];
        $DB->where($where_arr);
        $DB->order_by("id", "ASC");
        $data = $DB->get($this->table)->result_array();
        return $data ?: FALSE;
    }

    public function get_email_contents_by_lang($prod, $plat, $item, $user_langs = []) {
        $lang_arr = [];
        if ($arr = $this->get_email_contents($prod, $plat, $item)) {
            $languages = $user_langs ?: $this::LANG_ARR;
            foreach ($arr as $row) {
                foreach ($languages as $lang) {
                    $lang_arr[$lang][$row["keyword"]] = [
                        "id" => $row["id"],
                        "val" => $row[$lang],
                    ];
                }
            }
        }
        return $lang_arr ?: FALSE;
    }

    /**
     * DEPRECATED!!!!
     */
    /*
    public function get_translate($platform) {
        $DB = $this->_get_db();
        $DB->where("platform", $platform);
        $DB->order_by("id", "ASC");
        $data = $DB->get($this->table)->result_array();
        return $data ?: FALSE;
    }
     */

    public function get_last_id() {
        $DB = $this->_get_db();
        $DB->select("id");
        $DB->order_by("id", "DESC");
        $DB->limit(1);
        $data = $DB->get($this->table)->row_array();
        return $data["id"] + 1 ?: FALSE;
    }

}

/* End of file L10n_model.php */
/* Location: ./app/models/L10n_model.php */
