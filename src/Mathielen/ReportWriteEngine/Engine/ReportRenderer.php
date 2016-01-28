<?php
namespace Mathielen\ReportWriteEngine\Engine;

use Mathielen\ReportWriteEngine\Engine\Compiler\ReportDataCompiler;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ReportRenderer
{

    /**
     * @var RendererRepository
     */
    private $rendererRepository;

    /**
     * @var ReportDataCompiler
     */
    private $reportDataCompiler;

    /**
     * @var array
     */
    private $namedRanges;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        RendererRepository $rendererRepository,
        ReportDataCompiler $reportDataCompiler,
        array $namedRanges,
        LoggerInterface $logger = null)
    {
        $this->rendererRepository = $rendererRepository;
        $this->reportDataCompiler = $reportDataCompiler;
        $this->namedRanges = $namedRanges;
        $this->logger = $logger ? $logger : new NullLogger();
    }

    private static function isDeepArray(array $array)
    {
        return !empty($array) && is_numeric(array_keys($array)[0]);
    }

    /**
     * @return Canvas
     */
    public function render(array $data)
    {
        $canvas = new Canvas($this->logger);

        foreach ($data as $node => $item) {
            if (is_array($item)) {
                $itemCanvas = $this->renderItem($item, $node);

                $this->rendererRepository
                    ->getRendererForNode($node)
                    ->render($itemCanvas, $canvas);
            } else {
                throw new \RuntimeException("Every entry of the first level of data must be an array.");
            }
        }

        return $canvas;
    }

    private function renderList(array $list, $namedRangeName)
    {
        $this->logger->debug("Rendering list $namedRangeName");

        $canvas = new Canvas($this->logger);
        foreach ($list as $item) {
            $itemCanvas = $this->renderItem($item, $namedRangeName);

            $this->rendererRepository
                ->getRendererForNode($namedRangeName)
                ->render($itemCanvas, $canvas);
        }

        return $canvas;
    }

    private function calculateOffsetY($namedRangeParent, $namedRangeChild)
    {
        $minParent = min(array_keys($namedRangeParent));
        $minChild = min(array_keys($namedRangeChild));

        return $minChild - $minParent;
    }

    private function calculateOffsetX($namedRangeParent, $namedRangeChild)
    {
        $firstRowChild = $namedRangeChild[array_keys($namedRangeChild)[0]];
        $firstRowParent = $namedRangeParent[array_keys($namedRangeParent)[0]];

        $offsetX = min(array_keys($firstRowChild)) - min(array_keys($firstRowParent));

        return $offsetX;
    }

    /**
     * @return Canvas
     */
    private function renderItem(array $data, $namedRangeName)
    {
        $this->logger->debug("[START] Rendering START for item $namedRangeName");

        $data = array_change_key_case($data, CASE_UPPER);
        $namedRangeName = strtoupper($namedRangeName);
        $namedRange = $this->namedRanges[$namedRangeName];

        if (!isset($namedRange)) {
            throw new \RuntimeException("Could not find named range $namedRangeName");
        }

        $canvas = new Canvas($this->logger);
        $compiledData = $this->reportDataCompiler->compile($data, $namedRange);

        //zero-base all data
        $compiledData = array_values($compiledData);
        foreach ($compiledData as &$row) {
            $row = array_values($row);
        }

        $canvas->write($compiledData);

        $i = 0;
        foreach ($data as $node => $item) {
            if (is_array($item)) {
                $itemNamedRange = isset($this->namedRanges[$node]) ? $this->namedRanges[$node] : null;

                if (self::isDeepArray($item) && $itemNamedRange) {
                    $this->logger->debug("[START] List and Insert operation for $node");

                    $y = $this->calculateOffsetY($namedRange, $itemNamedRange);
                    $h = count($itemNamedRange);
                    $x = $this->calculateOffsetX($namedRange, $itemNamedRange);
                    //$w = count($this->namedRanges[$node][array_keys($this->namedRanges[$node])[0]]);
                    $w = 0;

                    $itemCanvas = $this->renderList($item, $node);

                    $canvas->insert($itemCanvas, $y, $x, $h, $w);

                    $this->logger->debug("[DONE] List and Insert operation for $node");
                }
            }
            $i++;
        }

        $this->logger->debug("[DONE] Rendering for item $namedRangeName");

        return $canvas;
    }

}
