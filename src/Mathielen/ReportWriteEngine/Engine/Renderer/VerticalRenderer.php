<?php
namespace Mathielen\ReportWriteEngine\Engine\Renderer;

use Mathielen\ReportWriteEngine\Engine\Canvas;

class VerticalRenderer implements RendererInterface
{

    public function render(Canvas $fromCanvas, Canvas $toCanvas)
    {
        $toCanvas->write((array) $fromCanvas);
        $toCanvas->setPointer(null, $toCanvas->getHighestRow() + 1);
    }

}
