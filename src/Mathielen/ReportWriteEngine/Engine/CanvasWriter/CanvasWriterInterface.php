<?php
namespace Mathielen\ReportWriteEngine\Engine\CanvasWriter;

use Mathielen\ReportWriteEngine\Engine\Canvas;

interface CanvasWriterInterface
{

    public function write(Canvas $canvas);

}
