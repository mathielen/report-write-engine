<?php

class ReportRendererTest extends \PHPUnit_Framework_TestCase
{

    private $rendererRepository;
    private $logger;

    protected function setUp()
    {
        $this->rendererRepository = new \PHPExcelReport\Report\RendererRepository([
            //  'LEVEL1' => \PHPExcelReport\Report\Renderer\RendererInterface::ORIENTATION_HORIZONTAL
        ]);
        $this->rendererRepository->add(
            \PHPExcelReport\Report\Renderer\RendererInterface::ORIENTATION_VERTICAL,
            new \PHPExcelReport\Report\Renderer\VerticalRenderer()
        );
        $this->rendererRepository->add(
            \PHPExcelReport\Report\Renderer\RendererInterface::ORIENTATION_HORIZONTAL,
            new \PHPExcelReport\Report\Renderer\HorizontalRenderer()
        );

        $this->logger = new \Monolog\Logger('rendertest');
        $this->logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout', \Monolog\Logger::DEBUG));
    }

    public function testSimple()
    {
        $data = [
            'HEADER' => [
                'HEADER_CAPTION' => 'I am Header'
            ],
            'ROOT' => [
                'ROOT_CAPTION' => 'I am Root'
            ],
            'FOOTER' => [
                'FOOTER_CAPTION' => 'I am Footer'
            ]
        ];

        $namedRanges = [
            'HEADER' => Array(
                0 => Array(
                    0 => 'HEADER {{HEADER_CAPTION}}',
                ),
            ),
            'ROOT' => Array(
                1 => Array(
                    0 => 'ROOT {{ROOT_CAPTION}}',
                ),
            ),
            'FOOTER' => Array(
                2 => Array(
                    0 => 'FOOTER {{FOOTER_CAPTION}}',
                ),
            ),
        ];

        $renderer = new \PHPExcelReport\Report\ReportRenderer(
            $this->rendererRepository,
            new \PHPExcelReport\Report\Compiler\ReportDataCompiler($this->logger),
            $namedRanges,
            $this->logger);

        $canvas = $renderer->render($data);

        $this->assertEquals([
            0 => [
                0 => 'HEADER I am Header'
            ],
            1 => [
                0 => 'ROOT I am Root'
            ],
            2 => [
                0 => 'FOOTER I am Footer'
            ]
        ], $canvas->getArrayCopy());
    }

    public function testOnelevel()
    {
        $data = [
            'ROOT' => [
                'ROOT_CAPTION' => 'TEXT',
                'LEVEL1' => [
                    [
                        'LEVEL1_CAPTION' => 'I am Level 1'
                    ]
                ]
            ]
        ];

        $namedRanges = [
            'ROOT' => Array(
                0 => Array(
                    0 => 'ROOT',
                    1 => '{{ROOT_CAPTION}}',
                ),
                1 => Array(
                    0 => 'LEVEL1',
                    1 => '{{LEVEL1_CAPTION}}',
                ),
                2 => Array(
                    0 => 'ROOT_FOOTER',
                )
            ),
            'LEVEL1' => Array(
                1 => Array(
                    0 => 'LEVEL1',
                    1 => '{{LEVEL1_CAPTION}}',
                )
            )
        ];

        $renderer = new \PHPExcelReport\Report\ReportRenderer(
            $this->rendererRepository,
            new \PHPExcelReport\Report\Compiler\ReportDataCompiler($this->logger),
            $namedRanges,
            $this->logger);

        $canvas = $renderer->render($data);

        $this->assertEquals([
            0 => [
                0 => 'ROOT',
                1 => 'TEXT'
            ],
            1 => [
                0 => 'LEVEL1',
                1 => 'I am Level 1'
            ],
            2 => [
                0 => 'ROOT_FOOTER'
            ]
        ], $canvas->getArrayCopy());
    }

    public function testMultilevel()
    {
        $data = [
            'ROOT' => [
                'ROOT_CAPTION' => 'TEXT',
                'LEVEL1' => [
                    [
                        'LEVEL1_CAPTION' => '1',
                        'LEVEL2' => [
                            [
                                'LEVEL2_CAPTION' => '1.1'
                            ]
                        ]
                    ],
                    [
                        'LEVEL1_CAPTION' => '2',
                        'LEVEL2' => [
                            [
                                'LEVEL2_CAPTION' => '2.1'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $namedRanges = [
            'ROOT' => Array(
                0 => Array(
                    0 => 'ROOT',
                    1 => '{{ROOT_CAPTION}}',
                ),
                1 => Array(
                    0 => 'LEVEL1',
                    1 => '{{LEVEL1_CAPTION}}',
                ),
                2 => Array(
                    0 => 'LEVEL2-R',
                    1 => '{{LEVEL2_CAPTION}}',
                ),
                3 => Array(
                    0 => 'ROOT_FOOTER',
                )
            ),
            'LEVEL1' => Array(
                1 => Array(
                    0 => 'LEVEL1',
                    1 => '{{LEVEL1_CAPTION}}',
                ),
                2 => Array(
                    0 => 'LEVEL2-1',
                    1 => '{{LEVEL2_CAPTION}}',
                )
            ),
            'LEVEL2' => Array(
                2 => Array(
                    0 => 'LEVEL2',
                    1 => '{{LEVEL2_CAPTION}}',
                )
            )
        ];

        $renderer = new \PHPExcelReport\Report\ReportRenderer(
            $this->rendererRepository,
            new \PHPExcelReport\Report\Compiler\ReportDataCompiler($this->logger),
            $namedRanges,
            $this->logger);

        $canvas = $renderer->render($data);

        $this->assertEquals([
            0 => [
                0 => 'ROOT',
                1 => 'TEXT'
            ],
            1 => [
                0 => 'LEVEL1',
                1 => '1'
            ],
            2 => [
                0 => 'LEVEL2',
                1 => '1.1'
            ],
            3 => [
                0 => 'LEVEL1',
                1 => '2'
            ],
            4 => [
                0 => 'LEVEL2',
                1 => '2.1'
            ],
            5 => [
                0 => 'ROOT_FOOTER'
            ]
        ], $canvas->getArrayCopy());
    }

}
