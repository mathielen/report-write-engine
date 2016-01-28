<?php

class VerticalRangeWriterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PHPExcelReport\Report\Writer\VerticalRangeWriter
     */
    private $sut;

    /**
     * @var \PHPExcel
     */
    private $output;

    protected function setUp()
    {
        $logger = new \Monolog\Logger('rendertest');
        $logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout', \Monolog\Logger::DEBUG));

        $inputFileType = \PHPExcel_IOFactory::identify(__DIR__ . '/../metadata/template/singlelevel.xlsx');
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
        $template = $objReader->load(__DIR__ . '/../metadata/template/singlelevel.xlsx');

        $this->output = new \PHPExcel();
        $outputSheet = $this->output->getActiveSheet();
        $templateSheet = $this->output->addExternalSheet($template->getSheetByName('TEMPLATE'));

        $namedRange = $this->output->getNamedRange('ROOT');
        $rangeData = $templateSheet->rangeToArray($namedRange->getRange(), null, false, true, true);
        print_R($rangeData);
        $namedRange = $this->output->getNamedRange('LEVEL1');
        $rangeData = $templateSheet->rangeToArray($namedRange->getRange(), null, false, true, true);
        print_R($rangeData);
die();
        $reportSheet = new \PHPExcelReport\Report\ReportSheet($outputSheet);

        $this->sut = new \PHPExcelReport\Report\Writer\VerticalRangeWriter($templateSheet, $reportSheet, $logger);
    }

    public function testSimple()
    {
        $data = [
            'ROOT' => [
                'LEVEL1' => [
                    ['LEVEL1_CAPTION' => '1'],
                    ['LEVEL1_CAPTION' => '2']
                ]
            ]
        ];

        $namedRange = $this->output->getNamedRange('ROOT');

        $this->sut->write($data, $namedRange);

        $this->output->setActiveSheetIndexByName('TEMPLATE');
        $this->output->removeSheetByIndex($this->output->getActiveSheetIndex());
        $excelWriter = \PHPExcel_IOFactory::createWriter($this->output, 'Excel2007');
        $excelWriter->save(__DIR__ . '/../metadata/output/verticalrangewritertest.xlsx');
    }

}
