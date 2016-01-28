<?php
namespace Mathielen\ReportWriteEngine;

use Mathielen\ReportWriteEngine\Output\Excel\Persistence\ExcelFileRepository;
use Mathielen\ReportWriteEngine\Engine\Renderer\HorizontalRenderer;
use Mathielen\ReportWriteEngine\Engine\Renderer\RendererInterface;
use Mathielen\ReportWriteEngine\Engine\Renderer\VerticalRenderer;
use Mathielen\ReportWriteEngine\Engine\RendererRepository;
use Mathielen\ReportWriteEngine\Engine\ReportConfig;
use Mathielen\ReportWriteEngine\Output\Excel\Writer\ExcelReportWriter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class ExcelReportWriterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ExcelReportWriter
     */
    private $sut;

    private $templateRepo;

    protected function setUp()
    {
        $logger = new Logger('rendertest');
        $logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));

        $rendererRepository = new RendererRepository();
        $rendererRepository->add(
            RendererInterface::ORIENTATION_VERTICAL,
            new VerticalRenderer()
        );
        $rendererRepository->add(
            RendererInterface::ORIENTATION_HORIZONTAL,
            new HorizontalRenderer()
        );

        $targetRepo = new ExcelFileRepository(__DIR__ . '/../metadata/output');
        $this->templateRepo = new ExcelFileRepository(__DIR__ . '/../metadata/template');
        $this->sut = new ExcelReportWriter($targetRepo, $this->templateRepo, $rendererRepository, $logger);
    }

    public function testSimpleReport()
    {
        $reportData = json_decode(file_get_contents(__DIR__ . '/../metadata/data/testSimpleReport.json'), true);

        $reportConfig = new ReportConfig('simple.xlsx', 'simple-output.xlsx');
        $this->sut->write($reportData, $reportConfig);
    }

    public function testVerticalSinglelevelReport()
    {
        $reportData = json_decode(file_get_contents(__DIR__ . '/../metadata/data/testSinglelevelReport.json'), true);

        $reportConfig = new ReportConfig('singlelevel.xlsx', 'singlelevel-output.xlsx');
        $this->sut->write($reportData, $reportConfig);
    }

    public function testVerticalMultilevelReport()
    {
        $reportData = json_decode(file_get_contents(__DIR__ . '/../metadata/data/testMultilevelReport.json'), true);

        $reportConfig = new ReportConfig('multilevel.xlsx', 'multilevel-output.xlsx');
        $this->sut->write($reportData, $reportConfig);
    }

    public function testVerticalMultiSamelevelReport()
    {
        $reportData = json_decode(file_get_contents(__DIR__ . '/../metadata/data/testMultiSamelevelReport.json'), true);

        $reportConfig = new ReportConfig('multisamelevel.xlsx', 'multisamelevel-output.xlsx');
        $this->sut->write($reportData, $reportConfig);
    }

    public function testHorizontalSinglelevelReport()
    {
        $reportData = json_decode(file_get_contents(__DIR__ . '/../metadata/data/testSinglelevelReport.json'), true);

        $this->templateRepo->setMetadata('horizontal-singlelevel.xlsx', ['LEVEL1'=> RendererInterface::ORIENTATION_HORIZONTAL]);

        $reportConfig = new ReportConfig('horizontal-singlelevel.xlsx', 'horizontal-singlelevel-output.xlsx');
        $this->sut->write($reportData, $reportConfig);
    }

    public function testHorizontalMultilevelReport()
    {
        $reportData = json_decode(file_get_contents(__DIR__ . '/../metadata/data/testMultilevelReport.json'), true);

        $this->templateRepo->setMetadata('horizontal-multilevel.xlsx', ['LEVEL2'=> RendererInterface::ORIENTATION_HORIZONTAL]);

        $reportConfig = new ReportConfig('horizontal-multilevel.xlsx', 'horizontal-multilevel-output.xlsx');
        $this->sut->write($reportData, $reportConfig);
    }

    public function testFinalReport()
    {
        $reportData = json_decode(file_get_contents(__DIR__ . '/../metadata/data/commission.json'), true);

        $this->templateRepo->setMetadata('commission.xlsx', ['SERVICEPARTNER'=> RendererInterface::ORIENTATION_HORIZONTAL]);

        $reportConfig = new ReportConfig('commission.xlsx', 'commission-output.xlsx');
        $this->sut->write($reportData, $reportConfig);
    }

}
