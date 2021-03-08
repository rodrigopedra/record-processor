<?php

namespace RodrigoPedra\RecordProcessor\Examples;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\EchoSerializerConfigurator;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\ExcelFileSerializerConfigurator;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\HTMLTableSerializerConfigurator;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\JSONFileSerializerConfigurator;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\LogSerializerConfigurator;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\SerializerAddonCallback;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\SerializerConfigurator;
use RodrigoPedra\RecordProcessor\Examples\Loggers\ConsoleOutputLogger;
use RodrigoPedra\RecordProcessor\Examples\RecordObjects\ExampleRecordAggregateSerializer;
use RodrigoPedra\RecordProcessor\Examples\RecordObjects\ExampleRecordParser;
use RodrigoPedra\RecordProcessor\Examples\RecordObjects\ExampleRecordSerializer;
use RodrigoPedra\RecordProcessor\ProcessorBuilder;
use RodrigoPedra\RecordProcessor\Serializers\JSONFileSerializer;
use RodrigoPedra\RecordProcessor\Serializers\PDOSerializer;
use RodrigoPedra\RecordProcessor\Support\Excel\Formats;
use RodrigoPedra\RecordProcessor\Support\Excel\WorkbookConfigurator;
use RodrigoPedra\RecordProcessor\Support\Excel\WorksheetConfigurator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExamplesCommand extends Command
{
    protected function configure()
    {
        $this->setName('examples');

        $this->setDescription('Showcases converters usage');

        $this->addArgument('parser', InputArgument::REQUIRED, $this->availableParsers());
        $this->addArgument('serializer', InputArgument::REQUIRED, $this->availableSerializers());

        $this->addOption('log', 'l', InputOption::VALUE_NONE, 'logs record flow');
        $this->addOption('aggregate', 'a', InputOption::VALUE_NONE, 'aggregates output');
        $this->addOption('invalid', 'i', InputOption::VALUE_NONE, 'log invalid records');
    }

    protected function availableParsers(): string
    {
        return 'array|collection|csv|excel|iterator|pdo|text';
    }

    protected function availableSerializers(): string
    {
        return 'array|collection|csv|echo|excel|html|json|log|pdo|pdo-buffered|text';
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Script start
        $time_start = \microtime(true);
        $logger = new ConsoleOutputLogger($output);

        try {
            $builder = $this->makeBuilder();
            $builder->setLogger($logger);

            if ($input->getOption('invalid')) {
                $builder->logInvalidRecords('INVALID INPUT');
            }

            if ($input->getOption('log')) {
                $builder->logRecords('INPUT');
            }

            if ($input->getOption('aggregate')) {
                $builder->withRecordSerializer(new ExampleRecordAggregateSerializer());
                $builder->aggregateRecordsByKey();
            } else {
                $builder->withRecordSerializer(new ExampleRecordSerializer());
            }

            if ($input->getOption('log')) {
                $builder->logRecords('OUTPUT');
            }

            if ($input->getOption('invalid')) {
                $builder->logInvalidRecords('INVALID OUTPUT');
            }

            $builder->withRecordParser(new ExampleRecordParser());
            $this->readFrom($builder, $input->getArgument('parser'));

            $builder->onlyValidRecords();

            $this->serializeTo($builder, $input->getArgument('serializer'));

            $processor = $builder->build();

            $output = $processor->process();

            $logger->info('input lines: ' . $output->inputLineCount());
            $logger->info('input records: ' . $output->inputRecordCount());
            $logger->info('output lines: ' . $output->outputRecordCount());
            $logger->info('output records: ' . $output->outputRecordCount());

            if ($output->hasOutput()) {
                $logger->info('output: ', Arr::wrap($output->output()));
            }

            return 0;
        } finally {
            $logger->info(\sprintf('memory: %.2fMB', \floatval(\memory_get_peak_usage(true)) / 1024.0 / 1024.0));

            $time_end = \microtime(true);
            $execution_time = ($time_end - $time_start);

            $logger->info('Total Execution Time: ' . $execution_time . ' seconds');
        }
    }

    protected function makeBuilder(): ProcessorBuilder
    {
        return new ProcessorBuilder();
    }

    protected function readFrom(ProcessorBuilder $builder, string $reader): ProcessorBuilder
    {
        $inputPath = $this->storagePath('input');

        switch ($reader) {
            case 'array':
                return $builder->readFromArray($this->sampleData());
            case 'collection':
                return $builder->readFromCollection(new Collection($this->sampleData()));
            case 'csv':
                return $builder->readFromCSVFile($inputPath . '.csv');
            case 'excel':
                return $builder->readFromExcelFile($inputPath . '.xlsx');
            case 'iterator':
                return $builder->readFromIterator(new \ArrayIterator($this->sampleData()));
            case 'pdo':
                $pdo = $this->makeConnection();
                $query = 'SELECT name, email FROM users ORDER BY rowid LIMIT 25';

                return $builder->readFromPDO($pdo, $query);
            case 'text':
                return $builder->readFromTextFile($inputPath . '.txt');
            default:
                throw new \InvalidArgumentException('Invalid parser');
        }
    }

    protected function serializeTo(ProcessorBuilder $builder, string $serializer): ProcessorBuilder
    {
        $outputPath = $this->storagePath('output');

        switch ($serializer) {
            case 'array':
                return $builder->serializeToArray();
            case 'collection':
                return $builder->serializeToCollection();
            case 'csv':
                return $builder->serializeToCSVFile($outputPath . '.csv',
                    function (SerializerConfigurator $configurator) {
                        $configurator->withHeader(['name', 'email']);
                    });
            case 'echo':
                return $builder->serializeToEcho(function (EchoSerializerConfigurator $configurator) {
                    $configurator->withPrefix('PERSIST');
                });
            case 'excel':
                return $builder->serializeToExcelFile($outputPath . '.xlsx',
                    function (ExcelFileSerializerConfigurator $configurator) {
                        $configurator->withHeader(['name', 'email']);

                        $configurator->withTrailler(function (SerializerAddonCallback $proxy) {
                            $proxy->append([$proxy->recordCount() . ' records']);
                            $proxy->append([($proxy->lineCount() + 1) . ' lines']);
                        });

                        $configurator->withWorkbookConfigurator(function (WorkbookConfigurator $workbook) {
                            $workbook->setTitle('Workbook title');
                            $workbook->setCreator('Creator');
                            $workbook->setCompany('Company');
                        });

                        $configurator->withWorksheetConfigurator(function (WorksheetConfigurator $worksheet) {
                            $worksheet->setTitle('results', false);

                            $worksheet->withColumnFormat([
                                'A' => Formats::text(),
                                'B' => Formats::general(),
                            ]);

                            // header
                            $worksheet->freezeFirstRow();
                            $worksheet->configureCells('A1:B1', function ($cells) {
                                $cells->setFontWeight('bold');
                                $cells->setBorder('node', 'none', 'solid', 'none');
                            });
                            $worksheet->getStyle('A1:B1')->getNumberFormat()->setFormatCode(Formats::text());
                        });
                    });
            case 'html':
                return $builder->serializeToHTMLTable(function (HTMLTableSerializerConfigurator $configurator) {
                    $configurator->withHeader(['name', 'email']);
                    $configurator->withTableClassAttribute('table table-condensed');
                    $configurator->withTableIdAttribute('my-table');
                });
            case 'json':
                return $builder->serializeToJSONFile($outputPath . '.json',
                    function (JSONFileSerializerConfigurator $configurator) {
                        $configurator->withEncodeOptions(JSONFileSerializer::JSON_ENCODE_OPTIONS | \JSON_PRETTY_PRINT);
                    });
            case 'log':
                return $builder->serializeToLog(function (LogSerializerConfigurator $configurator) {
                    $configurator->withPrefix('PERSIST');
                });
            case 'pdo':
            case 'pdo-buffered':
                $pdo = $this->makeConnection();

                return $builder->serializeToPDO($pdo, 'users', ['name', 'email'], $serializer === 'pdo-buffered');
            case 'text':
                return $builder->serializeToTextFile($outputPath . '.txt');
            default:
                throw new \InvalidArgumentException('Invalid serializer');
        }
    }

    protected function storagePath(string $file): string
    {
        return __DIR__ . '/../storage/' . $file;
    }

    protected function makeConnection(): \PDO
    {
        $filePath = $this->storagePath('database.sqlite');

        $path = \realpath($filePath);
        $fileExists = $path !== false;

        if (! $fileExists) {
            \touch($filePath);
            $path = \realpath($filePath);
        }

        $connection = new \PDO("sqlite:{$path}", '', '', [
            \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
            \PDO::ATTR_STRINGIFY_FETCHES => false,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        $this->populateTable($connection, ! $fileExists);

        return $connection;
    }

    protected function populateTable(\PDO $connection, $seed)
    {
        $connection->exec('CREATE TABLE IF NOT EXISTS users (NAME TEXT, email TEXT)');

        if (! $seed) {
            return;
        }

        $serializer = new PDOSerializer($connection, 'users', ['name', 'email']);
        $serializer->withUsesTransaction(true);

        $serializer->open();

        $sampleData = $this->sampleData();

        foreach ($sampleData as $values) {
            $serializer->append($values);
        }

        $serializer->close();
    }

    protected function sampleData(): array
    {
        return [
            ['Rodrigo', 'rodrigo@example.com'],
            ['Rodrigo', 'rodrigo@example.org'],
            ['Noemi', 'noemi@example.com'],
            ['Bruno', 'bruno@example.org'],
            ['Bruno', 'bruno@example.com'],
        ];
    }
}
