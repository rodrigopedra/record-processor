<?php

namespace RodrigoPedra\RecordProcessor\Stages;

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
    protected ?FileInfo $inputFileInfo = null;

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

    /**
     * @throws \League\Csv\Exception
     */
    public function flush(FlushPayload $payload, \Closure $next): ?FlushPayload
    {
        $this->inputFileInfo = $this->inputFile($payload);

        $this->buildOutputFileInfo($payload->serializerClassName());

        $this->downloadFile();
    }

    protected function inputFile(FlushPayload $payload): FileInfo
    {
        $inputFile = $payload->output();

        if (\is_string($inputFile)) {
            return new FileInfo($inputFile);
        }

        if ($inputFile instanceof \SplFileInfo) {
            return $inputFile->getFileInfo(FileInfo::class);
        }

        throw new \RuntimeException('Process output should be a file to be downloadable');
    }

    /**
     * @throws \League\Csv\Exception
     */
    protected function downloadFile(): never
    {
        if (\is_null($this->inputFileInfo)) {
            throw new \RuntimeException('No input file was provided');
        }

        if ($this->inputFileInfo->isCSV()) {
            $this->outputFileWithLeagueCSV($this->inputFileInfo);
        } else {
            $this->sendHeaders();

            $fileObject = $this->inputFileInfo->openFile();
            $fileObject->fpassthru();
        }

        $this->unlinkInputFile();

        die;
    }

    protected function sendHeaders(): void
    {
        if (\is_null($this->inputFileInfo)) {
            throw new \RuntimeException('No input file was provided');
        }

        if (\is_null($this->outputFile)) {
            throw new \RuntimeException('Failed to assess output mime type');
        }

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
     * @throws \League\Csv\Exception
     */
    protected function outputFileWithLeagueCSV(\SplFileInfo $file): void
    {
        // league\csv handles CSV BOM properly
        $reader = Reader::from($file);
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
        if ($this->inputFileInfo?->isTempFile()) {
            $this->outputFileInfo = $this->buildTempOutputFileInfo($className);

            return;
        }

        if (! \is_null($this->inputFileInfo)) {
            $this->outputFileInfo = $this->inputFileInfo;

            return;
        }

        throw new \RuntimeException('Failed to build output file info');
    }

    protected function unlinkInputFile(): void
    {
        if (! $this->deleteAfterDownload) {
            return;
        }

        $realPath = $this->inputFileInfo?->getRealPath() ?? false;

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
