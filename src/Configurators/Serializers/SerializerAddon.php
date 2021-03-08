<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Serializers;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;
use RodrigoPedra\RecordProcessor\Exceptions\InvalidAddonException;

class SerializerAddon
{
    /** @var  array|callable */
    protected $addon;

    /**
     * @param  array|callable  $addon
     * @throws \RodrigoPedra\RecordProcessor\Exceptions\InvalidAddonException
     */
    public function __construct($addon)
    {
        if (! \is_array($addon) && ! \is_callable($addon)) {
            throw new InvalidAddonException();
        }

        $this->addon = $addon;
    }

    public function handle(Serializer $serializer, $recordCount, Record $record = null)
    {
        if (\is_array($this->addon)) {
            $serializer->append($this->addon);

            return;
        }

        $content = \call_user_func($this->addon, new SerializerAddonCallback($serializer, $recordCount, $record));

        $content = \value($content);

        if (\blank($content)) {
            return;
        }

        $serializer->append($content);
    }
}
