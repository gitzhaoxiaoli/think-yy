<?php
//数学方法
namespace yy\helper;


class Math
{
    /**
     * 判断自然数
     * @param $var
     * @return bool
     */
    public static function isNatural( $var )
    {
        //转为整数
        $var2 = (int)$var;
        if( $var2 < 0 ) return false;
        if( $var != $var2 ) return false;
        return true;
    }

    /**
     * 判断正数
     */
    public static function isPositive( $var ){
        //转为小数
        $var2 = (double)$var;
        if( $var2!=$var || $var2<0){
            return false;
        }
        return true;
    }

    /**
     * 返回小数位数
     */
    public static function getFloatDigit( $var ){
        $varStr = self::convertFloat($var);
        $varArr = explode('.',$varStr);
        if( count($varArr) == 1 ){
            return 0;
        }
        return strlen($varArr[1]);
    }


    /**
     * 浮点型转换为纯字符串类型
     */
    public static function convertFloat($floatAsString)
    {
        $norm = strval(floatval($floatAsString));

        if (($e = strrchr($norm,'E')) === false) {
            return $norm;
        }

        return number_format($norm,-intval(substr($e,1)));
    }

}