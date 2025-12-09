<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Serializers;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;

final class SerializerAddon
{
    private readonly \Closure|array $addon;

    public function __construct(callable|array $addon)
    {
        $this->addon = \is_array($addon) ? $addon : $addon(...);
    }

    public function handle(Serializer $serializer, $recordCount, ?Record $record = null): void
    {
        if (\is_array($this->addon)) {
            $serializer->append($this->addon);

            return;
        }

        $content = \call_user_func($this->addon, new AddonContext($serializer, $recordCount, $record));

        $content = \value($content);

        if (\blank($content)) {
            return;
        }

        $serializer->append($content);
    }
}
