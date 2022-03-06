<?php

namespace RodrigoPedra\RecordProcessor\Concerns\Serializers;

trait WritesTrailler
{
    protected function writeTrailler(): static
    {
        $trailler = $this->trailler();

        if (\is_null($trailler)) {
            return $this;
        }

        $trailler->handle($this->serializer, $this->recordCount());

        return $this;
    }
}
