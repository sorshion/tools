<?php

namespace app\common\library;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/**
 * Excel处理
 */
class Excel
{
    /**
     * excel表格导出
     * @param string $fileName 文件名称 $name='测试导出';
     * @param array $headArr 表头名称 $header=['表头A','表头B'];
     * @param array $data 要导出的数据 $data=[['测试','测试'],['测试','测试']]
     * @param bool $auto 是否开启根据表头自适应宽度 默认开启
     */
    public static function excelExport($fileName = '', $headArr = [], $data = [], $auto = true)
    {
        $fileName .= '-' . time() . ".xlsx";
        $objPHPExcel = new Spreadsheet();
        $objPHPExcel->getProperties();
        $key = ord("A"); // 设置表头
        $key2 = ord("@"); // 超过26列会报错的解决方案

        // 居中
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // 设置表头
        foreach ($headArr as $v) {
            // 超过26列会报错的解决方案
            if ($key > ord("Z")) {
                $key2 += 1;
                $key = ord("A");
                $colum = chr($key2) . chr($key); //超过26个字母时才会启用
            } else {
                if ($key2 >= ord("A")) {
                    $colum = chr($key2) . chr($key);
                } else {
                    $colum = chr($key);
                }
            }
            // 写入表头
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
            // 自适应宽度
            if ($auto) {
                $len = strlen(iconv('utf-8', 'gbk', $v));
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum)->setWidth($len + 5);
            }
            $key += 1;
        }

        $column = 2;
        $objActSheet = $objPHPExcel->getActiveSheet();
        // 写入行数据
        foreach ($data as $key => $rows) {
            $span = ord("A");
            $span2 = ord("@");
            // 按列写入
            foreach ($rows as $keyName => $value) {
                // 超过26列会报错的解决方案
                if ($span > ord("Z")) {
                    $span2 += 1;
                    $span = ord("A");
                    $tmpSpan = chr($span2) . chr($span); //超过26个字母时才会启用
                } else {
                    if ($span2 >= ord("A")) {
                        $tmpSpan = chr($span2) . chr($span);
                    } else {
                        $tmpSpan = chr($span);
                    }
                }
                // 写入数据
                $objActSheet->setCellValue($tmpSpan . $column, $value);
                $span++;
            }
            $column++;
        }

        // 自动加边框
        // $styleThinBlackBorderOutline = array(
        //     'borders' => array(
        //         'allborders' => array( //设置全部边框
        //             'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN //粗的是thick
        //         ),

        //     ),
        // );
        // $objPHPExcel->getActiveSheet()->getStyle('A1:' . $colum . --$column)->applyFromArray($styleThinBlackBorderOutline);
        // 重命名表
        $fileName = iconv("utf-8", "gbk", $fileName);
        // 设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        ob_end_clean();
        ob_start();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=$fileName");
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($objPHPExcel);
        $writer->save('php://output'); // 文件通过浏览器下载
        exit();
    }
}
