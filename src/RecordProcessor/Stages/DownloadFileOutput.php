<?php

namespace RodrigoPedra\RecordProcessor\Stages;

use League\Csv\Reader as RawCSVReader;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageFlusher;
use RodrigoPedra\RecordProcessor\Helpers\FileInfo;
use RodrigoPedra\RecordProcessor\Stages\TransferObjects\FlushPayload;
use RodrigoPedra\RecordProcessor\Writers\CSVFileWriter;
use RodrigoPedra\RecordProcessor\Writers\ExcelFileWriter;
use RodrigoPedra\RecordProcessor\Writers\HTMLTableWriter;
use RodrigoPedra\RecordProcessor\Writers\JSONFileWriter;
use RodrigoPedra\RecordProcessor\Writers\TextFileWriter;
use RuntimeException;
use SplFileInfo;
use SplFileObject;
use function RodrigoPedra\RecordProcessor\value_or_null;

class DownloadFileOutput implements ProcessorStageFlusher
{
    const DELETE_FILE_AFTER_DOWNLOAD = true;
    const KEEP_AFTER_DOWNLOAD        = false;

    /** @var SplFileObject */
    protected $inputFile;

    /** @var FileInfo */
    protected $inputFileInfo;

    /** @var FileInfo */
    protected $outputFileInfo;

    /** @var bool */
    protected $deleteAfterDownload;

    public function __construct( $outputFileName = '', $deleteFileAfterDownload = false )
    {
        $this->outputFileInfo      = value_or_null( $outputFileName );
        $this->deleteAfterDownload = $deleteFileAfterDownload === true;
    }

    public function flush( FlushPayload $payload )
    {
        $this->inputFile     = $this->getInputFile( $payload );
        $this->inputFileInfo = $this->inputFile->getFileInfo( FileInfo::class );

        $this->buildOutputFileInfo( $payload->getWriterClassName() );

        $output = $this->downloadFile();

        $payload->setOutput( $output );

        return $payload;
    }

    protected function getInputFile( FlushPayload $payload )
    {
        $inputFile = $payload->getOutput();

        if (!$inputFile instanceof SplFileObject) {
            throw new RuntimeException( 'Output is not a file' );
        }

        return $inputFile;
    }

    protected function downloadFile()
    {
        if ($this->inputFileInfo->isCSV()) {
            $this->outputFileWithLeagueCSV( $this->inputFile );
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

        header( 'Content-Type: ' . $mimeType . '; charset=utf-8' );
        header( 'Content-Transfer-Encoding: binary' );
        header( 'Content-Description: File Transfer' );

        $filename = rawurlencode( $this->outputFileInfo->getBasename() );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
    }

    protected function outputFileWithLeagueCSV( SplFileObject $file )
    {
        // League\CSV handles CSV BOM properly
        $reader = RawCSVReader::createFromFileObject( $file );
        $reader->output( $this->outputFileInfo->getBasename() );
    }

    protected function buildOutputFileInfo( $writerClassName )
    {
        if (is_string( $this->outputFileInfo )) {
            $this->outputFileInfo = new FileInfo( $this->outputFileInfo );

            return;
        }

        if ($this->outputFileInfo instanceof SplFileInfo) {
            $this->outputFileInfo = $this->outputFileInfo->getFileInfo( FileInfo::class );

            return;
        }

        // invalid outputFileInfo, tries to guess from inputFile

        if ($this->inputFileInfo->isTempFile()) {
            $this->outputFileInfo = $this->buildTempOutputFileInfo( $writerClassName );

            return;
        }

        $this->outputFileInfo = $this->inputFileInfo;
    }

    protected function unlinkInputFile()
    {
        if (!$this->deleteAfterDownload) {
            return;
        }

        $this->inputFile = null;

        $realPath = $this->inputFileInfo->getRealPath();

        if ($realPath === false) {
            // file does not exists
            return;
        }

        unlink( $realPath );
    }

    protected function buildTempOutputFileInfo( $writerClassName )
    {
        $fileName = implode( '_', [ 'temp', date( 'YmdHis' ), str_random( 8 ) ] );

        $extension = null;

        switch ($writerClassName) {
            case CSVFileWriter::class:
                $extension = 'csv';
                break;
            case ExcelFileWriter::class:
                // Cannot write excel to temporary file
                break;
            case HTMLTableWriter::class:
                $extension = 'html';
                break;
            case JSONFileWriter::class:
                $extension = 'json';
                break;
            case TextFileWriter::class:
                $extension = 'txt';
                break;
        }

        $fileName = implode( '.', array_filter( [ $fileName, $extension ] ) );

        return new FileInfo( $fileName );
    }
}
