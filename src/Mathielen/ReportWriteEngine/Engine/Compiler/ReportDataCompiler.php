<?php
namespace Mathielen\ReportWriteEngine\Engine\Compiler;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ReportDataCompiler
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger ? $logger : new NullLogger();
    }

    protected function translate($templateValue, array $data)
    {
        $translated = null;

        if (preg_match('/{{(\w+)}}/', $templateValue, $regs)) {
            $key = $regs[1];
            if (array_key_exists($key, $data) && !is_Array($data[$key])) {
                $translated = preg_replace('/{{(\w+)}}/', $data[$key], $templateValue);
            } else {
                $this->logger->notice("Could not translate $templateValue", ['data-keys' => array_keys($data)]);
            }
        }

        return $translated;
    }

    public function compile(array $item, array $template)
    {
        $data = $template;
        foreach ($data as &$row) {
            foreach ($row as &$col) {
                $col['value'] = $this->translate($col['value'], $item);
            }
        }

        return $data;
    }

}
