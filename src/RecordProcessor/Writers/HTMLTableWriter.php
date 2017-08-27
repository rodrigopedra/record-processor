<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use League\Csv\HTMLConverter;
use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
use RodrigoPedra\RecordProcessor\Helpers\WriterConfigurator;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use RodrigoPedra\RecordProcessor\Traits\HasOutput;
use RuntimeException;
use function RodrigoPedra\RecordProcessor\value_or_null;

class HTMLTableWriter implements ConfigurableWriter
{
    use CountsLines, HasOutput;

    /** @var HTMLConverter|null */
    protected $writer = null;

    /** @var array */
    protected $records = [];

    /** @var string */
    protected $html = '';

    /** @var string */
    protected $tableClassAttribute = '';

    /** @var string */
    protected $tableIdAttribute = '';

    /**
     * @param  string $tableClassAttribute
     */
    public function setTableClassAttribute( $tableClassAttribute )
    {
        $this->tableClassAttribute = value_or_null( $tableClassAttribute ) ?: '';
    }

    /**
     * @param  string $tableIdAttribute
     */
    public function setTableIdAttribute( $tableIdAttribute )
    {
        $this->tableIdAttribute = value_or_null( $tableIdAttribute ) ?: '';
    }

    public function open()
    {
        $this->lineCount = 0;
        $this->html      = '';
        $this->records   = [];

        $this->writer = ( new HTMLConverter )
            // should be chained, ->table() returns a cloned HTMLConverter instance
            ->table( $this->tableClassAttribute, $this->tableIdAttribute );
    }

    public function close()
    {
        $this->html = $this->writer->convert( $this->records );

        $this->writer  = null;
        $this->records = [];
    }

    public function append( $content )
    {
        if (!is_array( $content )) {
            throw new RuntimeException( 'content for HTMLTableWriter should be an array' );
        }

        array_push( $this->records, $content );

        $this->incrementLineCount();
    }

    public function output()
    {
        return $this->html;
    }

    public function getConfigurableMethods()
    {
        return [
            'setTableClassAttribute',
            'setTableIdAttribute',
        ];
    }

    public function createConfigurator()
    {
        return new WriterConfigurator( $this, true, true );
    }
}
