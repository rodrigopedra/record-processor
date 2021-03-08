<?php

namespace RodrigoPedra\RecordProcessor\Support;

class FileInfo extends \SplFileInfo
{
    public const INPUT_STREAM = 'php://input';
    public const OUTPUT_STREAM = 'php://output';
    public const TEMP_FILE = 'php://temp';
    public const TEMP_FILE_MEMORY_SIZE = 4194304; // 4MB

    public function getExtension(): string
    {
        return \strtolower(parent::getExtension());
    }

    public function getFileInfo($class = null): \SplFileInfo
    {
        return parent::getFileInfo($class ?? self::class);
    }

    public function isTempFile(): bool
    {
        if ($this instanceof \SplTempFileObject) {
            return true;
        }

        return \substr($this->getPathname(), 0, 10) === self::TEMP_FILE;
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

        return \mime_content_type($this->getBasename());
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

    public function getBasenameWithExtension(string $extension = null): string
    {
        return \implode('.', \array_filter([
            $this->getBasenameWithoutExtension(),
            $extension,
        ]));
    }

    public static function createTempFileObject(): \SplTempFileObject
    {
        return new \SplTempFileObject(self::TEMP_FILE_MEMORY_SIZE);
    }

    public static function createFileObject($file, string $mode = 'r'): \SplFileObject
    {
        if ($file === static::TEMP_FILE) {
            return static::createTempFileObject();
        }

        if (\is_string($file)) {
            $fileInfo = new static($file);

            return $fileInfo->isTempFile()
                ? FileInfo::createTempFileObject()
                : $fileInfo->openFile($mode);
        }

        if (! (\is_object($file) && $file instanceof \SplFileObject)) {
            throw new \InvalidArgumentException('File should be a path to a file or a \SplFileObject');
        }

        /** @var FileInfo $fileInfo */
        $fileInfo = $file->getFileInfo(static::class);

        if ($fileInfo->isTempFile()) {
            return $file;
        }

        $file = null;

        return $fileInfo->openFile($mode);
    }

    public static function createWritableFileObject($file, $mode = 'wb'): \SplFileObject
    {
        $file = static::createFileObject($file, $mode);

        /** @var static $fileInfo */
        $fileInfo = $file->getFileInfo(static::class);

        if ($fileInfo->isTempFile()) {
            $file->ftruncate(0);

            return $file;
        }

        if ($fileInfo->getPathname() === static::OUTPUT_STREAM) {
            return $file;
        }

        if (! $fileInfo->isWritable()) {
            $fileName = $fileInfo->getPathname();

            throw new \RuntimeException("File {$fileName} is not writable");
        }

        return $file;
    }

    public static function createReadableFileObject($file, $mode = 'rb'): \SplFileObject
    {
        $file = static::createFileObject($file, $mode);

        /** @var static $fileInfo */
        $fileInfo = $file->getFileInfo(static::class);

        if (! $fileInfo->isTempFile() && ! $fileInfo->isReadable()) {
            $fileName = $fileInfo->getPathname();

            throw new \RuntimeException("File {$fileName} is not readable");
        }

        $file->rewind();

        return $file;
    }
}
