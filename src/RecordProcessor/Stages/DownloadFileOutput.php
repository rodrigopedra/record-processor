<?php

namespace RodrigoPedra\RecordProcessor\Stages;

use League\Csv\Reader as RawCSVReader;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageFlusher;
use RodrigoPedra\RecordProcessor\Helpers\FileInfo;
use RodrigoPedra\RecordProcessor\Stages\TransferObjects\FlushPayload;
use RuntimeException;
use SplFileInfo;
use SplFileObject;
use function RodrigoPedra\RecordProcessor\value_or_null;

class DownloadFileOutput implements ProcessorStageFlusher
{
    /** @var FileInfo */
    protected $downloadFileInfo;

    public function __construct( $fileName = '' )
    {
        $fileName = value_or_null( $fileName );

        $this->downloadFileInfo = is_null( $fileName )
            ? null
            : new FileInfo( $fileName );
    }

    public function flush( FlushPayload $payload )
    {
        $output = $payload->getOutput();

        if (!is_object( $output )) {
            throw new RuntimeException( 'Output is not a SplFileInfo object' );
        }

        if (!$output instanceof SplFileInfo) {
            throw new RuntimeException( 'Output is not a SplFileInfo object' );
        }

        $realPath = $output->getRealPath();

        if ($realPath === false) {
            throw new RuntimeException( "File {$realPath} does not exist" );
        }

        $fileInfo = new FileInfo( $realPath );

        if (is_null( $this->downloadFileInfo )) {
            $this->downloadFileInfo = $fileInfo;
        }

        if ($fileInfo->isCSV()) {
            $this->downloadWithLeagueCSV( $fileInfo );

            return $payload;
        }

        $this->outputContent( $fileInfo );

        return $payload;
    }

    protected function outputContent( FileInfo $fileInfo )
    {
        $this->outputHeaders( $fileInfo );

        $downloadFile = new SplFileObject( $fileInfo->getRealPath(), 'rb' );
        $downloadFile->rewind();
        $downloadFile->fpassthru();

        die;
    }

    protected function outputHeaders( FileInfo $fileInfo )
    {
        header( 'Content-Type: ' . $fileInfo->guessMimeType() . '; charset=utf-8' );
        header( 'Content-Transfer-Encoding: binary' );
        header( 'Content-Description: File Transfer' );

        $filename = rawurlencode( $this->downloadFileInfo->getBasename() );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
    }

    protected function downloadWithLeagueCSV( FileInfo $fileInfo )
    {
        // League\CSV handle CSV correctly BOM
        $reader = RawCSVReader::createFromPath( $fileInfo->getRealPath() );
        $reader->output( $this->downloadFileInfo->getBasenameWithExtension( 'csv' ) );
        die;
    }
}
