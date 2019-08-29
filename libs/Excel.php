<?php
// +------------------------------------------------+
// | http://www.cjango.com                          |
// +------------------------------------------------+
// | 修复BUG不是一朝一夕的事情，等我喝醉了再说吧！  |
// +------------------------------------------------+
// | Author: 小陈叔叔 <Jason.Chen>                  |
// +------------------------------------------------+
namespace tools;

/**
 * Excel 读取生成工具类
 */
class Excel
{

    /**
     * 读取Excel
     */
    public static function reader($file)
    {
        import("tools.excel.PHPExcel");
        if (self::_getExt($file) == 'xls') {
            import("tools.excel.PHPExcel.Reader.Excel5");
            $PHPReader = new \PHPExcel_Reader_Excel5();
        } elseif (self::_getExt($file) == 'xlsx') {
            import("tools.excel.PHPExcel.Reader.Excel2007");
            $PHPReader = new \PHPExcel_Reader_Excel2007();
        } else {
            return false;
        }

        $PHPExcel     = $PHPReader->load($file);
        $currentSheet = $PHPExcel->getSheet(0);
        $allColumn    = $currentSheet->getHighestColumn();
        $allRow       = $currentSheet->getHighestRow();
        for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
            for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
                $address                          = $currentColumn . $currentRow;
                $arr[$currentRow][$currentColumn] = $currentSheet->getCell($address)->getValue();
            }
        }
        return $arr;
    }

    /**
     * 获取文件后缀名
     */
    private static function _getExt($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION);
    }

    /**
     * [writer description]
     * @param  [type]  $header 表头信息
     * @param  [type]  $data   表数据
     * @param  boolean $type   是否输出 true 直接下载 false 保存文件
     */
    public static function writer($header, $data, $fileName = '交通工程', $output = false)
    {
        import("tools.excel.PHPExcel");
        import("tools.excel.PHPExcel.Writer.Excel5");
        import("tools.excel.PHPExcel.IOFactory.php");
        $objPHPExcel = new \PHPExcel();
        $objProps    = $objPHPExcel->getProperties();
        //设置表头
        $key = ord("A");
        foreach ($header as $v) {
            $colum = chr($key);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
            $key += 1;
        }
        $column      = 2;
        $objActSheet = $objPHPExcel->getActiveSheet();
        foreach ($data as $key => $rows) {
            //行写入
            $span = ord("A");
            foreach ($rows as $keyName => $value) {
                // 列写入
                $j = chr($span);
                $objActSheet->setCellValue($j . $column, $value);
                $span++;
            }
            $column++;
        }
        // $objPHPExcel->getActiveSheet()->setTitle('cjango.data');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        if ($output) {
            $fileName = iconv("utf-8", "gb2312", $fileName);
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename=\"$saveName\"");
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output');
        } else {
            $fileName = iconv("utf-8", "gb2312", $fileName);
            $objWriter->save($fileName);
            return $fileName;
        }
    }
}
