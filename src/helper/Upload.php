<?php


namespace yy\helper;

class Upload
{
    protected $fileSize = 1024 * 1024 * 50;
    protected $fileExt = ['jpg', 'jpeg', 'png','gif','docx','doc','xlsx','xls','zip','rar','7z' ,'mp4','mp3','wma','wmv','mov','wav'];
    protected $fileName = 'image';
    protected $saveName = [];
    protected $diskName = 'local';
    protected $putPath = '';
    protected $errMsg = '';

    /**
     * 上传文件
     * @param string $fileName 上传文件名
     * @param string $diskName 磁盘配制名称
     * @param string $putPath 保存文件夹
     * @param string $size 大小
     * @param string $ext 扩展名
     * @return array|mixed
     */
    public function upFile($fileName = 'file' , $diskName = '' , $putPath = '' , $size = '' , $ext = '')
    {
        $this->fileName = $fileName;
        if ($diskName){
            $this->diskName = $diskName;
        }
        if ($putPath){
            $this->putPath = $putPath;
        }
        if ($size){
            $this->fileSize = $size;
        }
        if ($ext){
            $this->fileExt = $ext;
        }
        $this->_upload();
        if ($this->errMsg){
            return $this->_error();
        }

        return $this->saveName[0];
    }


    /**
     * 上传图片
     * @param string $fileName 上传文件名
     * @param string $diskName 磁盘配制名称
     * @param string $putPath 保存文件夹
     * @param bool $compress 是否压缩
     * @param string $size 大小
     * @param string $ext 扩展名
     * @return array|mixed
     */
    public function upImage($fileName = 'pic' , $diskName = '' , $putPath = '' , $compress = true , $size = '' , $ext = '')
    {
        $this->fileName = $fileName;
        if ($diskName){
            $this->diskName = $diskName;
        }
        if ($putPath){
            $this->putPath = $putPath;
        }
        if ($size){
            $this->fileSize = $size;
        }
        $ext = $ext ?: ['jpg', 'jpeg', 'png','gif'];
        if ($ext){
            $this->fileExt = $ext;
        }
        $this->_upload();
        if ($this->errMsg){
            return $this->_error();
        }
        if ($compress !== true)
            return $this->saveName[0];

        return $this->imageThumb($this->saveName[0]);
    }

    /**
     * 压缩图片
     * @param $path
     * @return mixed
     */
    public function imageThumb($path)
    {
        $saveName = $path;
        $end = strrchr( $saveName , ".");
        $new_path = str_replace($end , "_thumb" . $end , $saveName);

        $image = \think\Image::open('.' .$saveName);
        $image->thumb(750, 750)->save('.' .$new_path);

        return $new_path;
    }

    /**
     * 文件上传
     * @return string
     */
    protected function _upload(){
        // 获取表单上传文件
        $files = request()->file();
        if (empty($files))return $this->errMsg = '请选择上传文件';
        // 上传验证
        $rule = [
            $this->fileName => [
                'filesize' => $this->fileSize ,
                'fileExt' => $this->fileExt
            ]
        ];
        $message = [
            $this->fileName => [
                'filesize' => '文件大小不符合' ,
                'fileExt' => '文件类型不符合'
            ]
        ];
        try {
            // 上传验证
            validate($rule  , $message)
                ->check($files);
            // 计算存储路径
            if (empty($this->putPath)){
                $this->putPath = $this->fileName;
            }
            // 计算访问路径
            if ($this->diskName == 'local') {
                $prePath = '/runtime/storage/';
            } else {
                $prePath = \think\facade\Filesystem::getDiskConfig($this->diskName,'url') . '/';
            }
            // 文件上传
            foreach ($files as $file) {

                $saveName = $prePath .\think\facade\Filesystem::disk($this->diskName)->putFile($this->putPath, $file);
                $this->saveName[] = str_replace("\\", '/', $saveName);
            }
        } catch (\think\exception\ValidateException $e) {
            $this->errMsg = $e->getMessage();
        }
    }

    /**
     * 返回错误信息
     * @return array
     */
    protected function _error()
    {
        return [
            'error' => 1,
            'msg' => $this->errMsg
        ];

    }


}