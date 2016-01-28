<?php
namespace Mathielen\ReportWriteEngine\Output\Excel\Persistence;

interface ExcelRepositoryInterface
{

    /**
     * @return \PHPExcel
     */
    public function get($id);

    /**
     * @return string the resulting filepath
     */
    public function save(\PHPExcel $excel, $id);

}
