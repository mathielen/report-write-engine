<?php
namespace Mathielen\ReportWriteEngine\Engine;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Canvas extends \ArrayObject
{

    private $pointer = ['X' => 0, 'Y' => 0];

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger = null, array $input = [])
    {
        parent::__construct($input);
        $this->logger = $logger ? $logger : new NullLogger();
    }

    public function getHeight()
    {
        return $this->count() === 0 ? 0 : $this->getHighestRow() - $this->getLowestRow() + 1;
    }

    public function getHighestRow()
    {
        $highestRow = $this->count() === 0 ? 0 : max(array_keys($this->getArrayCopy()));

        return $highestRow;
    }

    public function getLowestRow()
    {
        return $this->count() === 0 ? 0 : min(array_keys($this->getArrayCopy()));
    }

    public function getHighestCol()
    {
        $highestCol = 0;
        foreach ($this as $row) {
            foreach ($row as $c => $col) {
                $highestCol = max($highestCol, $c);
            }
        }

        return $highestCol;
    }

    public function setPointer($x, $y = null)
    {
        $this->pointer['X'] = is_null($x) ? $this->pointer['X'] : $x;
        $this->pointer['Y'] = is_null($y) ? $this->pointer['Y'] : $y;

        $this->logger->debug("Setting pointer to X:" . $this->pointer['X'] . ", Y:" . $this->pointer['Y']);
    }

    public function inc($direction = 'Y', $increment = 1)
    {
        $this->pointer[$direction] += $increment;
    }

    public function write(array $data)
    {
        $this->logger->debug("Writing array to canvas", ['array' => $data]);

        $y = $this->pointer['Y'];

        foreach ($data as $row) {

            foreach ($row as $x => $col) {
                $this->set($x + $this->pointer['X'], $y, $col);
            }

            $y++;
        }

        $this->sort();
    }

    private function set($x, $y, $data)
    {
        //$this->logger->debug("Write value to canvas [$x,$y] = " . (is_array($data) ? $data['value'] : $data));

        $this[$y][$x] = $data;
    }

    private function sort()
    {
        $this->ksort();
        foreach ($this as &$row) {
            ksort($row);
        }
    }

    public function insert(Canvas $canvas, $y, $x = 0, $h = 0, $w = 0)
    {
        $this->logger->debug("Inserting canvas at X:$x, Y:$y. Replacing W:$w, H:$h", ['canvas' => $canvas]);

        //remove height
        for ($i = $y; $i < $y + $h; $i++) {
            if (isset($this[$i])) {
                if ($x > 0) {
                    foreach ($this[$i] as $ci => $col) {
                        if ($ci > $x) {
                            unset($this[$i][$x]);
                        }
                    }
                } else {
                    unset($this[$i]);
                }
            }
        }

        //make space
        $insertHeight = $canvas->getHeight();
        foreach ($this->getArrayCopy() as $thisRowNum => $row) {
            if ($thisRowNum >= $y + $h && $x === 0) {
                $this->logger->debug("Shifting row $thisRowNum to " . ($thisRowNum + $insertHeight) . " due to >= Y:$y + H:$h = " . ($y + $h) . " and X === 0. Insertheight is: $insertHeight", ['row' => $row]);

                //unset($this[$thisRowNum]);
                $this[$thisRowNum + $insertHeight - $h] = $row;
            }
        }

        /*$startRowNum = $canvas->getLowestRow();
        $insertWidth = $canvas->getHighestCol() - $w;
        foreach ($this->getArrayCopy() as $thisRowNum => $row) {
            if ($thisRowNum == $startRowNum) {
                foreach ($row as $thisColNum => $col) {
                    if ($thisColNum > $x + $w && $col != '') {
                        $this[$thisRowNum][$thisColNum + $insertWidth] = $col;
                    }
                }
            }
        }*/

        $r = $c = 0;
        foreach ($canvas as $row) {
            foreach ($row as $c => $col) {
                $this->set($x + $c, $y + $r, $col);
            }

            $r++;
        }

        $this->sort();
    }

    public function __toString()
    {
        return json_encode($this->getArrayCopy(), JSON_PRETTY_PRINT);
    }

}
