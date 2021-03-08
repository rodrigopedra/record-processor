<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\JSONFileSerializerConfigurator;

class JSONFileSerializer extends FileSerializer
{
    public const JSON_ENCODE_OPTIONS = \JSON_NUMERIC_CHECK | \JSON_HEX_TAG | \JSON_HEX_AMP | \JSON_HEX_APOS | \JSON_HEX_QUOT;

    protected $jsonEncodeOptions = self::JSON_ENCODE_OPTIONS;

    public function __construct($file = null)
    {
        parent::__construct($file);

        $this->configurator = new JSONFileSerializerConfigurator($this, false, false);
    }

    public function withEncodeOptions(int $encodeOptions): self
    {
        $this->jsonEncodeOptions = $encodeOptions;

        return $this;
    }

    public function close()
    {
        $this->file->fwrite(']');
    }

    public function append($content)
    {
        if (\is_object($content) && $content instanceof \JsonSerializable) {
            $content = \json_encode($content->jsonSerialize(), $this->jsonEncodeOptions);

            $this->write($content);

            return;
        }

        if (\is_object($content) && $content instanceof Jsonable) {
            $content = $content->toJson($this->jsonEncodeOptions);

            $this->write($content);

            return;
        }

        if (\is_object($content) && $content instanceof Arrayable) {
            $content = $content->toArray();
        }

        $content = \value($content);

        if (\blank($content)) {
            return;
        }

        $content = \json_encode($content, $this->jsonEncodeOptions);

        $this->write($content);
    }

    protected function write($content)
    {
        $prepend = $this->lineCount() === 0 ? '[' : ',';
        $content = \vsprintf('%s%s', [$prepend, $content]);

        $this->file->fwrite($content);

        $this->incrementLineCount();
    }
}
