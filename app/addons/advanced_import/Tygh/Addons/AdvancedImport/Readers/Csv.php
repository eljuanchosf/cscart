<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

namespace Tygh\Addons\AdvancedImport\Readers;

use Tygh\Common\OperationResult;
use Tygh\Enum\Addons\AdvancedImport\CsvDelimiters;

class Csv implements IReader
{
    /** @var string $path */
    protected $path;

    /** @var array $options */
    protected $options = array();

    /** @var int $max_line_size */
    protected $max_line_size = 65536;

    /** @var int $sample_size */
    protected $sample_size = 5;

    /** @var int $probes_number Delimiter auto detection probes number */
    protected $probes_number = 3;

    /** @var string $enclosure */
    protected $enclosure = '"';

    /** @var array $delimiter */
    protected $delimiter;

    /** @var array $schema */
    protected $schema = array();

    /** @inheritdoc */
    public function __construct($path, array $options = array())
    {
        $this->path = $path;
        $this->options = $options;
    }

    /** @inheritdoc */
    public function getSchema()
    {
        $result = new OperationResult(false, array());

        if (!$this->schema) {
            $delimiters = $this->getDelimiters();
            $file = fopen($this->path, 'rb');

            foreach ($delimiters as $delimiter => $literal_delimiter) {
                $schema = fgetcsv($file, $this->max_line_size, $literal_delimiter);
                if ($schema && count($schema) > 1) {
                    $this->options['delimiter'] = $delimiter;
                    $this->schema = $this->normalizeSchema($schema);
                    break;
                }
                fseek($file, 0);
            }

            fclose($file);
        } else {
            $result->setSuccess(true);
        }

        $result->setData($this->normalizeSchema($this->schema));

        return $result;
    }

    /**
     * Applies each of the provided delimiters to csv
     * and returns one that divides string to the biggest number of columns
     *
     * @param array $delimiters Array of delimiters
     *
     * @return array|bool
     */
    protected function getProbeDelimiter($delimiters = array())
    {
        $delimiters = $delimiters ?: CsvDelimiters::getAll();
        $columns_by_delimiter = array();

        $file = fopen($this->path, 'rb');

        foreach ($delimiters as $delimiter_code => $delimiter) {
            $probes_number = $this->probes_number;
            rewind($file);

            while ($probes_number && !feof($file)) {
                $csv_line = fgetcsv($file, 0, $delimiter, $this->enclosure);
                $columns_number = count($csv_line);

                if (isset($columns_by_delimiter[$delimiter_code])) {

                    if ($columns_by_delimiter[$delimiter_code] !== $columns_number) {
                        unset($columns_by_delimiter[$delimiter_code]);
                        break;
                    }
                } else {
                    $columns_by_delimiter[$delimiter_code] = $columns_number;
                }

                $probes_number--;
            }
        }

        arsort($columns_by_delimiter);
        $literal_delimiter = key($columns_by_delimiter);

        return $literal_delimiter
            ? array($literal_delimiter => $delimiters[$literal_delimiter])
            : false;
    }

    /**
     * Gets list of delimiter to parse file with.
     *
     * @return array
     */
    protected function getDelimiters()
    {
        $all_delimiters = CsvDelimiters::getAll();

        if ($this->delimiter === null) {
            $this->delimiter = $this->getProbeDelimiter($all_delimiters);
        }

        if ($this->delimiter) {
            $delimiters = $this->delimiter;
        } else {
            $delimiters = $all_delimiters;
        }

        return $delimiters;
    }

    /** @inheritdoc */
    public function getContents($count = null, array $schema = null)
    {
        $delimiters = $this->getDelimiters();

        if ($schema === null) {
            $schema = $this->getSchema()->getData();
        }

        if (!$schema) {
            return array();
        }

        foreach ($delimiters as $delimiter => $literal_delimiter) {
            $contents = fn_exim_get_csv(array(), $this->path, array(
                'validate_schema' => false,
                'import_schema'   => $schema,
                'count'           => $count,
                'delimiter'       => $delimiter,
            ));
            if ($contents && count($contents[0]) > 1) {
                $this->options['delimiter'] = $delimiter;

                return $contents;
            }
        }

        return array();
    }

    /** @inheritdoc */
    public function getApproximateLinesCount()
    {
        $filesize = filesize($this->path);

        $file = fopen($this->path, 'rb');

        $aggregate_sample_size = 0;
        $sample_lines_count = 0;
        $is_header_read = false;

        while (($sample = fgets($file, $this->max_line_size)) && $sample_lines_count < $this->sample_size) {
            $sample_size = count(unpack('C*', $sample));

            // skip header
            if (!$is_header_read) {
                $filesize -= $sample_size;
                $is_header_read = true;
                continue;
            }

            $aggregate_sample_size += $sample_size;
            $sample_lines_count++;
        }

        fclose($file);

        $approx_count = ceil($filesize / $aggregate_sample_size * $sample_lines_count);

        return $approx_count ?: 1;
    }

    /** @inheritdoc */
    public function getExtension()
    {
        return 'csv';
    }

    /**
     * Normalizes field names to store them in the database: removes linebreaks, trims content and
     * adds numeric indices to repeating fields.
     *
     * @param array $schema Import file fields description
     *
     * @return array
     */
    protected function normalizeSchema(array $schema)
    {
        $unique_names = array();

        foreach ($schema as $field_position => &$field_name) {
            $field_name = trim(preg_replace('/\s+/', ' ', $field_name));
            if (!isset($unique_names[$field_name])) {
                $unique_names[$field_name] = array();
            }
            $unique_names[$field_name][] = $field_position;
        }
        unset($field_name);

        foreach ($unique_names as $field_name => $positions_list) {
            if (count($positions_list) == 1) {
                continue;
            }

            $c = 0;
            foreach ($positions_list as $field_position) {
                $c++;
                $schema[$field_position] = $field_name . ' (' . $c . ')';
            }
        }

        return $schema;
    }
}