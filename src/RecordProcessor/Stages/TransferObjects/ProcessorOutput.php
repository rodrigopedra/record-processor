<?php

namespace RodrigoPedra\RecordProcessor\Stages\TransferObjects;

class ProcessorOutput
{
    /** @var  int */
    protected $inputLineCount = 0;

    /** @var int */
    protected $inputRecordCount = 0;

    /** @var  int */
    protected $outputLineCount = 0;

    /** @var int */
    protected $outputRecordCount = 0;

    /** @var  mixed */
    protected $output = null;

    /**
     * ProcessOutput constructor.
     *
     * @param  int   $inputLineCount
     * @param  int   $inputRecordCount
     * @param  int   $outputLineCount
     * @param  int   $outputRecordCount
     * @param  mixed $output
     */
    public function __construct( $inputLineCount, $inputRecordCount, $outputLineCount, $outputRecordCount, $output )
    {
        $this->inputLineCount    = $inputLineCount;
        $this->inputRecordCount  = $inputRecordCount;
        $this->outputLineCount   = $outputLineCount;
        $this->outputRecordCount = $outputRecordCount;
        $this->output            = $output;
    }

    /**
     * @return int
     */
    public function getInputLineCount()
    {
        return $this->inputLineCount;
    }

    /**
     * @return int
     */
    public function getInputRecordCount()
    {
        return $this->inputRecordCount;
    }

    /**
     * @return int
     */
    public function getOutputLineCount()
    {
        return $this->outputLineCount;
    }

    /**
     * @return int
     */
    public function getOutputRecordCount()
    {
        return $this->outputRecordCount;
    }

    /**
     * @return bool
     */
    public function hasOutput()
    {
        return !is_null( $this->output );
    }

    /**
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
    }
}
