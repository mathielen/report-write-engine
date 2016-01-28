<?php
namespace Mathielen\ReportWriteEngine\Output\Excel\Persistence;

class ExcelFileRepositoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ExcelFileRepository
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new ExcelFileRepository('tests/metadata/template');

        @unlink('tests/metadata/template/test.xlsx');
    }

    protected function teardown()
    {
        @unlink('tests/metadata/template/test.xlsx');
    }

    public function testGet()
    {
        $actual = $this->sut->get('simple.xlsx');

        $objReader = new \PHPExcel_Reader_Excel2007();
        $expected = $objReader->load('tests/metadata/template/simple.xlsx');

        $this->assertEquals($expected->getActiveSheet()->toArray(), $actual->getActiveSheet()->toArray());
    }

    public function testSave()
    {
        $objReader = new \PHPExcel_Reader_Excel2007();
        $excel = $objReader->load('tests/metadata/template/simple.xlsx');

        $actual = $this->sut->save($excel, 'test.xlsx');

        $this->assertEquals('tests/metadata/template/test.xlsx', $actual);
        $this->assertFileExists('tests/metadata/template/test.xlsx');
    }

}
