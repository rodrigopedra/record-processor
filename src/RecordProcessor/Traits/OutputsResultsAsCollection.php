<?php

namespace RodrigoPedra\RecordProcessor\Traits;

use Illuminate\Support\Collection;

trait OutputsResultsAsCollection
{
    /** @var  Collection */
    protected $results;

    /** @var  bool */
    protected $outputsResults = false;

    protected function resetOutput()
    {
        $this->results = $this->outputsResults ? new Collection : null;
    }

    protected function pushToOuput( $content )
    {
        if (!$this->outputsResults) {
            return;
        }

        $this->results->push( $content );
    }

    public function shouldOutputResults( $outputResults = false )
    {
        $this->outputsResults = $outputResults;

        $this->resetOutput();
    }

    public function output()
    {
        if ($this->outputsResults) {
            return $this->results;
        }

        return null;
    }
}
