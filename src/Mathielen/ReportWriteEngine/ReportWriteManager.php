<?php
namespace Mathielen\ReportWriteEngine;

use Assert\Assertion;
use Mathielen\ReportWriteEngine\Engine\ReportConfig;
use Mathielen\ReportWriteEngine\Engine\Writer\ReportWriterInterface;

class ReportWriteManager
{

    /**
     * @var ReportWriterInterface[]
     */
    private $reportWriters;

    public function __construct(array $reportWriters = [])
    {
        $this->reportWriters = $reportWriters;
    }

    /**
     * @return $this
     */
    public function addReportWriter($format, ReportWriterInterface $reportWriter)
    {
        Assertion::string($format);
        Assertion::notEmpty($format);

        $this->reportWriters[$format] = $reportWriter;

        return $this;
    }

    /**
     * @return ReportWriterInterface
     */
    private function getReportWriter($format)
    {
        Assertion::string($format);
        Assertion::notEmpty($format);

        if (!isset($this->reportWriters[$format])) {
            throw new \RuntimeException("ReportWriter for $format not found");
        }

        return $this->reportWriters[$format];
    }

    /**
     * @return mixed
     */
    public function write($format, array $data, ReportConfig $reportConfig)
    {
        $reportWriter = $this->getReportWriter($format);

        return $reportWriter->write($data, $reportConfig);
    }

}
