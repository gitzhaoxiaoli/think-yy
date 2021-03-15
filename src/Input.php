<?php
namespace yy;
class Input implements \ArrayAccess,\IteratorAggregate
{
    public $param = [];
    function __construct()
    {
        $this -> param = $this -> myInput();
    }
	

    public static function data($key = false)
    {
        $post = new self();
        if ($key) {
            return $post -> param[$key] ?? null;
        }
        return $post;
    }

    /**
     * 设置筛选的字段
     * @param string|array $onlys 筛查的字段
     */
    public function only( $onlys = '' ){
        if( is_string($onlys) ){
            $onlys = preg_replace('/\s+/', '', $onlys);
            $onlys = explode(',',$onlys);
        }
        foreach( $this -> param as $field => $param ){
            if( !in_array($field,$onlys) ){
                unset($this -> param[$field]);
            }
        }
        return $this;
    }

    /**
     * 返回数组
     */
    public function toArray(){
        return $this -> param;
    }


    private function myInput($key = false)
    {
        $param = [];

        //input传进来的
        $input = file_get_contents('php://input', 'r');
        $input = json_decode($input, true);

        //get和post传进来的
        $getAndPost = input();
        if (isset($getAndPost['param'])) {
            unset($getAndPost['param']);
        }


        //get和post传进来的json
        $json = input('param');
        if ($json) {
            $json = json_decode($json, true);
        }

        if (isset($input) && $input && is_array($input)) {
            $param = array_merge($param, $input);
        } else if (isset($getAndPost) && $getAndPost && is_array($getAndPost)) {
            $param = array_merge($param, $getAndPost);
        } else if (isset($json) && $json && is_array($json)) {
            $param = array_merge($param, $json);
        }

        $param = $this->clearSpace($param);
        // var_dump($param);
        if ($key) {
            return $param[$key] ?? null;
        }
        return $param;
    }

    /**
     * 遍历去首尾空格
     */
    private function clearSpace($params)
    {
        if (is_array($params)) {
            foreach ($params as $k => $p) {
                if (is_string($p)) {
                    $params[$k] = trim($p);
                    if( $p === '{}' || $k === '{}' ){
                        unset($params[$k]);		//删除空字段
                    }
                    /*if( !$params[$k] ){
                        unset($params[$k]);		//删除空字段
                    }*/
                } else if (is_array($p)) {
                    $params[$k] = $this->clearSpace($p);
                }
            }
        } else if (is_string($params)) {
            $params = trim($params);
        }
        return $params;
    }



    public function getIterator(){
        return new \ArrayIterator($this->param);
    }


    public function offsetExists($key)
    {
        //return isset($this->param[$key]);
        return true;
    }

    public function offsetSet($key, $value)
    {
        $this->param[$key] = $value;
    }

    public function offsetGet($key)
    {
        return $this->param[$key] ?? null;
    }

    public function offsetUnset($key)
    {
        unset($this->param[$key]);
    }
}