<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");


final class L10n_model extends MY_Model
{

	/**
	 * [__construct description]
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table = "l10n";
		$this->table_user = "l10n_user";
		$this->table_translate = "translate";
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
	 * [fetchTranslation description]
	 * @param  [type] $key [description]
	 * @return [type]          [description]
	 */
	public function fetchTranslation($platform, $view_path, $keyword) {
		$db = $this->_get_db();
        $where_arr = [
            "platform" => $platform,
            "view_path" => $view_path,
            "keyword" => $keyword,
        ];
		$db->where($where_arr);
		$data = $db->get($this->table)->row_array();
		return $data ?: FALSE;
	}

	/**
	 * [update description]
	 * @param  $data  [description]
	 * @return [type] [description]
	 */
	public function update(Array $data, $use_lang) {
		$data["created_at"] = $data["updated_at"] = date("Y-m-d H:i:s");
        $update_fields = [];
        foreach($data as $key => &$value)
        {
            if ($key == "created_at") continue;
            $value = str_replace("'", "&#39;", $value);
            if ($key != "`{$use_lang}`")
            {
                $update_fields[] = $key . " = '{$value}'";
            }
        }
        $DB = $this->_get_db();
        $sql = $DB->insert_string($this->table, $data) . " ON DUPLICATE KEY UPDATE " . implode(", ", $update_fields);
        log_message("INFO", "SQL1: (l10n->update) " . $sql);
        //echo "SQL : " . $sql . "<br>\n";
        $DB->query($sql);
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
        $DB = $this->_get_db();
        $DB->insert($this->table, $data);
        return $DB->insert_id();
    }

    public function update_translate($id, $data) {
        foreach (["`en-US`", "`ja-JP`", "`zh-TW`", "`id-ID`", "`ms-MY`"] as $use_lang) {
            if (isset($data[$use_lang])) {
                $data[$use_lang] = str_replace("'", "&#39;", $data[$use_lang]);
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

    public function translator($data) {
        $DB = $this->_get_db();
        $DB->insert($this->table_translate, $data);
        return $DB->insert_id();
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
        $user = $DB->get_where($this->table_user, ["email" => $data["email"]])->row_array();
        if ( ! $user) {
            $data = [
                "email" => $data["email"],
                "last_update" => json_encode([], JSON_FORCE_OBJECT),
                "created_at" => date("Y-m-d H:i:s"),
                "last_login_at" => date("Y-m-d H:i:s"),
            ];
            $DB->insert($this->table_user, $data);
        } else {
            $DB->where("email", $data["email"]);
            $DB->update($this->table_user, $data);
        }
        return $DB->affected_rows() ? TRUE : FALSE;
    }

    public function get_platforms()
    {
        $DB = $this->_get_db();
        $DB->select("platform");
        $DB->group_by("platform");
        $data = $DB->get($this->table)->result_array();
        return $data ? array_column($data, "platform") : FALSE;
    }

    public function get_l10n_by_platform($limit_start, $limit_length, $pf, $key = "") {
        $this->load->library("pagination");
        $DB = $this->_get_db();
        $DB->from($this->table);
        $DB->where("platform", $pf);
        if ( ! empty($key)) {
            $DB->group_start();
            $DB->like("keyword", $key);
            $DB->or_like("default_str", $key);
            $DB->group_end();
        }
        $tempdb = clone $DB;
        $num_rows = $tempdb->count_all_results();
        $DB->order_by("id", "ASC");
        $DB->limit($limit_length, $limit_start);
        $data = $DB->get()->result_array();
        log_message("INFO", "SQL : l10n_model->get_l10n_by_platform() " . $DB->result_id->queryString);
        return $data ? ["data" => $data, "rows" => $num_rows] : [];
    }

    public function get_translate($p) {
        list($production, $platform) = explode("_", $p);
        $DB = $this->_get_db();
        $where_arr = [
            "production" => $production,
            "platform"   => $platform,
        ];
        $DB->order_by("id ASC");
        $data = $DB->get_where($this->table_translate, $where_arr)->result_array();
        return $data ?: FALSE;
    }

}

/* End of file L10n_model.php */
/* Location: ./app/models/L10n_model.php */
