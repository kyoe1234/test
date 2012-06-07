<?php
/**
 * @brief MySQL 클래스
 */
class MySQL {
    private $host;
    private $user;
    private $pw;
    private $db;
    private $port;
    private $socket;

    private $mysqli;
    private $begin_count = 0; // self::begin() 호출 횟수
    private $is_rollback = false; // 롤백 여부
    private $loging_level = 0;
    private $logs = array();

    /**
     * @brief 생성자
     * @param $host string 호스트
     * @param $user string 사용자
     * @param $pw string 비밀번호
     * @param $db string 데이터베이스 이름
     * @param $port int 포트 번호
     * @param $socket string 소켓
     */
    public function __construct($host = MYSQL_HOST, $user = MYSQL_USER,
            $pw = MYSQL_PW, $db = MYSQL_DB, $port = MYSQL_PORT, $socket = MYSQL_SOCKET) {
        $this->host = $host;
        $this->user = $user;
        $this->pw = $pw;
        $this->db = $db;
        $this->port = $port;
        $this->socket = $socket;
    }

    function __destruct() {
        $this->close();
    }

    /**
     * @brief DB 연결
     */
    public function connect() {
        if ( $this->mysqli ) return;

        $this->mysqli = new mysqli();
        $this->mysqli->init();
        $this->mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
        $this->mysqli->connect($this->host, $this->user, $this->pw, $this->db,
            $this->port, $this->socket);

        if ( $this->mysqli->connect_errno ) {
            throw new Exception($this->mysqli->connect_error, $this->mysqli->connect_errno);
        }

        $this->mysqli->set_charset('utf8');

        // 멤버변수 초기화
        $this->begin_count = 0;
        $this->is_rollback = false;

        // 셧다운시 닫기
        register_shutdown_function(array($this, 'close'));
    }

    /**
     * @brief DB 닫기
     */
    public function close() {
        if ( $this->mysqli ) {
            $this->mysqli->close();
            $this->mysqli = null;
        }
    }

    /**
     * @brief 쿼리를 실행한다.
     * @param $sql string 쿼리
     * @return mysqli_result
     */
    public function query($sql) {
        // loging
        if ( $this->loging_level > 0 ) {
            $this->logs[] = $sql;
        }

        try {
            $this->connect();
        } catch ( Exception $e ) {
            throw $e;
        }

        $result = $this->mysqli->query($sql);
        if ( $this->mysqli->errno ) {
            $error = $this->mysqli->error;
            $errno = $this->mysqli->errno;
            $this->rollback();
            $this->close();
            throw new Exception("{$error}\n{$sql}]", $errno);
        }

        return $result;
    }

    /**
     * @brief 쿼리 실행 결과의 전부를 가져온다.
     * @param $sql string 쿼리
     * @param $result_type int [MYSQL_ASSOC|MYSQL_NUM|MYSQL_BOTH]
     * @return array
     */
    public function fetch_all($sql, $result_type = MYSQL_ASSOC) {
        try {
            $result = $this->query($sql);
        } catch ( Exception $e ) {
            throw $e;
        }

        if ( !is_object($result) ) return array();

        $data = array();
        while ( $row = $result->fetch_array($result_type) ) {
            $data[] = $row;
        }

        $result->free();

        return $data;
    }

    /**
     * @brief 쿼리 실행 결과의 1행을 가져온다.
     * @param $sql string 쿼리
     * @param $result_type int [MYSQL_ASSOC|MYSQL_NUM|MYSQL_BOTH]
     * @return array
     */
    public function fetch_row($sql, $result_type = MYSQL_ASSOC) {
        try {
            $result = $this->query($sql);
        } catch ( Exception $e ) {
            throw $e;
        }

        if ( !is_object($result) ) return array();

        $row = $result->fetch_array($result_type);
        $result->free();

        return $row ? $row : array();
    }

    /**
     * @brief 쿼리 실행 결과의 1열을 가져온다.
     * @param $sql string 쿼리
     * @return array
     */
    public function fetch_col($sql) {
        try {
            $result = $this->query($sql);
        } catch ( Exception $e ) {
            throw $e;
        }

        if ( !is_object($result) ) return array();

        $column = array();
        while ( $row = $result->fetch_row() ) {
            $column[] = $row[0];
        }

        $result->free();

        return $column;
    }

    /**
     * @brief 쿼리 실행 결과의 1행1열을 가져온다.
     * @param $sql string 쿼리
     * @return mixed
     */
    public function fetch_val($sql) {
        try {
            $result = $this->query($sql);
        } catch ( Exception $e ) {
            throw $e;
        }

        if ( !is_object($result) ) return null;

        $row = $result->fetch_row();
        $result->free();

        return $row ? $row[0] : null;
    }

    /**
     * @brief 'SELECT FOUND_ROWS()' 값을 구한다.
     * @return int
     */
    public function found_rows() {
        return (int)$this->fetch_val('SELECT FOUND_ROWS()');
    }

    /**
     * @brief 쿼리에 영향을 받은 레코드 개수를 반환한다.
     * @return int
     */
    public function affected_rows() {
        try {
            $this->connect();
        } catch ( Exception $e ) {
            throw $e;
        }
        return $this->mysqli->affected_rows;
    }

    /**
     * @brief AUTO_INCREMENT에 의해 자동 생성된 id를 반환한다.
     * @return int
     */
    public function insert_id() {
        try {
            $this->connect();
        } catch ( Exception $e ) {
            throw $e;
        }
        return $this->mysqli->insert_id;
    }

    /**
     * @brief 트랜잭션 시작
     */
    public function begin() {
        try {
            $this->connect();
        } catch ( Exception $e ) {
            throw $e;
        }

        if ( $this->begin_count == 0 ) {
            $this->mysqli->autocommit(false);
        }

        ++$this->begin_count;
    }

    /**
     * @brief 트랜잭션 커밋
     */
    public function commit() {
        if ( !$this->mysqli ) return;

        // 롤백이 진행중이면 커밋도 롤백으로 처리
        if ( $this->is_rollback ) {
            return $this->rollback();
        }

        if ( $this->begin_count == 0 ) return;

        // 시작 카운트 감소
        --$this->begin_count;

        if ( $this->begin_count == 0 ) {
            $this->mysqli->commit();
            $this->mysqli->autocommit(true);
        }
    }

    /**
     * @brief 트랜잭션 롤백
     */
    public function rollback() {
        if ( !$this->mysqli ) return;

        if ( $this->begin_count == 0 ) return;

        // 시작 카운트 감소
        --$this->begin_count;

        if ( $this->begin_count == 0 ) {
            $this->mysqli->rollback();
            $this->mysqli->autocommit(true);
            $this->is_rollback = false;
        } else {
            $this->is_rollback = true;
        }
    }

    /**
     * @brief 단일 변수, object, array의 모든 값을 escape 처리
     * @param $var mixed
     * @return mixed
     */
    public function escape($var) {
        try {
            $this->connect();
        } catch ( Exception $e ) {
            throw $e;
        }

        if ( is_array($var) || is_object($var) ) {
            foreach ( $var as &$v ) {
                $v = $this->escape($v);
            }
        } else if ( is_scalar($var) ) {
            $var = $this->mysqli->real_escape_string($var);
        }

        return $var;
    }

    /**
     * @brief 로깅 설정
     * @param $level int 레벨(0:OFF, 1:ON)
     */
    public function loging($level) {
        $this->loging_level = $level;
    }

    /**
     * @brief 로그 데이터 반환
     * @return array
     */
    public function logs() {
        return $this->logs;
    }

    /**
     * @brief auto-commit 여부를 반환한다.
     * @return boolean
     */
    public function is_autocommit() {
        return $this->fetch_val('SELECT @@autocommit') == 1;
    }
}
?>
