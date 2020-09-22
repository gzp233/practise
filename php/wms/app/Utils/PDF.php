<?php

namespace App\Utils;

class PDF extends \TCPDF
{
    public $POcode = '';
    //Page header
    public function Header()
    {
        // LOGO
        $image_file = public_path() . '/image/logo_red.png';
        $this->Image($image_file, 45, 6, 57, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('stsongstdlight', '', 20);
        // Title
        $this->Cell(50, 20, "交 货 单", 0, false, 'R', 0, '', 0, false, 'M', 'B');

        $this->SetFont('stsongstdlight', '', 9);
        $this->Cell(110, 10, "作成时间：           " . date('Y-m-d'), 0, false, 'R', 0, '', 0, false, 'M', 'B');
    }

    // Page footer
    public function Footer()
    {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('stsongstdlight', '', 8);
        // 设置二维码
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $this->Cell(15, 10, '创建人：世源供应链', 0, 0, '', 0, '', 0, false, 'T', '');
        $this->Cell(0, 10, '第 ' . $this->getAliasNumPage() . ' 页 ，共 ' . $this->getAliasNbPages() . '页', 0, false, 'R', 0, '', 0, false, 'T', '');
        
        // 生成二维码
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false,
            'text' => false,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 4
        );
        $this->write1DBarcode($this->POcode, 'C128', 13, 187, 50, 11, 0.4, $style, 'T');
    }

    public function getTable($data)
    {
        $header = ['序号', '商品代码', '旧产品代码', '产品名称', '数量', '零售单价', '批发单价', '批发金额', '商品单价', '商品金额', '增值税额'];
        // Colors, line width and bold font
        $this->SetLineWidth(0.3);
        $this->setCellMargins(0, 0, 0, 0);
        // Header
        $w = array(15, 20, 20, 60, 20, 20, 20, 25, 25, 25, 25);
        $num_headers = count($header);
        for ($i = 0; $i < $num_headers; $i++) {
            $this->Cell($w[$i], 6, $header[$i], 1, 0, 'C', 0);
        }
        $this->Ln();
        $this->SetFont('');
        // Data
        $pageLimit = 21;
        $pageStart = 12;
        $count = 0;
        foreach ($data as $row) {
            // 判断该行的高度
            $len = mb_strlen($row[3], 'UTF-8');
            $heightCount = (int)ceil($len / 20);
            $count += $heightCount;
            foreach ($row as $index => $value) {
                if ($index == 3) {
                    $str = '';
                    for ($i = 0; $i < $heightCount; $i++) {
                        if ($i != 0) $str .= "\n";
                        $str .= mb_substr($value, $i * 27, 27, 'UTF-8');
                    }
                    $value = $str;
                    $value = str_replace("\n", "", $value);
                    $value = str_replace(" ", '&nbsp;', $value);
                }
                if ($heightCount == 1) {
                    $cellHight = 6;
                } else {
                    $cellHight = $heightCount * 7;
                }
                if ($index == 3) {
                    $this->MultiCell($w[$index], $cellHight, $value, 1, '', 0, 0, '', '', 1, 0, 1);
                } else {
                    $this->MultiCell($w[$index], $cellHight, $value, 1, 'C', 0, 0);
                }
            }
            if ($count >= $pageStart) {
                $count = 0;
                $pageStart = $pageLimit;
                $this->AddPage();
            }
            $this->Ln();
        }
        // dd(1);
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}