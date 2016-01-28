<?php
namespace Mathielen\ReportWriteEngine\Engine;

use Assert\Assertion;

class ReportConfig
{

    private $id;
    private $templateId;

    public function __construct($templateId, $id)
    {
        Assertion::notEmpty($templateId);
        Assertion::string($id);

        $this->id = $id;
        $this->templateId = $templateId;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

}
