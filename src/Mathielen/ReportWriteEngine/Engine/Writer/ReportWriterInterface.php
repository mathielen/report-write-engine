<?php
namespace Mathielen\ReportWriteEngine\Engine\Writer;

use Mathielen\ReportWriteEngine\Engine\ReportConfig;

interface ReportWriterInterface
{

    /**
     * @return mixed
     */
    public function write(array $reportData, ReportConfig $reportConfig);

}
