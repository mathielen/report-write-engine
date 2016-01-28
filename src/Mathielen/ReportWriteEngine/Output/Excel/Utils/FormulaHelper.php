<?php
namespace Mathielen\ReportWriteEngine\Utils;

class FormulaHelper
{

    public static function processFormula($cellValue, $rangeCellValue, $currentRowNum, $rangeRowNum)
    {
        if (substr($rangeCellValue, 0, 1) == '=') {
            $rowDelta = $currentRowNum + $rangeRowNum;
            //has ref to field - add row-offset
            $cellValue = preg_replace_callback(
                '/([A-Z]+)([0-9])+/',
                function ($matches) use ($rowDelta) {
                    $offsettedY = ($matches[2] + $rowDelta);

                    return $matches[1] . $offsettedY;
                },
                $cellValue);
        }

        return $cellValue;
    }
}
