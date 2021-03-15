<?php


namespace yy\response;


use think\Cookie;
use think\Response;

class Json extends Response
{
    // 输出参数
    //protected $options = [
    //    'json_encode_param' => JSON_UNESCAPED_UNICODE,
    //];

    protected $contentType = 'application/json';

    public function __construct(Cookie $cookie,$data = '', int $code = 200)
    {
        $this->init($data, $code);
        $this->cookie = $cookie;
    }

    /**
     * 处理数据
     * @access protected
     * @param  mixed $data 要处理的数据
     * @return string
     * @throws \Exception
     */
    protected function output($data): string
    {
        try {
            $data = yy_json_encode($data);
            return $data;
        } catch (\Exception $e) {
            if ($e->getPrevious()) {
                throw $e->getPrevious();
            }
            throw $e;
        }
    }

}
