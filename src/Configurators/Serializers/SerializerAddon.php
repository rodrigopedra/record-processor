<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Serializers;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;
use RodrigoPedra\RecordProcessor\Exceptions\InvalidAddonException;

class SerializerAddon
{
    protected readonly \Closure|array $addon;

    public function __construct(array|callable $addon)
    {
        if (! \is_array($addon) && ! \is_callable($addon)) {
            throw new InvalidAddonException('Invalid Addon');
        }

        $this->addon = \is_array($addon) ? $addon : $addon(...);
    }

    public function handle(Serializer $serializer, $recordCount, ?Record $record = null): void
    {
        if (\is_array($this->addon)) {
            $serializer->append($this->addon);

            return;
        }

        $content = \call_user_func($this->addon, new SerializerAddonContext($serializer, $recordCount, $record));

        $content = \value($content);

        if (\blank($content)) {
            return;
        }

        $serializer->append($content);
    }
}
