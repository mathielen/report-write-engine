<?php
namespace Mathielen\ReportWriteEngine\Engine;

class CanvasTest extends \PHPUnit_Framework_TestCase
{

    public function testCanvas()
    {
        $canvas = new Canvas();
        $canvas[1][0] = 'a';
        $canvas[2][0] = 'X';
        $canvas[3][0] = 'Y';
        $canvas[4][0] = 'c';

        $canvasB = new Canvas();
        $canvasB[1][0] = 'b';

        $canvas->insert($canvasB, 2, 0, 2);

        $this->assertEquals([
            1 => ['a'],
            2 => ['b'],
            3 => ['c'],
            4 => ['c'] //TODO fix me! must be removed
        ], $canvas->getArrayCopy());
    }

    public function testCanvasVertical()
    {
        $canvas = new Canvas();
        $canvas[1][0] = '0';
        $canvas[2][1] = 'X';
        $canvas[2][2] = 'Y';
        $canvas[1][3] = '4';
        $canvas[1][4] = '5';

        $canvasB = new Canvas();
        $canvasB[1][0] = '1';
        $canvasB[1][1] = '2';
        $canvasB[1][2] = '3';

        $canvas->insert($canvasB, 1, 1, 1, 2);

        $this->assertEquals([
            1 => [0, 1, 2, 3, 5],
            2 => [1 => 'X', 2 => 'Y']
        ], $canvas->getArrayCopy());
    }

    /**
     * @dataProvider getInsertYData
     */
    public function testInsertY($y, $h, array $expectedData)
    {
        $from = [
            0 => [
                0 => 'A',
                1 => 'B',
                2 => 'C'
            ],
            1 => [
                0 => 'D',
                1 => 'E',
                2 => 'F'
            ]
        ];
        $from = new Canvas(null, $from);

        $to = [
            0 => [
                0 => 'X',
                1 => 'Y',
                2 => 'Z'
            ]
        ];
        $to = new Canvas(null, $to);

        $from->insert($to, $y, 0, $h, 0);
        $this->assertEquals($expectedData, $from->getArrayCopy());
    }

    public function getInsertYData()
    {
        return [
            [
                1, 0,
                [
                    0 => [
                        0 => 'A',
                        1 => 'B',
                        2 => 'C'
                    ],
                    1 => [
                        0 => 'X',
                        1 => 'Y',
                        2 => 'Z'
                    ],
                    2 => [
                        0 => 'D',
                        1 => 'E',
                        2 => 'F'
                    ]
                ]
            ],
            [
                2, 0,
                [
                    0 => [
                        0 => 'A',
                        1 => 'B',
                        2 => 'C'
                    ],
                    1 => [
                        0 => 'D',
                        1 => 'E',
                        2 => 'F'
                    ],
                    2 => [
                        0 => 'X',
                        1 => 'Y',
                        2 => 'Z'
                    ],
                ]
            ],
            [
                0, 2,
                [
                    0 => [
                        0 => 'X',
                        1 => 'Y',
                        2 => 'Z'
                    ],
                ]
            ]
        ];
    }

    /**
     * @dataProvider getInsertXData
     */
    public function testInsertX($x, $w, array $expectedData)
    {
        $from = [
            0 => [
                0 => 'A',
                1 => 'B',
                2 => 'C'
            ],
            1 => [
                0 => 'D',
                1 => 'E',
                2 => 'F'
            ]
        ];
        $from = new Canvas(null, $from);

        $to = [
            0 => [
                0 => 'X',
                1 => 'Y',
                2 => 'Z'
            ]
        ];
        $to = new Canvas(null, $to);

        $from->insert($to, 0, $x, 0, $w);

        $this->assertEquals($expectedData, $from->getArrayCopy());
    }

    public function getInsertXData()
    {
        return [
            [
                1, 0,
                [
                    0 => [
                        0 => 'A',
                        1 => 'X',
                        2 => 'Y',
                        3 => 'Z',
                    ],
                    1 => [
                        0 => 'D',
                        1 => 'E',
                        2 => 'F'
                    ]
                ]
            ],
            [
                2, 0,
                [
                    0 => [
                        0 => 'A',
                        1 => 'B',
                        2 => 'X',
                        3 => 'Y',
                        4 => 'Z',
                    ],
                    1 => [
                        0 => 'D',
                        1 => 'E',
                        2 => 'F'
                    ]
                ]
            ]
        ];
    }

}
