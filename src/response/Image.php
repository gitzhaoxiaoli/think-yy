<?php


namespace yy\response;


use think\Cookie;
use think\Response;

class Image extends Response
{
    /**
     * 当前contentType
     * @var string
     */
    protected $contentType = 'image/png';

    public function __construct($data = '', int $code = 200)
    {
        $this->data = $data;
        $this->code = $code;
        $this->header['Content-Type'] = $this->contentType;
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
            return $data;
        } catch (\Exception $e) {
            if ($e->getPrevious()) {
                throw $e->getPrevious();
            }
            throw $e;
        }
    }

}
