<?php
class UserPhoto {
    /** @brief JPEG 화질 */
    const JPEG_QUALITY = 60;
    //const DIR_FILE = '/home/kyoe/project/withblog/www/web/_dev/facechart/file';
    //const URL_FILE = 'http://withblog.net/_dev/facechart/file';

    /** @brief 사진 크기 종류 */
    private static $size_list = array(80, 160, 320, 640);

    /**
     * @brief userid에 따른 그룹번호를 반환한다.
     * @param $user_id int test.userthumb.userid
     * @return int
     */
    private static function group_number($user_id) {
        if ( !preg_match('/^[1-9][0-9]*$/', $user_id) ) return 0;
        return ceil($user_id / 1000);
    }

    /**
     * @brief 파일 디렉터리 경로를 반환
     * @param $user_id int test.userthumb.userid
     * @return string
     */
    private static function file_dir($user_id) {
        $group = self::group_number($user_id);
        return DIR_MOBILE_FILE.'/user/'.$group;
    }

    /**
     * @brief 파일 URL을 반환
     * @param $user_id int test.userthumb.userid
     * @return string
     */
    private static function file_url($user_id) {
        $group = self::group_number($user_id);
        return URL_MOBILE_FILE.'/user/'.$group;
    }

    /**
     * @brief 추가
     * @param $image_path string 이미지 경로
     * @param $warning object Warning 객체 참조
     * @return int
     */
    public static function add($user_id, $files, &$warning = null) {
        global $g;

        if ( !preg_match('/^[1-9][0-9]*$/', $user_id) ) {
            return Warning::make($warning, false, 'userid', 'idx(숫자)를 입력해주세요');
        }

        $image_path = $files['profile_image']['tmp_name'];

        // 파일 디렉토리
        $file_dir = self::file_dir($user_id);
        if ( !is_dir($file_dir) ) {
            if ( !mkdir($file_dir, 0777, true) ) {
                return Warning::make($warning, false, 'mkdir', '디렉토리 생성 오류');
            }
        }

        // 저장 경로
        $time = time();
        $save_path = $file_dir."/{$user_id}_{$time}.jpg";


        ## 원본 저장 ##
        $mime_type = mime_content_type($image_path);
        if ( $mime_type == 'image/jpeg' ) {
            $result = rename($image_path, $save_path);
            if ( !$result ) {
                $result = copy($image_path, $save_path);
            }

            if ( !$result ) {
                return Warning::make($warning, false, 'move_origin', '원본 이동 오류');
            }

            chmod($save_path, 0644);
        } else {
            // 원본 소스
            $origin_img = self::create_image_resource($image_path);
            if ( !$origin_img ) {
                return Warning::make($warning, false, 'origin_resource', '원본 리소스 생성 오류');
            }

            $result = imagejpeg($origin_img, $save_path, self::JPEG_QUALITY);
            if ( !$result ) {
                return Warning::make($warning, false, 'save_origin', '원본 저장 오류');
            }
        }


        ## 섬네일 생성 ##
        $result = self::make_thumb($user_id, $time, $warning);
        if ( !$result ) {
            return $warning->remake(false);
        }

        // db 등록
        $createdate = date('Y-m-d H:i:s', $time);
        $sql = "INSERT test.userphoto SET
                    userid = {$user_id},
                    createdate = '{$createdate}'";
        $g->db->query($sql);

        return Warning::make($warning, true);
    }

    /**
     * @brief 이미지 파일에 해당하는 GD 이미지 리소스를 생성하여 반환한다.
     * @param $image_path string 이미지 파일 경로(Local 또는 URL)
     * @return resource GD 이미지 리소스, 지원하지 않는 이미지 파일은 null 반환
     */
    private static function create_image_resource($image_path) {
        $mime = mime_content_type($image_path);

        // png, jpg, gif, bmp만 지원
        switch ( $mime ) {
            case 'image/png':
                return imagecreatefrompng($image_path);
            case 'image/jpeg':
                return imagecreatefromjpeg($image_path);
            case 'image/gif':
                return imagecreatefromgif($image_path);
            case 'image/bmp':
                return imagecreatefromwbmp($image_path);
            default:
                return null;
        }
    }

    /**
     * @brief 이미지를 정사각형으로 리사이즈 하여 반환한다.
     * @param $image resource GD 이미지 리소스
     * @param $size int 크기
     * @return resource GD 이미지 리소스
     */
    private static function square_resize($image, $size) {
        $img_w = imagesx($image);
        $img_h = imagesy($image);

        // 가로와 세로중 더 작은 쪽을 기준으로 복사 범위를 계산한다.
        if ( $img_w < $img_h ) {
            $src_w = $img_w;
            $src_h = $img_w;
            $src_x = 0;
            $src_y = ($img_h - $img_w) / 2;
        } else {
            $src_w = $img_h;
            $src_h = $img_h;
            $src_x = ($img_w - $img_h) / 2;
            $src_y = 0;
        }

        // 새 이미지
        $resize_image = imagecreatetruecolor($size, $size);
        // 원본에서 새 이미지로 리사이즈
        $result = imagecopyresampled($resize_image, $image, 0, 0, $src_x, $src_y, $size, $size, $src_w, $src_h);
        if ( !$result ) return null;

        return $resize_image;
    }

    /**
     * @brief 섬네일을 만든다.
     * @warning 섬네일을 재생성 하고 싶을때도 사용
     * @param $user_id int test.userthumb.userid
     * @param $time int time()
     * @param $warning object Warning 객체 참조
     * @return boolean
     */
    public static function make_thumb($user_id, $time, &$warning = null) {
        $file_dir = self::file_dir($user_id);

        // 원본 사진
        $file_path = $file_dir."/{$user_id}_{$time}.jpg";
        if ( !is_file($file_path) ) {
            return Warning::make($warning, false, 'origin_file', '원본을 찾을 수 없음.');
        }

        // 원본 소스
        $origin_img = self::create_image_resource($file_path);
        if ( !$origin_img ) {
            return Warning::make($warning, false, 'origin_resource', '원본 리소스 생성 오류');
        }

        ## 섬네일 생성 ##
        foreach ( self::$size_list as $size ) {
            // 섬네일 저장 경로
            $thumb_path = preg_replace('/\.[^.]+$/', "_{$size}$0", $file_path);

            $thumb_img = self::square_resize($origin_img, $size);
            if ( !$thumb_img ) {
                return Warning::make($warning, false, 'resize_thumb', '섬네일 리사이즈 오류');
            }

            $result = imagejpeg($thumb_img, $thumb_path, self::JPEG_QUALITY);
            if ( !$result ) {
                return Warning::make($warning, false, 'save_thumb', '섬네일 저장 오류');
            }
        }

        return Warning::make($warning, true);
    }

    /**
     * @brief 생성자
     * @param $user_id int test.userthumb.userid
     */
    public function __construct($user_id) {
        global $g;

        $row = $g->db->fetch_row("
            SELECT * FROM test.userphoto
                WHERE userid = {$user_id}
        ");

        foreach ( $row as $k => $v ) {
            $this->$k = $v;
        }

        // 사진 파일
        $file_url = self::file_url($this->userid);
        $file_dir = self::file_dir($this->userid);

        $time = strtotime($this->createdate);

        // 원본 사진
        $this->photo[0] = $file_url."/{$this->userid}_{$time}.jpg";
        $this->photo_file[0] = $file_dir."/{$this->userid}_{$time}.jpg";

        // 섬네일
        foreach ( self::$size_list as $size ) {
            $filename = "{$this->userid}_{$time}_{$size}.jpg";
            $this->photo[$size] = $file_url.'/'.$filename;
            $this->photo_file[$size] = $file_dir.'/'.$filename;
        }
    }

}

?>
