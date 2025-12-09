<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\JSONFileSerializerConfigurator;
use RodrigoPedra\RecordProcessor\Support\EOL;
use RodrigoPedra\RecordProcessor\Support\FileInfo;

/**
 * @property \RodrigoPedra\RecordProcessor\Configurators\Serializers\JSONFileSerializerConfigurator $configurator
 */
class JSONFileSerializer extends FileSerializer
{
    public const JSON_ENCODE_OPTIONS = \JSON_NUMERIC_CHECK
    | \JSON_HEX_TAG
    | \JSON_HEX_AMP
    | \JSON_HEX_APOS
    | \JSON_HEX_QUOT;

    protected int $jsonEncodeOptions = self::JSON_ENCODE_OPTIONS;

    protected ?\SplFileObject $writer = null;

    public function __construct(\SplFileInfo|string|null $file = null)
    {
        parent::__construct(
            configurator: new JSONFileSerializerConfigurator($this, false, false),
            file: $file,
        );
    }

    public function withEncodeOptions(int $encodeOptions): static
    {
        $this->jsonEncodeOptions = $encodeOptions;

        return $this;
    }

    public function open(): void
    {
        $this->lineCount = 0;
        $this->writer = FileInfo::createWritableFileObject($this->file);
    }

    public function close(): void
    {
        $this->writer->fwrite(']');
        $this->writer->fwrite(EOL::UNIX->value);
        $this->writer = null;
    }

    /**
     * @throws \JsonException
     */
    public function append($content): void
    {
        if (\is_null($this->writer)) {
            $this->open();
        }

        $content = \value($content);

        if (\blank($content)) {
            return;
        }

        if ($this->lineCount() === 0) {
            $this->writer->fwrite('[');
        } else {
            $this->writer->fwrite(',');
        }

        $this->writer->fwrite($this->serialize($content));

        $this->incrementLineCount();
    }

    /**
     * @throws \JsonException
     */
    protected function serialize($content): string
    {
        if ($content instanceof Jsonable) {
            return $content->toJson($this->jsonEncodeOptions | \JSON_THROW_ON_ERROR);
        }

        $content = match (true) {
            $content instanceof \JsonSerializable => $content->jsonSerialize(),
            $content instanceof Arrayable => $content->toArray(),
            default => $content,
        };

        return \json_encode($content, $this->jsonEncodeOptions | \JSON_THROW_ON_ERROR);
    }
}
