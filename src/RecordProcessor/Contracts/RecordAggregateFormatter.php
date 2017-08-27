<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface RecordAggregateFormatter extends RecordFormatter
{
    /**
     * Encode Record objects content to writer format
     *
     * @param  Writer   $writer
     * @param  Record[] $children
     *
     * @return bool
     */
    public function formatChildren( Writer $writer, array $children );
}
