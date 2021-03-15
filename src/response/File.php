<?php

namespace yy\response;

use think\Response;

class File extends Response
{


    function __construct($data = false, $header = false)
    {


        if ($data) $this->data = $data;
        if ($header) $this->header = $header;

    }

    public function downloadExcel( $fileDir , $filename ){
        $this -> data = file_get_contents($fileDir);
        $filename = urlencode($filename);
        $this->header['Content-Type']              = 'application/vnd.ms-excel';
        $this->header['Content-Disposition']       = 'attachment; filename="'.$filename.'"';
        $this->header['Content-Length']            = strlen($this -> data);
        $this->header['Content-Transfer-Encoding'] = 'binary';
        return $this;
    }

    public function send(): void
    {
        // 发送头部信息
        foreach ($this->header as $name => $val) {
            header($name . (!is_null($val) ? ':' . $val : ''));
        }
        echo $this->data;
    }
}