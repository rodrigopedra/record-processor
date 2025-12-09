<?php

namespace RodrigoPedra\RecordProcessor\Stages;

use RodrigoPedra\RecordProcessor\Concerns\CountsRecords;
use RodrigoPedra\RecordProcessor\Concerns\Serializers\HasHeader;
use RodrigoPedra\RecordProcessor\Concerns\Serializers\HasTrailler;
use RodrigoPedra\RecordProcessor\Concerns\Serializers\WritesHeader;
use RodrigoPedra\RecordProcessor\Concerns\Serializers\WritesTrailler;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\SerializerAddon;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageFlusher;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageHandler;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordSerializer;
use RodrigoPedra\RecordProcessor\Contracts\Serializer as SerializerContract;
use RodrigoPedra\RecordProcessor\Support\TransferObjects\FlushPayload;

final class Writer implements ProcessorStageHandler, ProcessorStageFlusher
{
    use CountsRecords;
    use HasHeader;
    use WritesHeader;
    use HasTrailler;
    use WritesTrailler;

    private RecordSerializer $recordSerializer;

    private bool $isOpen = false;

    public function __construct(
        private readonly SerializerContract $serializer,
    ) {
        $this->recordSerializer = $serializer->configurator()->recordSerializer();
    }

    public function handle(Record $record, \Closure $next): ?Record
    {
        $this->open($record);

        if ($this->recordSerializer->serializeRecord($this->serializer, $record)) {
            $this->incrementRecordCount();
        }

        return $next($record);
    }

    public function flush(FlushPayload $payload, \Closure $next): FlushPayload
    {
        if (! $this->isOpen) {
            // writes header if no records were written yet
            $this->open();
        }

        if ($payload->hasRecord()) {
            $this->handle($payload->record(), fn (): null => null);
        }

        $this->close();

        $payload->withSerializerClassName($this->serializer::class);
        $payload->withLineCount($this->serializer->lineCount());
        $payload->withRecordCount($this->recordCount());
        $payload->withOutput($this->serializer->output());

        return $next($payload);
    }

    private function open(?Record $record = null): void
    {
        if ($this->isOpen) {
            return;
        }

        $this->recordCount = 0;
        $this->isOpen = true;

        $this->serializer->open();
        $this->writeHeader($record);
    }

    private function close(): void
    {
        if (! $this->isOpen) {
            return;
        }

        $this->isOpen = false;

        $this->writeTrailler();
        $this->serializer->close();
    }

    public function withHeader(?SerializerAddon $header): self
    {
        $this->header = $header;

        return $this;
    }

    public function withTrailler(?SerializerAddon $trailler): self
    {
        $this->trailler = $trailler;

        return $this;
    }
}
