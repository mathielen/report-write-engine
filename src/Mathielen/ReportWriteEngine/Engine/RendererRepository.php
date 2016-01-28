<?php
namespace Mathielen\ReportWriteEngine\Engine;

use Mathielen\ReportWriteEngine\Engine\Renderer\RendererInterface;

class RendererRepository
{

    /**
     * @var array
     */
    private $reportConfig;

    /**
     * @var array
     */
    private $renderer = [];

    public function __construct(array $reportConfig = [])
    {
        $this->reportConfig = $reportConfig;
    }

    /**
     * @return $this
     */
    public function setConfig(array $reportConfig = [])
    {
        $this->reportConfig = $reportConfig;

        return $this;
    }

    /**
     * @return $this
     */
    public function add($orientation, RendererInterface $renderer)
    {
        $this->renderer[$orientation] = $renderer;

        return $this;
    }

    /**
     * @return RendererInterface
     */
    public function getRendererForNode($nodeId)
    {
        $orientation = isset($this->reportConfig[$nodeId]) ? $this->reportConfig[$nodeId] : RendererInterface::ORIENTATION_VERTICAL;
        if (!isset($this->renderer[$orientation])) {
            throw new \RuntimeException("Could not find writer for orientation $orientation");
        }

        return $this->renderer[$orientation];
    }

}
