<?php
namespace Mathielen\ReportWriteEngine\Output\Excel\Template;

use Mathielen\ReportWriteEngine\Engine\Template\TemplatePopulatorInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ExcelNamedRangeTemplatePopulator implements TemplatePopulatorInterface
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger ? $logger : new NullLogger();
    }

    public function populate($templateSheet)
    {
        return $this->populateNamedRanges($templateSheet);
    }

    private function populateNamedRanges(\PHPExcel_Worksheet $templateSheet)
    {
        $template = $templateSheet->getParent();

        $excelNamedRanges = array_change_key_case($template->getNamedRanges(), CASE_UPPER);
        if (!isset($excelNamedRanges['ROOT'])) {
            throw new \InvalidArgumentException("Missing Named-Range: 'ROOT'");
        }

        $namedRanges = [];

        /** @var \PHPExcel_NamedRange $excelNamedRange */
        foreach ($excelNamedRanges as $name => $excelNamedRange) {
            $namedRanges[$name] = [];

            foreach ($templateSheet->rangeToArray($excelNamedRange->getRange(), null, false, true, true) as $rowNum => $row) {
                $namedRanges[$name][$rowNum - 1] = [];

                foreach ($row as $col => $cellValue) {
                    $colNum = \PHPExcel_Cell::columnIndexFromString($col);

                    $templateCor = $col.$rowNum;
                    $style = $this->getStyleOfCell($templateSheet, $templateCor);

                    $namedRanges[$name][$rowNum - 1][$colNum - 1] = [
                        'value' => $cellValue,
                        'style' => $style
                    ];
                }
            }
        }

        $this->logger->debug('Populated named ranges', array_keys($namedRanges));

        return $namedRanges;
    }

    private function getStyleOfCell(\PHPExcel_Worksheet $templateSheet, $templateCor)
    {
        $style = $templateSheet->getStyle($templateCor);
        $style = $style->getIsSupervisor() ? $style->getSharedComponent() : $style;

        return $style->getIndex();
    }

}
