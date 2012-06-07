<?php
/**
 * @class
 * @brief 알림창 띄우고 페이지 이동을 도와주는 클래스
 */
class Alert {
    private static $header_code = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <meta http-equiv="Cache-control" content="no-cache" />
            <meta http-equiv="expires" content="-1" />
        </head>
        <body>
    ';
    private static $footer_code = '</body></html>';

    /**
     * @brief 메시지 출력 후 이전 페이지로 돌아가는 스크립트 코드 반환
     * @param string 메시지
     * @return string
     */
    public static function backScript($msg) {
        // 홑따옴표 처리
        $msg = str_replace( "'", "\\'", $msg);
        // html 처리
        $msg_html = htmlspecialchars($msg);

        $script = <<<CODE
<script>
    alert('{$msg}');
    history.back();
</script>
<noscript>
    <h1>{$msg_html}</h1>
</noscript>
CODE;
        return $script;
    }

    /**
     * @brief 메시지 출력 후 특정 URL로 이동하는 스크립트 코드 반환
     * @param string 메시지
     * @param string 이동할경로
     * @return string
     */
    public static function moveScript($msg, $url) {
        // 홑따옴표 처리
        $msg = str_replace("'", "\\'", $msg);
        // html 처리
        $msg_html = htmlspecialchars($msg);
        $url_html = htmlspecialchars($url);

        $script = <<<CODE
<script>
    alert('{$msg}');
    window.location.href = '{$url}';
</script>
<noscript>
    <h1><a href="{$url_html}">{$msg_html}</a></h1>
</noscript>
CODE;
        return $script;
    }

    /**
     * @brief 메시지 출력 후 창을 닫는 스크립트 코드 반환
     * @param string 메시지
     * @return string
     */
    public static function closeScript($msg) {
        // 홑따옴표 처리
        $msg = str_replace("'", "\\'", $msg);
        // html 처리
        $msg_html = htmlspecialchars($msg);

        $script = <<<CODE
<script>
    alert('{$msg}');
    window.close();
</script>
<noscript>
    <h1>{$msg_html}</h1>
</noscript>
CODE;
        return $script;
    }

    /**
     * @brief backScript()가 반환한 값을 바로 출력한다.
     * @param string 메시지
     */
    public static function backPrint($msg) {
        echo self::backScript($msg);
    }

     /**
     * @brief moveScript()가 반환한 값을 바로 출력한다.
     * @param string 메시지
     * @param string 이동할경로
     */
    public static function movePrint($msg, $url) {
        echo self::moveScript($msg, $url);
    }

    /**
     * @brief closeScript()가 반환한 값을 바로 출력한다.
     * @param string 메시지
     * @param string 이동할경로
     */
    public static function closePrint($msg) {
        echo self::closeScript($msg);
    }

    /**
     * @brief 메시지를 출력 후 이전 페이지로 돌아간다.
     * 내부적으로 exit 코드가 호출된다.
     * @param string 메시지
     */
    public static function back($msg) {
        echo self::$header_code;
        echo self::backScript($msg);
        echo self::$footer_code;
        exit;
    }

    /**
     * @brief 메시지를 출력 후 특정 URL로 이동한다.
     * 내부적으로 exit 코드가 호출된다.
     * @param string 메시지
     * @param string 이동할경로
     */
    public static function move($msg, $url) {
        echo self::$header_code;
        echo self::moveScript($msg, $url);
        echo self::$footer_code;
        exit;
    }

    /**
     * @brief 메시지를 출력 후 창을 닫는다.
     * 내부적으로 exit 코드가 호출된다.
     * @param string 메시지
     */
    public static function close($msg) {
        echo self::$header_code;
        echo self::closeScript($msg);
        echo self::$footer_code;
        exit;
    }
}
?>
