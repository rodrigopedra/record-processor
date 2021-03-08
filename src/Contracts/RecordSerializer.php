<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface RecordSerializer
{
    public function serializeRecord(Serializer $serializer, Record $record): bool;
}
