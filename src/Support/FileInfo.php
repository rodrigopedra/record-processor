<?php

namespace RodrigoPedra\RecordProcessor\Support;

class FileInfo extends \SplFileInfo
{
    private const TEMP_FILE_MEMORY_SIZE = 2_097_152; // 2MB

    public function getExtension(): string
    {
        return \strtolower(parent::getExtension());
    }

    public function getFileInfo($class = null): \SplFileInfo
    {
        return parent::getFileInfo($class ?? self::class);
    }

    public function guessMimeType(): string
    {
        $mimeMap = [
            'csv' => 'text/csv',
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'json' => 'application/json',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        $extension = $this->getExtension();

        if (\array_key_exists($extension, $mimeMap)) {
            return $mimeMap[$extension];
        }

        if (\extension_loaded('fileinfo')) {
            return \mime_content_type($this->getBasename());
        }

        throw new \RuntimeException('Failed to guess mimetipe for: ' . $this->getRealPath());
    }

    public function isCSV(): bool
    {
        return $this->getExtension() === 'csv';
    }

    public function getBasenameWithoutExtension(): string
    {
        $extension = $this->getExtension();
        $extension = $extension ? '.' . $extension : $extension;

        return $this->getBasename($extension);
    }

    public function getBasenameWithExtension(?string $extension = null): string
    {
        return \implode(
            '.',
            \array_filter([
                $this->getBasenameWithoutExtension(),
                $extension,
            ]),
        );
    }

    public static function createTempFileObject(int $maxMemory = self::TEMP_FILE_MEMORY_SIZE): \SplTempFileObject
    {
        return new \SplTempFileObject($maxMemory);
    }

    public static function createMemoryFileObject(): \SplFileObject
    {
        return new \SplFileObject(PhpStream::MEMORY->value, 'wb+');
    }

    public static function createFileObject(\SplFileInfo|PhpStream|string $file, string $mode = 'rb'): \SplFileObject
    {
        if ($file instanceof \SplFileObject) {
            return $file;
        }

        if ($file instanceof PhpStream) {
            return match ($file) {
                PhpStream::TEMP => static::createTempFileObject(),
                PhpStream::MEMORY => static::createMemoryFileObject(),
                PhpStream::OUTPUT => (new static($file->value))->openFile($mode),
            };
        }

        if (\str_starts_with($file, PhpStream::TEMP->value)) {
            return static::createTempFileObject(match (\str_contains($file, '/maxmemory:')) {
                true => \intval(\substr($file, \strrpos($file, ':') + 1)),
                false => self::TEMP_FILE_MEMORY_SIZE,
            });
        }

        if (\is_string($file)) {
            $file = new static($file);
        }

        return match (true) {
            PhpStream::isTempFile($file) => self::createTempFileObject(),
            PhpStream::isMemoryFile($file) => self::createMemoryFileObject(),
            default => $file->openFile($mode),
        };
    }

    public static function createWritableFileObject(\SplFileInfo|PhpStream|string $file, string $mode = 'wb'): \SplFileObject
    {
        $file = static::createFileObject($file, $mode);

        if (PhpStream::isOutputFile($file)) {
            return $file;
        }

        if (PhpStream::isTempFile($file) || PhpStream::isMemoryFile($file)) {
            $file->ftruncate(0);

            return $file;
        }

        if (! $file->isWritable()) {
            throw new \RuntimeException(\sprintf('File "%s" is not writable', $file->getPathname()));
        }

        return $file;
    }

    public static function createReadableFileObject(\SplFileInfo|string $file, string $mode = 'rb'): \SplFileObject
    {
        $file = static::createFileObject($file, $mode);
        $file->setFlags(\SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);

        if (! $file->isReadable() && ! PhpStream::isTempFile($file) && ! PhpStream::isMemoryFile($file)) {
            $fileName = $file->getPathname();

            throw new \RuntimeException(\sprintf('File "%s" is not readable', $fileName));
        }

        $file->rewind();

        return $file;
    }
}
