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

class ReportWriteManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ReportWriteManager
     */
    private $sut;

    private $templateRepo;

    protected function setUp()
    {
        $logger = null;
        //$logger = new Logger('rendertest');
        //$logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));

        $rendererRepository = new RendererRepository();
        $rendererRepository->add(
            RendererInterface::ORIENTATION_VERTICAL,
            new VerticalRenderer()
        );
        $rendererRepository->add(
            RendererInterface::ORIENTATION_HORIZONTAL,
            new HorizontalRenderer()
        );

        $targetRepo = new ExcelFileRepository('tests/metadata/output');
        $this->templateRepo = new ExcelFileRepository('tests/metadata/template');
        $excelWriter = new ExcelReportWriter($targetRepo, $this->templateRepo, $rendererRepository, $logger);

        $this->sut = new ReportWriteManager([
            'excel' => $excelWriter
        ]);
    }

    public function testSimpleReport()
    {
        $reportData = json_decode(file_get_contents('tests/metadata/data/testSimpleReport.json'), true);

        $reportConfig = new ReportConfig('simple.xlsx', 'simple-output.xlsx');

        $actual = $this->sut->write('excel', $reportData, $reportConfig);
        $this->assertEquals('tests/metadata/output/simple-output.xlsx', $actual);
    }

}
