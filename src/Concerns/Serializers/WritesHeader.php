<?php

namespace RodrigoPedra\RecordProcessor\Concerns\Serializers;

use RodrigoPedra\RecordProcessor\Contracts\Record;

trait WritesHeader
{
    protected function writeHeader(Record $firstRecord = null): self
    {
        $header = $this->header();

        if (\is_null($header)) {
            return $this;
        }

        $header->handle($this->serializer, $this->recordCount(), $firstRecord);

        return $this;
    }
}
