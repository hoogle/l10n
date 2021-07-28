<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");

final class Translate_model extends MY_Model
{

	/**
	 * [__construct description]
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table = "translate";
		$this->table_user = "l10n_user";
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
            //if ($key != "`{$use_lang}`")
            {
                $update_fields[] = $key . " = '{$value}'";
            }
        }
        $DB = $this->_get_db();
        $sql = $DB->insert_string($this->table, $data) . " ON DUPLICATE KEY UPDATE " . implode(", ", $update_fields);
        log_message("INFO", "SQL1: (translate->update) " . $sql);
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
        $DB->insert($this->table, $data);
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
        $DB->where("email", $data["email"]);
        $DB->update($this->table_user, $data);
        return $DB->affected_rows() ? TRUE : FALSE;
    }

    public function get_platforms()
    {
        $DB = $this->_get_db();
        $DB->distinct();
        $DB->select("production");
        $DB->select("platform");
        $data = $DB->get($this->table)->result_array();
        return $data ?: FALSE;
    }

    public function get_l10n_old($platform = "goface") {
        $DB = $this->_get_db();
        $DB->where("platform", $platform);
        $DB->order_by("id", "ASC");
        $data = $DB->get($this->table_l10n_old)->result_array();
        return $data ?: FALSE;
    }

    public function get_translate_by_page($limit_start, $limit_length, $pf, $orderby_arr = ["keyword", "DESC"], $key = "") {
        $DB = $this->_get_db();
        $DB->from($this->table);
        $DB->where("platform", $pf);
        list($order, $by) = $orderby_arr;
        if ( ! empty($key)) {
            $DB->group_start();
            $DB->like("keyword", $key);
            $DB->or_like("default_str", $key);
            foreach (["`en-US`", "`ja-JP`", "`zh-TW`", "`id-ID`", "`ms-MY`"] as $lang) {
                $DB->or_like($lang, $key);
            }
            $DB->group_end();
        }
        $tempdb = clone $DB;
        $num_rows = $tempdb->count_all_results();
        $DB->order_by($order, $by);
        $DB->limit($limit_length, $limit_start);
        $data = $DB->get()->result_array();
        log_message("INFO", "SQL : translate_model->get_translate_by_page() " . $DB->result_id->queryString);
        return $data ? ["data" => $data, "rows" => $num_rows] : [];
    }

    public function get_translate($platform) {
        $DB = $this->_get_db();
        $DB->where("platform", $platform);
        $DB->order_by("id", "ASC");
        $data = $DB->get($this->table)->result_array();
        return $data ?: FALSE;
    }

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
