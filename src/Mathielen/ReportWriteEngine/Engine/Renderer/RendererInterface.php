<?php
namespace Mathielen\ReportWriteEngine\Engine\Renderer;

use Mathielen\ReportWriteEngine\Engine\Canvas;

interface RendererInterface
{

    const ORIENTATION_HORIZONTAL = 'horizontal';
    const ORIENTATION_VERTICAL = 'vertical';

    public function render(Canvas $fromCanvas, Canvas $toCanvas);

}
