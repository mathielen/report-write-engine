<?php
namespace Mathielen\ReportWriteEngine\Output\Excel\Writer;

use Mathielen\ReportWriteEngine\Engine\Compiler\ReportDataCompiler;
use Mathielen\ReportWriteEngine\Engine\ReportRenderer;
use Mathielen\ReportWriteEngine\Output\Excel\CanvasWriter\ExcelCanvasWriter;
use Mathielen\ReportWriteEngine\Output\Excel\Persistence\ExcelRepositoryInterface;
use Mathielen\ReportWriteEngine\Engine\RendererRepository;
use Mathielen\ReportWriteEngine\Engine\ReportConfig;
use Mathielen\ReportWriteEngine\Engine\Writer\ReportWriterInterface;
use Mathielen\ReportWriteEngine\Output\Excel\Template\ExcelNamedRangeTemplatePopulator;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ExcelReportWriter implements ReportWriterInterface
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ExcelRepositoryInterface
     */
    private $targetRepository;

    /**
     * @var ExcelRepositoryInterface
     */
    private $templateRepository;

    /**
     * @var RendererRepository
     */
    private $rendererRepository;

    public function __construct(
        ExcelRepositoryInterface $targetRepository,
        ExcelRepositoryInterface $templateRepository,
        RendererRepository $rendererRepository,
        LoggerInterface $logger = null)
    {
        $this->targetRepository = $targetRepository;
        $this->templateRepository = $templateRepository;
        $this->rendererRepository = $rendererRepository;
        $this->logger = $logger ? $logger : new NullLogger();
    }

    public function write(array $reportData, ReportConfig $reportConfig)
    {
        //TODO metadata is a hack. Refactor the whole template repository idea to support metadata related
        //to the template file (like horizontal/vertical render style, etc)
        $templateConfig = $this->templateRepository->getMetadata($reportConfig->getTemplateId());
        $this->rendererRepository->setConfig($templateConfig ? $templateConfig : []);

        $template = $this->templateRepository->get($reportConfig->getTemplateId());
        $canvasWriter = new ExcelCanvasWriter($template, $this->logger);
        $rangePopulator = new ExcelNamedRangeTemplatePopulator($this->logger);

        $renderer = new ReportRenderer(
            $this->rendererRepository,
            new ReportDataCompiler($this->logger),
            $rangePopulator->populate($canvasWriter->getTemplateSheet()),
            $this->logger
        );

        $canvas = $renderer->render($reportData);
        $output = $canvasWriter->write($canvas);

        return $this->targetRepository->save($output, $reportConfig->getId());
    }

}
