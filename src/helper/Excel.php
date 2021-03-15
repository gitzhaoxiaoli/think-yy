<?php


namespace yy\helper;


use PhpOffice\PhpSpreadsheet\IOFactory;
use yy\exception\Error;

/**
 * excel 处理类
 * Class Excel
 * @package yy\helper
 */
class Excel
{

    public static function init(){
        return new self();
    }

    /**
     * 读取excel返回数组
     * @param $filename
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function readToArray($filename)
    {
        $fileExtendName = substr(strrchr($filename, '.'), 1);
        if (!in_array($fileExtendName , ['xls','xlsx']))throw new Error( 400 ,'必须为excel表格，且必须为xls或xlsx格式！' );
        $reader = IOFactory::createReader(ucfirst($fileExtendName));
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load(app()->getRootPath() . $filename);

        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        return $sheetData;
    }

    /**
     * 导出excel
     * @param $data 导出数据
     * @param $downname 文件名称
     * @param string $filename 保存到磁盘上的路径，空就是输出到浏览器
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportXlsx($data ,$downname,$filename = '')
    {
        $downname = iconv("UTF-8","gbk//TRANSLIT" , $downname) .time() . '.xlsx';
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getActiveSheet()->fromArray($data);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        if (!empty($filename)){
            $writer->save($filename);
            return;
        }
        header('Content-Description: File Transfer');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $downname);
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

    /**
     * 这个函数用不了
     * @param $data
     * @param string $filename
     */
    public function exportCsv($data ,$filename = '')
    {
        // halt($data);
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getActiveSheet()->fromArray($data);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        header('Content-Description: File Transfer');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=test.csv');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        $fp = fopen('php://output', 'a');//打开output流
        mb_convert_variables('GBK', 'UTF-8', $columns);
        fputcsv($fp, $columns);//将数据格式化为csv格式并写入到output流中
        $dataNum = count( $data );
        $perSize = 1000;//每次导出的条数
        $pages = ceil($dataNum / $perSize);

        for ($i = 1; $i <= $pages; $i++) {
            foreach ($data as $item) {
                mb_convert_variables('GBK', 'UTF-8', $item);
                fputcsv($fp, $item);
            }
            //刷新输出缓冲到浏览器
            ob_flush();
            flush();//必须同时使用 ob_flush() 和flush() 函数来刷新输出缓冲。
        }
        fclose($fp);
        exit();
    }

    /**
     * excel 读取时间数字转时间
     * @param $number
     * @param bool $timestamp
     * @return false|float|int|string
     */
    public function numberToDate($number , $timestamp = true)
    {
        $second = 24 * 3600 * ($number - 25569);
        if ($timestamp)return $second;
        return date("Y-m-d" , $second);

    }

}