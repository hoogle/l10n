<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class MY_Model extends CI_Model {

    protected static $DB = NULL;
    protected $table     = '';

    /**
     * [__construct description]
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * [_get_db description]
     * @return [type] [description]
     */
    protected function _get_db() {
        (is_null(self::$DB)) && self::$DB = $this->load->database('default', TRUE);
        return self::$DB;
    }

    /**
     * [close description]
     * @return [type] [description]
     */
    protected function close() {
        ( ! is_null(self::$DB)) && self::$DB->close();
    }

}
/* End of file MY_Model.php */
/* Location: ./app/core/MY_Model.php */