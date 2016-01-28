<?php
namespace Mathielen\ReportWriteEngine\Output\Excel\Persistence;

use Assert\Assertion;

class ExcelFileRepository implements ExcelRepositoryInterface
{

    private $directory;

    private $metadata = [];

    public function __construct($directory)
    {
        Assertion::directory($directory);
        Assertion::readable($directory);

        $directory .= substr($directory, -1) === '/' ? '' : '/'; //add ending slash
        $this->directory = $directory;
    }

    public function setMetadata($id, $metadata = null)
    {
        $this->metadata[$id] = $metadata;
    }

    public function getMetadata($id)
    {
        return isset($this->metadata[$id]) ? $this->metadata[$id] : null;
    }

    /**
     * @return \PHPExcel
     */
    public function get($id)
    {
        Assertion::notEmpty($id);
        Assertion::string($id);

        $filePath = $this->directory . $id;

        $inputFileType = \PHPExcel_IOFactory::identify($filePath);
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);

        return $objReader->load($filePath);
    }

    /**
     * @return string
     */
    public function save(\PHPExcel $excel, $id)
    {
        Assertion::notEmpty($id);
        Assertion::string($id);
        Assertion::writeable($this->directory);

        $filePath = $this->directory . $id;

        $pathinfo = pathinfo($id);
        if (!isset($pathinfo['extension'])) {
            throw new \RuntimeException("Extension was expected for report id: '$id'. Unable to determine excel type.");
        }

        $writerType = self::extensionToType($pathinfo['extension']);
        $excelWriter = \PHPExcel_IOFactory::createWriter($excel, $writerType);

        $excelWriter->save($filePath);

        return $filePath;
    }

    private static function extensionToType($extension)
    {
        $extensionType = null;

        switch (strtolower($extension)) {
            case 'xlsx':            //	Excel (OfficeOpenXML) Spreadsheet
            case 'xlsm':            //	Excel (OfficeOpenXML) Macro Spreadsheet (macros will be discarded)
            case 'xltx':            //	Excel (OfficeOpenXML) Template
            case 'xltm':            //	Excel (OfficeOpenXML) Macro Template (macros will be discarded)
                $extensionType = 'Excel2007';
                break;
            case 'xls':                //	Excel (BIFF) Spreadsheet
            case 'xlt':                //	Excel (BIFF) Template
                $extensionType = 'Excel5';
                break;
            case 'ods':                //	Open/Libre Offic Calc
            case 'ots':                //	Open/Libre Offic Calc Template
                $extensionType = 'OOCalc';
                break;
            case 'slk':
                $extensionType = 'SYLK';
                break;
            case 'xml':                //	Excel 2003 SpreadSheetML
                $extensionType = 'Excel2003XML';
                break;
            case 'gnumeric':
                $extensionType = 'Gnumeric';
                break;
            case 'htm':
            case 'html':
                $extensionType = 'HTML';
                break;
            case 'csv':
                $extensionType = 'CSV';
                break;
        }

        return $extensionType;
    }
}
