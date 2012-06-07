<?php
/**
 * @brief 전역 객체
 */
class GlobalObject {
    private static $instance = null;

    /**
     * @brief 싱글톤 객체 반환
     */
    public static function singleton() {
        if ( !$instance ) {
            $instance = new self();
        }

        return $instance;
    }

    private function __construct() {}

    /**
     * @brief __get
     * @param $name string
     * @return mixed
     */
    public function __get($name) {
        if ( $name == 'db' ) {
            require_once DIR_LIB.'/common/MySQL.php';

            $this->db = new MySQL();
            return $this->db;
        }

        if ( $name == 'alert' ) {
            require_once DIR_LIB.'/common/Alert.php';
            return $this->alert = new Alert();
        }

    }
}
?>
