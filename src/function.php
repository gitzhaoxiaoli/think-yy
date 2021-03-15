<?php

/**
 * 返回信息
 *
 * @param array  $data 数据
 * @param string $code 字符串或int
 * @param string $msg
 */
function api( $data = [], $code = 200, $msg = '', $e = false )
{
    // var_dump($e);
    if ( !$msg ) {
        $msg = \yy\exception\Error::$errCode[ $code ] ?? '';
    }
    $detail = [
        'data' => $data,
        'code' => $code,
        'msg' => $msg,
    ];
    // if ( $file ) $detail[ 'file' ] = $file;
    // if ( $line ) $detail[ 'line' ] = $line;
    if( $e ){
        $detail[ 'file' ] = $e -> getFile();
        $detail[ 'line' ] = $e -> getLine();
        $detail[ 'trace' ] = $e -> getTrace();
    }
    if ( isset( $GLOBALS['formValidate'] ) ) $detail[ 'formValidate' ] = $GLOBALS[ 'formValidate' ];
    $GLOBALS[ 'apiResult' ] = $detail;
    // halt($detail);
    return json( $detail );
}

/**
 * 控制器方法自动返回错误
 *
 * @param $cb
 *
 * @return \think\response\Json
 */
function tryCatch( $cb )
{
    try {
        $res = $cb();
    } catch ( \Exception $e ) {
        //return api( [], $e -> getCode(), $e -> getMessage(), $e -> getFile(), $e -> getLine() );
        return api( [], $e -> getCode(), $e -> getMessage(), $e );
    }
    return api( $res );
}

//接受参数
function myInput()
{
    //input传进来的
    $input = file_get_contents( 'php://input', 'r' );
    $input = json_decode( $input, true );
    //get和post传进来的
    $getAndPost = input();
    $getAndPost = clearSpace( $getAndPost );
    if ( isset( $getAndPost[ 'params' ] ) ) {
        unset( $getAndPost[ 'params' ] );
    }

    //get和post传进来的json
    $json = input( 'params' );
    if ( $json ) {
        $json = json_decode( $json, true );
    }

    $params = [];
    if ( isset( $input ) && $input && is_array( $input ) ) {
        $params = array_merge( $params, $input );
    } else if ( isset( $getAndPost ) && $getAndPost && is_array( $getAndPost ) ) {
        $params = array_merge( $params, $getAndPost );
    } else if ( isset( $json ) && $json && is_array( $json ) ) {
        $params = array_merge( $params, $json );
    }
    $params = clearSpace( $params );

    return $params;
}

/**
 * 遍历去首尾空格
 */
function clearSpace( $params )
{
    if ( is_array( $params ) ) {
        foreach ( $params as $k => $p ) {
            if ( is_string( $p ) ) {
                $params[ $k ] = trim( $p );
                if ( !$params[ $k ] ) {
                    unset( $params[ $k ] );
                }
            } else if ( is_array( $p ) ) {
                $params[ $k ] = clearSpace( $p );
            }
        }
    } else if ( is_string( $params ) ) {
        $params = trim( $params );
    }
    return $params;
}

/**
 * 返回数组的最后一个
 */
function arrEnd( $arr )
{
    return end( $arr );
}


function httpRequest( $url, $method = 'GET', $postData = [], $headers = [] , $cookie = '' )
{
    // 创建一个cURL资源
    $ch = curl_init();
    // 设置URL和相应的选项
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_HEADER, 0 );
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    if ( stripos( $url, 'https' ) === 0 ) {
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    //不用https验证
    }
    if ( $method == 'POST' ) {
        //设置post
        curl_setopt( $ch, CURLOPT_POST, 1 );
        //设置post数据
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $postData );
    }

    if ($headers) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if ($cookie){
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    }

    // 抓取URL并返回
    $data = curl_exec( $ch );
    if ( !$data ) {
//        return curl_strerror(curl_errno($ch));
        return false;
    }
    // 关闭cURL资源，并且释放系统资源
    curl_close( $ch );
    return $data;
}


/**
 * @param     $data      需要处理的数据
 * @param int $precision 保留几位小数
 *
 * @return array|string
 */
function fix_number_precision( $data, $precision = 2 )
{
    if ( is_array( $data ) ) {
        foreach ( $data as $key => $value ) {
            $data[ $key ] = fix_number_precision( $value, $precision );
        }
        return $data;
    }

    if ( is_numeric( $data ) ) {
        $precision = is_float( $data ) ? $precision : 0;
        return number_format( $data, $precision, '.', '' );
    }

    return $data;
}

/**
 * 编译json
 */
function yy_json_encode( $arr )
{
    //如果是对象则转为数组
    if ( is_object( $arr ) ) {
        if ( method_exists( $arr, 'toArray' ) ) {
            $arr = $arr -> toArray();
        } else {
            $newArr = [];
            foreach ( $arr as $k => $v ) {
                $newArr[ $k ] = $v;
            }
            $arr = $newArr;
        }
    }
    //如果是索引数组
    if ( count( $arr ) == 0 || implode( ',', array_keys( $arr ) ) == implode( ',', range( 0, count( $arr ) - 1 ) ) ) {
        $isIndexes = true;
    } else {
        $isIndexes = false;
    }
    $json = "";
    if ( !is_array( $arr ) ) {
        return false;
    }
    foreach ( $arr as $k => $v ) {
        //关联数组
        if ( !$isIndexes ) {
            $json .= "\"$k\":";
        }
        $type = gettype( $v );
        if ( $type == 'array' ) {
            $json .= yy_json_encode( $v );
        } else if ( $type == 'object' ) {
            $json .= yy_json_encode( $v );
        } else if ( $type == 'string' ) {
            $v = str_replace( "\\", "\\\\", $v );
            $v = str_replace( "\"", "\\\"", $v );
            $v = str_replace( "\n", "\\n", $v );
            $v = str_replace( "\[", "\\[", $v );
            $v = str_replace( "\]", "\\]", $v );
            $v = str_replace( "\{", "\\{", $v );
            $v = str_replace( "\}", "\\}", $v );
            $v = str_replace( "\:", "\\:", $v );
            $json .= "\"{$v}\"";
        } else if ( in_array( $type, [ 'integer', 'double' ] ) ) {
            $json .= $v;
        } else if ( $type == 'boolean' ) {
            $json .= ( $v ? 'true' : 'false' );
        } else if ( $type == 'NULL' ) {
            $json .= 'null';
        }
        $json .= ',';
    }
    //删除最后一个逗号
    if ( substr( $json, -1 ) == ',' ) {
        $json = substr( $json, 0, -1 );
    }
    if ( $isIndexes ) {
        $json = "[" . $json . "]";
    } else {
        $json = "{" . $json . "}";
    }
    return $json;
}

/**
 * 数组类型转换
 *
 * @param array  $arr  数组
 * @param string $type boolean|integer|float|string|array|object|null
 */
function setArrayType( $arr, $type )
{
    if ( !is_array( $arr ) ) return null;
    foreach ( $arr as $i => $a ) {
        if ( is_array( $a ) ) {
            $a = setArrayType( $a, $type );
        } else {
            $a = settype( $a, $type );
        }
        $arr[ $i ] = $a;
    }
    return $arr;
}

/**
 * 获取图片的Base64编码(不支持url)
 *
 * @date 2017-02-20 19:41:22
 *
 * @param $img_file 传入本地图片地址
 *
 * @return string
 */
function imgToBase64( $img_file )
{

    $img_base64 = '';
    if ( file_exists( $img_file ) ) {
        $app_img_file = $img_file; // 图片路径
        $img_info = getimagesize( $app_img_file ); // 取得图片的大小，类型等

        //echo '<pre>' . print_r($img_info, true) . '</pre><br>';
        $fp = fopen( $app_img_file, "r" ); // 图片是否可读权限

        if ( $fp ) {
            $filesize = filesize( $app_img_file );
            $content = fread( $fp, $filesize );
            $file_content = chunk_split( base64_encode( $content ) ); // base64编码
            switch ( $img_info[ 2 ] ) {           //判读图片类型
                case 1:
                    $img_type = "gif";
                    break;
                case 2:
                    $img_type = "jpg";
                    break;
                case 3:
                    $img_type = "png";
                    break;
            }

            $img_base64 = 'data:image/' . $img_type . ';base64,' . $file_content;//合成图片的base64编码

        }
        fclose( $fp );
    }

    return $img_base64; //返回图片的base64
}



//无限分级
function getTree( $data, $pid = 0 )
{
    $tree = array();
    foreach ( $data as $k => $v ) {
        if ( $v[ "pid" ] == $pid ) {
            unset( $data[ $k ] );
            if ( !empty( $data ) ) {
                $children = getTree( $data, $v[ "value" ] );
                if ( !empty( $children ) ) {
                    $v[ "children" ] = $children;
                }
            }
            $tree[] = $v;
        }
    }
    return $tree;
}


// 根据子类id 找所有父类 $isId 为true 返回 父类id
function getTreeParent( $data, $son_id, $level = 0, $isClear = true,$isId = false )
{
    //声明一个静态数组存储结果
    static $res = array();
    //刚进入函数要清除上次调用此函数后留下的静态变量的值，进入深一层循环时则不要清除
    if ( $isClear == true ) $res = array();
    foreach ( $data as $v ) {
        if ( $v[ 'id' ] == $son_id ) {
            $v[ 'level' ] = $level;
            if ($isId) {
                $res[] = $v['id'];
            } else {
                $res[] = $v;
            }

            getTreeParent( $data, $v[ 'pid' ], $level - 1, $isClear = false ,$isId );
        }
    }
    return $res;
}




// 根据父类id找所有子类
function getTreeSon( $data, $p_id = 0, $level = 0, $isClear = true )
{
    //声明一个静态数组存储结果
    static $res = array();
    //刚进入函数要清除上次调用此函数后留下的静态变量的值，进入深一层循环时则不要清除
    if ( $isClear == true ) $res = array();
    foreach ( $data as $v ) {
        if ( $v[ 'pid' ] == $p_id ) {
            $v[ 'level' ] = $level;
            $res[] = $v;
            getTreeSon( $data, $v[ 'id' ], $level + 1, $isClear = false );
        }
    }
    return $res;
}





/**
 * 数组转小写
 */
function arrtolower( $arr ){
    foreach( $arr as $i => $v ){
        if( is_string($v) ){
            $arr[$i] = strtolower($v);
        }
        if( is_array($v) ){
            $arr[$i] = arrtolower($v);
        }
    }
    return $arr;
}

function pwdHash( $length = 6 )
{
    // 密码字符集，可任意添加你需要的字符
    $chars = array(
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
        'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's',
        't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D',
        'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O',
        'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
    );
    $pwd_hash = '';
    for ( $i = 0; $i < $length; $i++ ) {
        // 将 $length 个数组元素连接成字符串
        $pwd_hash .= $chars[ array_rand( $chars ) ];
    }
    return $pwd_hash;
}


/**
 * 返回26个英文字母
 * @param bool $capital 是否大写
 */
function getLetterList($capital=false){
    $letterList = [];
    for($i=65;$i<91;$i++){
        if($capital){
            $letterList[] = chr($i); //输出大写字母
        }else{
            $letterList[] = strtoupper(chr($i)); //输出小写字母
        }
    }
    return $letterList;
}

/**
 * 数组替换
 * @param $result 要替换的数组
 * @param null $search 查找值
 * @param string $replace 替换值
 */
function arrayConvertNull(&$result , $search = null ,$replace = '')
{
    foreach ($result as $k => &$item) {
        if ($item === $search) {
            $result[$k] = $replace;
        } elseif (is_array($item)){
            arrayConvertNull($item ,$search ,$replace);

        }
    }
}
/**
 *数字金额转换成中文大写金额的函数
 *String Int $num 要转换的小写数字或小写字符串
 *return 大写字母
 *小数位为两位
 **/
function num_to_rmb($num){
    $_num = $num;
    $c1 = "零壹贰叁肆伍陆柒捌玖";
    $c2 = "分角元拾佰仟万拾佰仟亿";
    //精确到分后面就不要了，所以只留两个小数位
    $num = round($num, 2);
    //将数字转化为整数
    $num = $num * 100;
    if (strlen($num) > 10) {
        return "金额太大，请检查";
    }
    $i = 0;
    $c = "";
    while (1) {
        if ($i == 0) {
            //获取最后一位数字
            $n = substr($num, strlen($num)-1, 1);
        } else {
            $n = $num % 10;
        }
        //每次将最后一位数字转化为中文
        $p1 = substr($c1, 3 * $n, 3);
        $p2 = substr($c2, 3 * $i, 3);
        if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
            $c = $p1 . $p2 . $c;
        } else {
            $c = $p1 . $c;
        }
        $i = $i + 1;
        //去掉数字最后一位了
        $num = $num / 10;
        $num = (int)$num;
        //结束循环
        if ($num == 0) {
            break;
        }
    }
    $j = 0;
    $slen = strlen($c);
    while ($j < $slen) {
        //utf8一个汉字相当3个字符
        $m = substr($c, $j, 6);
        //处理数字中很多0的情况,每次循环去掉一个汉字“零”
        if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
            $left = substr($c, 0, $j);
            $right = substr($c, $j + 3);
            $c = $left . $right;
            $j = $j-3;
            $slen = $slen-3;
        }
        $j = $j + 3;
    }
    //这个是为了去掉类似23.0中最后一个“零”字
    if (substr($c, strlen($c)-3, 3) == '零') {
        $c = substr($c, 0, strlen($c)-3);
    }
    //将处理的汉字加上“整”
    if (empty($c)) {
        return "零元整";
    }else{
        return $c . (strpos($_num , '.') ? '': "整");
    }
}

/**
 * 两点间距离
 * @param $lat1
 * @param $lng1
 * @param $lat2
 * @param $lng2
 * @return float
 */
function getDistance($lat1, $lng1, $lat2, $lng2) {
    //地球半径
    $R = 6378137;
    //将角度转为狐度
    $radLat1 = deg2rad($lat1);
    $radLat2 = deg2rad($lat2);
    $radLng1 = deg2rad($lng1);
    $radLng2 = deg2rad($lng2);
    //结果
    $s = acos(cos($radLat1)*cos($radLat2)*cos($radLng1-$radLng2)+sin($radLat1)*sin($radLat2))*$R;
    //精度
    $s = round($s* 10000)/10000;
    return  round($s);
}


/**
 * @desc 根据两点间的经纬度计算距离
 * @param float $latitude 纬度值
 * @param float $longitude 经度值
 */
function getDistance1($latitude1, $longitude1, $latitude2, $longitude2)
{
    $earth_radius = 6371000; //approximate radius of earth in meters

    $dLat = deg2rad($latitude2 - $latitude1);
    $dLon = deg2rad($longitude2 - $longitude1);
    /*
    Using the
    Haversine formula

    http://en.wikipedia.org/wiki/Haversine_formula
    http://www.codecodex.com/wiki/Calculate_Distance_Between_Two_Points_on_a_Globe
    验证：百度地图 http://developer.baidu.com/map/jsdemo.htm#a6_1
    calculate the distance
    */
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * asin(sqrt($a));
    $d = $earth_radius * $c;

    return round($d); //四舍五入
}
/**
 * 输出
 * @param mixed ...$var
 */
function p (...$var){
    $var =  count($var) == 1 ? $var[0] : $var;
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
}

function pe (...$var){
    $var =  count($var) == 1 ? $var[0] : $var;
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
    exit;
}