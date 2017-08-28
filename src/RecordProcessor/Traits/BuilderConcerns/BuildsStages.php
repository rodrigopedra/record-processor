<?php

namespace RodrigoPedra\RecordProcessor\Traits\BuilderConcerns;

use RodrigoPedra\RecordProcessor\Stages\DownloadFileOutput;
use RodrigoPedra\RecordProcessor\Stages\ValidRecords;

trait BuildsStages
{
    public function onlyValidRecords()
    {
        $this->addStage( new ValidRecords );

        return $this;
    }

    public function downloadFileOutput( $outputFilename = '' )
    {
        $this->addStage( new DownloadFileOutput( $outputFilename ) );

        return $this;
    }
}
