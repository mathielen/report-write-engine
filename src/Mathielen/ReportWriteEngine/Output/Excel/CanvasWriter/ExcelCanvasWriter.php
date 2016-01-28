<?php
namespace Mathielen\ReportWriteEngine\Output\Excel\CanvasWriter;

use Mathielen\ReportWriteEngine\Engine\Canvas;
use Mathielen\ReportWriteEngine\Engine\CanvasWriter\CanvasWriterInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ExcelCanvasWriter implements CanvasWriterInterface
{

    /**
     * @var \PHPExcel
     */
    private $output;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \PHPExcel_Worksheet
     */
    private $templateSheet;

    public function __construct(
        \PHPExcel $template,
        LoggerInterface $logger = null)
    {
        $this->logger = $logger ? $logger : new NullLogger();

        $templateSheet = $template->getSheetByName('TEMPLATE');
        if (!$templateSheet) {
            throw new \InvalidArgumentException("A sheet named 'TEMPLATE' must exist.");
        }

        $this->prepare($templateSheet);
    }

    private static function excelCoordinate($rowNum, $colNum)
    {
        return \PHPExcel_Cell::stringFromColumnIndex($colNum) . ($rowNum+1); //A1...
    }

    public function write(Canvas $canvas)
    {
        $this->output->setActiveSheetIndex(0);

        foreach ($canvas as $rowNum => $row) {
            foreach ($row as $colNum => $cell) {
                //even if cell value is empty, write the style

                $excelCor = self::excelCoordinate($rowNum, $colNum);
                $excelCell = $this->output->getActiveSheet()->getCell($excelCor);

                //$cellValue = FormulaHelper::processFormula($cellValue, $cellValue, $this->getCurrentRowNum(), $rangeRowNum);

                //set value
                $excelCell->setValue($cell['value']);

                //set style
                $excelCell->setXfIndex($cell['style']);
            }
        }

        $this->clean();

        return $this->output;
    }

    protected function prepare(\PHPExcel_Worksheet $templateSheet)
    {
        $this->output = new \PHPExcel();
        $outputSheet = $this->output->getActiveSheet();
        $outputSheet->setTitle('Report');
        $this->templateSheet = $this->output->addExternalSheet($templateSheet);

        foreach ($this->templateSheet->getColumnDimensions() as $col => $columnDimension) {
            $outputSheet->getColumnDimension($col)->setWidth($columnDimension->getWidth());
        }
    }

    public function getTemplateSheet()
    {
        return $this->templateSheet;
    }

    protected function clean()
    {
        $this->output->setActiveSheetIndexByName('TEMPLATE');
        $this->output->removeSheetByIndex($this->output->getActiveSheetIndex());
    }

}
