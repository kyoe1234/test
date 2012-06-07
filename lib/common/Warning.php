<?php
/**
 * @brief 추가적 정보 전달용
 * @code
 * function foo($bar, &$warning = null) {
 *     if ( !$bar ) {
 *         return Warning::make($warning, false, 'fail', '오류‘);
 *     }
 *     return Warning::make($warning, true, 'success', '성공‘);
 * }
 * @endcode
 */
class Warning {
    /**
     * @brief Warning 객체를 만든다.
     * @param $warning object 만들어진 Warning 객체를 받을 참조 변수
     * @param $result mixed 결과값
     * @param $code string 코드
     * @param $text string 텍스트
     * @return mixed 인수로 받은 $result
     */
    public static function make(&$warning, $result, $code = '', $text = '') {
        list(, $caller) = debug_backtrace();

        if ( $caller['type'] == '->' ) {
            $target = $caller['object'];
        } else if ( $caller['type'] == '::' ) {
            $target = $caller['class'];
        } else {
            $target = $caller['function'];
        }

        $warning = new self($target, $result, $code, $text);
        return $result;
    }

    public $target; /**< @brief 타겟 함수 */
    public $result; /**< @brief 결과값 */
    public $code = ''; /**< @brief 코드 */
    public $text = ''; /**< @brief 텍스트 */

    /**
     * @brief 생성자
     * @param $target function 타겟 함수
     * @param $result mixed 결과값
     * @param $code string 코드
     * @param $text string 텍스트
     */
    private function __construct($target, $result, $code = '', $text = '') {
        $this->target = $target;
        $this->result = $result;
        $this->code = $code;
        $this->text = $text;
    }

    /**
     * @brief __toString
     * @return string
     */
    public function __toString() {
        return $this->text;
    }

    /**
     * @brief 객체의 값을 고친다.
     * @return mixed 인수로 받은 $result
     */
    public function remake($result, $code = '', $text = '') {
        $this->result = $result;
        if ( $code ) $this->code = $code;
        if ( $text ) $this->text = $text;

        return $result;
    }

    /**
     * @brief 객체의 데이터를 JSON 형식으로 반환한다.
     * @return string
     */
    public function json() {
        return json_encode($this);
    }
}
?>
