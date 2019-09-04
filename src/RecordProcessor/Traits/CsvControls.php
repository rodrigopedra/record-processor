<?php

namespace RodrigoPedra\RecordProcessor\Traits;

use InvalidArgumentException;
use RodrigoPedra\RecordProcessor\Contracts\NewLines;

/**
 *  A trait to configure and check CSV file and content
 * Partially extracted from
 * \League\Csv\Config\AbstractCsv
 *
 * @license http://opensource.org/licenses/MIT
 * @link    https://github.com/thephpleague/csv/
 * @version 9.0.1
 * @package League.csv
 */
trait CsvControls
{
    /**
     * the field delimiter (one character only)
     *
     * @var string
     */
    protected $delimiter = ',';

    /**
     * the field enclosure character (one character only)
     *
     * @var string
     */
    protected $enclosure = '"';

    /**
     * the field escape character (one character only)
     *
     * @var string
     */
    protected $escape = '\\';

    /**
     * newline character
     *
     * @var string
     */
    protected $newline = NewLines::UNIX_NEWLINE;

    /**
     * The Output file BOM character
     *
     * @var string
     */
    protected $outputBOM = '';

    /**
     * Sets the field delimiter
     *
     * @param  string  $delimiter
     * @return void
     * @throws InvalidArgumentException If $delimiter is not a single character
     */
    public function setDelimiter($delimiter)
    {
        if (! $this->isValidCsvControls($delimiter)) {
            throw new InvalidArgumentException('The delimiter must be a single character');
        }
        $this->delimiter = $delimiter;
    }

    /**
     * Returns the current field delimiter
     *
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * Sets the field enclosure
     *
     * @param  string  $enclosure
     * @return void
     * @throws InvalidArgumentException If $enclosure is not a single character
     */
    public function setEnclosure($enclosure)
    {
        if (! $this->isValidCsvControls($enclosure)) {
            throw new InvalidArgumentException('The enclosure must be a single character');
        }
        $this->enclosure = $enclosure;
    }

    /**
     * Returns the current field enclosure
     *
     * @return string
     */
    public function getEnclosure()
    {
        return $this->enclosure;
    }

    /**
     * Sets the field escape character
     *
     * @param  string  $escape
     * @return void
     * @throws InvalidArgumentException If $escape is not a single character
     */
    public function setEscape($escape)
    {
        if (! $this->isValidCsvControls($escape)) {
            throw new InvalidArgumentException('The escape character must be a single character');
        }
        $this->escape = $escape;
    }

    /**
     * Returns the current field escape character
     *
     * @return string
     */
    public function getEscape()
    {
        return $this->escape;
    }

    /**
     * Sets the newline sequence characters
     *
     * @param  string  $newline
     * @return void
     */
    public function setNewline($newline)
    {
        $this->newline = (string)$newline;
    }

    /**
     * Returns the current newline sequence characters
     *
     * @return string
     */
    public function getNewline()
    {
        return $this->newline;
    }

    /**
     * Sets the BOM sequence in use
     *
     * @param  string  $str The BOM sequence
     * @return void
     */
    public function setOutputBOM($str)
    {
        $this->outputBOM = (string)$str;
    }

    /**
     * Returns the BOM sequence in use
     *
     * @return string
     */
    public function getOutputBOM()
    {
        return $this->outputBOM;
    }

    /**
     * Tell whether the submitted string is a valid CSV Control character
     *
     * @param  string  $str The submitted string
     * @return bool
     */
    protected function isValidCsvControls($str)
    {
        return 1 == mb_strlen($str);
    }
}
