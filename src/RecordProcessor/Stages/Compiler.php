<?php

namespace RodrigoPedra\RecordProcessor\Stages;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\Writer;
use RodrigoPedra\RecordProcessor\Traits\CountsRecords;
use RodrigoPedra\RecordProcessor\Traits\Writers\HasHeader;
use RodrigoPedra\RecordProcessor\Contracts\RecordFormatter;
use RodrigoPedra\RecordProcessor\Traits\Writers\HasTrailler;
use RodrigoPedra\RecordProcessor\Traits\Writers\WritesHeader;
use RodrigoPedra\RecordProcessor\Traits\Writers\WritesTrailler;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageFlusher;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageHandler;
use RodrigoPedra\RecordProcessor\Stages\TransferObjects\FlushPayload;

class Compiler implements ProcessorStageHandler, ProcessorStageFlusher
{
    use CountsRecords, HasHeader, WritesHeader, HasTrailler, WritesTrailler;

    /** @var Writer */
    protected $writer;

    /** @var RecordFormatter */
    protected $recordFormatter;

    /** @var bool */
    protected $isOpen;

    public function __construct(Writer $writer, RecordFormatter $recordFormatter)
    {
        $this->writer = $writer;
        $this->recordFormatter = $recordFormatter;
    }

    /**
     * @param  Record  $record
     * @return  Record|null
     */
    public function handle(Record $record)
    {
        $this->open($record);

        if ($this->recordFormatter->formatRecord($this->writer, $record)) {
            $this->incrementRecordCount();
        }

        return $record;
    }

    /**
     * @param  FlushPayload  $payload
     * @return FlushPayload
     */
    public function flush(FlushPayload $payload)
    {
        if ($payload->hasRecord()) {
            $record = $this->handle($payload->getRecord());

            $payload->setRecord($record);
        }

        if (! $this->isOpen) {
            // writes header if result is still empty (no records were written)
            $this->open();
        }

        $this->close();

        $payload->setWriterClassName(get_class($this->writer));
        $payload->setLineCount($this->writer->getLineCount());
        $payload->setRecordCount($this->getRecordCount());
        $payload->setOutput($this->writer->output());

        return $payload;
    }

    /**
     * @param  Record|null  $record
     * @return void
     */
    protected function open(Record $record = null)
    {
        if ($this->isOpen) {
            return;
        }

        $this->recordCount = 0;
        $this->isOpen = true;

        $this->writer->open();
        $this->writeHeader($record);
    }

    protected function close()
    {
        if (! $this->isOpen) {
            return;
        }

        $this->isOpen = false;

        $this->writeTrailler();
        $this->writer->close();
    }
}
