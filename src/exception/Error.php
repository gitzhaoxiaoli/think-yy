<?php

//错误类
namespace yy\exception;


use Throwable;

class Error extends \Exception
{
    /**
     * 大概对应http状态码
     * 0:其他错误
     * 200：执行成功
     * 201-299：用户/登录/权限/验证等错误
     * 300-399：
     * 400-499：参数/接口调用等错误
     * 500-599：接口/服务器/数据库等错误
     */
    public static $errCode = [
        0 => '其他错误',
        200 => '执行成功',

        400 => '参数错误',
        404 => '接口不存在',

        201 => '登录超时',
        202 => '用户名或密码错误',
        203 => '验证码错误',
        204 => '没有权限访问此操作',
        205 => '名称重复',
        206 => '手机号重复',


        220 => '文件类型不符',
        221 => '文件大小不符',


        500 => '服务器接口错误',
        501 => '数据关联，不能操作',
        502 => '门店信息与当前人不符',
        503 => '超过最大限制数',
        504 => '数据格式错误',


        //微信相关
        591 => '微信授权接口错误',
        592 => '公众号接口错误',
        593 => '微信支付接口错误',
        594 => 'code失效',
        595 => 'accessToken错误',
        596 => '注册jssdk错误',
    ];

    function __construct( $code = 200, $message = "", $previous = null )
    {
        if (
            !$message && !empty( self ::$errCode[ $code ] )
        ) {
            $message = self ::$errCode[ $code ];
        }
        //存日志
        $this -> writeLog( $code, $message, $this -> getFile(), $this -> getLine() );
        parent ::__construct( $message, $code, $previous );

    }

    public function writeLog( $code, $message, $file, $line )
    {
        $date = date( 'Y-m-d H:i:s' );
        $url = request() -> url( true );

        $log = "\n";
        $log .= "=========================================================\n";
        $log .= "[{$date}] {$url}\n";
        $log .= "[{$code}] {$message}\n";
        $log .= "文件：{$file} 行：{$line}\n";
        $log .= "=========================================================\n";
        trace( $log, "error" );
    }
}