<?php
namespace Mathielen\ReportWriteEngine\Engine\Renderer;

use Mathielen\ReportWriteEngine\Engine\Canvas;

class HorizontalRenderer implements RendererInterface
{

    public function render(Canvas $fromCanvas, Canvas $toCanvas)
    {
        $toCanvas->write((array) $fromCanvas);
        $toCanvas->setPointer($toCanvas->getHighestCol() + 1);
    }
}
