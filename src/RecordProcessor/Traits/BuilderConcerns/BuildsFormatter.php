<?php

namespace RodrigoPedra\RecordProcessor\Traits\BuilderConcerns;

use InvalidArgumentException;
use RodrigoPedra\RecordProcessor\Contracts\RecordFormatter;
use RodrigoPedra\RecordProcessor\Records\Formatter\ArrayRecordFormatter;
use RodrigoPedra\RecordProcessor\Records\Formatter\CallbackRecordFormatter;

trait BuildsFormatter
{
    /** @var  RecordFormatter */
    protected $recordFormatter;

    /**
     * @param  RecordFormatter|callable $recordFormatter
     *
     * @return $this
     */
    public function usingFormatter( $recordFormatter = null )
    {
        if (is_callable( $recordFormatter )) {
            $this->recordFormatter = new CallbackRecordFormatter( $recordFormatter );

            return $this;
        }

        if (!$recordFormatter instanceof RecordFormatter) {
            throw new InvalidArgumentException( 'Formatter should implement RecordFormatter interface' );
        }

        $this->recordFormatter = $recordFormatter;

        return $this;
    }

    protected function getRecordFormatter()
    {
        if (is_null( $this->recordFormatter )) {
            return new ArrayRecordFormatter;
        }

        return $this->recordFormatter;
    }
}
