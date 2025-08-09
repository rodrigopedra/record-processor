<?php

namespace RodrigoPedra\RecordProcessor\Stages;

use League\Csv\Exception;
use League\Csv\Reader;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageFlusher;
use RodrigoPedra\RecordProcessor\Serializers\CSVFileSerializer;
use RodrigoPedra\RecordProcessor\Serializers\HTMLTableSerializer;
use RodrigoPedra\RecordProcessor\Serializers\JSONFileSerializer;
use RodrigoPedra\RecordProcessor\Serializers\TextFileSerializer;
use RodrigoPedra\RecordProcessor\Support\FileInfo;
use RodrigoPedra\RecordProcessor\Support\TransferObjects\FlushPayload;

class DownloadFileOutput implements ProcessorStageFlusher
{
    public const DELETE_FILE_AFTER_DOWNLOAD = true;
    public const KEEP_AFTER_DOWNLOAD = false;

    protected ?\SplFileObject $inputFile = null;
    protected FileInfo $inputFileInfo;
    protected ?FileInfo $outputFileInfo = null;
    protected bool $deleteAfterDownload;
    protected \SplFileInfo|string|null $outputFile;

    public function __construct(string $outputFile = '', bool $deleteFileAfterDownload = false)
    {
        $this->outputFile = \blank($outputFile)
            ? null
            : $outputFile;

        $this->deleteAfterDownload = $deleteFileAfterDownload;
    }

    public function flush(FlushPayload $payload, \Closure $next): ?FlushPayload
    {
        $this->inputFile = $this->inputFile($payload);
        $this->inputFileInfo = $this->inputFile->getFileInfo(FileInfo::class);

        $this->buildOutputFileInfo($payload->serializerClassName());

        $this->downloadFile();

        return null;
    }

    protected function inputFile(FlushPayload $payload): \SplFileObject
    {
        $inputFile = $payload->output();

        if (! ($inputFile instanceof \SplFileObject)) {
            throw new \RuntimeException('Process output should be a file to be downloadable');
        }

        return $inputFile;
    }

    protected function downloadFile(): void
    {
        if ($this->inputFileInfo->isCSV()) {
            $this->outputFileWithLeagueCSV($this->inputFile);
        } else {
            $this->sendHeaders();

            $this->inputFile->rewind();
            $this->inputFile->fpassthru();
        }

        $this->unlinkInputFile();

        die;
    }

    protected function sendHeaders()
    {
        $mimeType = $this->inputFileInfo->isTempFile()
            ? $this->outputFileInfo->guessMimeType()
            : $this->inputFileInfo->guessMimeType();

        \header('Content-Type: ' . $mimeType . '; charset=utf-8');
        \header('Content-Transfer-Encoding: binary');
        \header('Content-Description: File Transfer');

        $filename = \rawurlencode($this->outputFileInfo->getBasename());
        \header('Content-Disposition: attachment; filename="' . $filename . '"');
    }

    /**
     * @throws Exception
     */
    protected function outputFileWithLeagueCSV(\SplFileObject $file)
    {
        // league\csv handles CSV BOM properly
        $reader = Reader::createFromFileObject($file);
        $reader->download($this->outputFileInfo->getBasename());
    }

    protected function buildOutputFileInfo(?string $className): void
    {
        if (\is_string($this->outputFile)) {
            $this->outputFileInfo = new FileInfo($this->outputFile);

            return;
        }

        if ($this->outputFile instanceof \SplFileInfo) {
            $this->outputFileInfo = $this->outputFile->getFileInfo(FileInfo::class);

            return;
        }

        // invalid outputFileInfo, tries to guess from inputFile
        if ($this->inputFileInfo->isTempFile()) {
            $this->outputFileInfo = $this->buildTempOutputFileInfo($className);

            return;
        }

        $this->outputFileInfo = $this->inputFileInfo;
    }

    protected function unlinkInputFile(): void
    {
        if (! $this->deleteAfterDownload) {
            return;
        }

        $this->inputFile = null;

        $realPath = $this->inputFileInfo->getRealPath();

        if ($realPath === false) {
            // file does not exist
            return;
        }

        \unlink($realPath);
    }

    protected function buildTempOutputFileInfo(?string $className): FileInfo
    {
        $fileName = \implode('_', ['temp', \uniqid(\date('YmdHis'), true)]);

        $fileName = match ($className) {
            CSVFileSerializer::class => $fileName . '.csv',
            HTMLTableSerializer::class => $fileName . '.html',
            JSONFileSerializer::class => $fileName . '.json',
            TextFileSerializer::class => $fileName . '.txt',
            // Cannot write excel to temporary file
            default => $fileName,
        };

        return new FileInfo($fileName);
    }
}
