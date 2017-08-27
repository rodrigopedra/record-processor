<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use Exception;
use InvalidArgumentException;
use PDO;
use PDOStatement;
use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
use RodrigoPedra\RecordProcessor\Helpers\WriterConfigurator;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use RodrigoPedra\RecordProcessor\Traits\NoOutput;
use RuntimeException;
use function RodrigoPedra\RecordProcessor\is_associative_array;
use function RodrigoPedra\RecordProcessor\value_or_null;

class PDOWriter implements ConfigurableWriter
{
    use CountsLines, NoOutput;

    /** @var PDO */
    protected $pdo = null;

    /** @var PDOStatement|mixed */
    protected $writer = null;

    /** @var string */
    protected $tableName;

    /** @var int */
    protected $columnCount;

    /** @var array */
    protected $columns = [];

    /** @var string */
    protected $valuesStatement;

    /** @var bool */
    protected $usesTransaction = true;

    /** @var bool */
    protected $inTransaction = false;

    /** @var bool|null */
    protected $isAssociative = null;

    public function __construct( PDO $pdo, $tableName, array $columns )
    {
        $this->columnCount = count( $columns );

        if ($this->columnCount < 1) {
            throw new InvalidArgumentException( 'Columns array should containt at least one column' );
        }

        $this->pdo             = $pdo;
        $this->tableName       = value_or_null( $tableName );
        $this->columns         = is_associative_array( $columns ) ? array_keys( $columns ) : $columns;
        $this->valuesStatement = $this->formatValuesString( $this->columnCount );
    }

    /**
     * @param bool $usesTransaction
     */
    public function setUsesTransaction( $usesTransaction )
    {
        $this->usesTransaction = $usesTransaction;
    }

    public function open()
    {
        $this->lineCount = 0;

        if ($this->usesTransaction === true) {
            $this->pdo->beginTransaction();
            $this->inTransaction = true;
        }
    }

    public function close()
    {
        if ($this->inTransaction) {
            $this->pdo->commit();
            $this->inTransaction = false;
        }

        $this->writer = null;
    }

    /**
     * @param  array $content
     *
     * @return void
     * @throws Exception
     */
    public function append( $content )
    {
        if (!is_array( $content )) {
            throw new RuntimeException( 'content for PDOWriter should be a string' );
        }

        try {
            $data   = $this->prepareValuesForInsert( $content );
            $writer = $this->prepareWriter( 1 );

            if (!$writer->execute( $data )) {
                throw new RuntimeException( 'Could not write PDO records' );
            }

            $this->incrementLineCount( $this->writer->rowCount() );
        } catch ( Exception $exception ) {
            if ($this->inTransaction) {
                $this->pdo->rollBack();
                $this->inTransaction = false;
            }

            throw $exception;
        }
    }

    protected function prepareWriter( $count )
    {
        if (!is_null( $this->writer )) {
            return $this->writer;
        }

        $query = $this->formatQueryStatement( $count );

        $this->writer = $this->pdo->prepare( $query );

        return $this->writer;
    }

    protected function formatQueryStatement( $count )
    {
        $tokens = [
            'INSERT INTO',
            $this->tableName,
            $this->sanitizeColumns( $this->columns ),
            'VALUES',
            implode( ',', array_fill( 0, $count, $this->valuesStatement ) ),
        ];

        return implode( ' ', $tokens );
    }

    protected function formatValuesString( $valuesQuantity )
    {
        return '(' . implode( ',', array_fill( 0, $valuesQuantity, '?' ) ) . ')';
    }

    protected function sanitizeColumns( array $columns )
    {
        $columns = value_or_null( $columns );
        $columns = array_map( function ( $column ) {
            return value_or_null( $column );
        }, $columns );

        return '(' . implode( ',', $columns ) . ')';
    }

    protected function prepareValuesForInsert( array $values )
    {
        if (count( $values ) !== $this->columnCount) {
            throw new RuntimeException( 'Record column count does not match PDOWriter column definition' );
        }

        if (is_null( $this->isAssociative )) {
            $this->isAssociative = is_associative_array( $values );

            if ($this->isAssociative) {
                sort( $this->columns );
            }
        }

        if ($this->isAssociative) {
            ksort( $values );

            return array_values( $values );
        }

        return $values;
    }

    public function getConfigurableSetters()
    {
        return [ 'setUsesTransaction' ];
    }

    public function createConfigurator()
    {
        return new WriterConfigurator( $this, false, false );
    }
}
