<?php

namespace RodrigoPedra\RecordProcessor\Examples;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Psr\Log\LoggerInterface;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\EchoSerializerConfigurator;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\ExcelFileSerializerConfigurator;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\HTMLTableSerializerConfigurator;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\JSONFileSerializerConfigurator;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\LogSerializerConfigurator;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\AddonContext;
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
    protected function configure(): void
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
        $startedAt = \microtime(true);
        $logger = new ConsoleOutputLogger($output);

        try {
            $this->runExample($logger, $input);

            return Command::SUCCESS;
        } finally {
            $logger->info(\sprintf('memory: %.2fMB', \floatval(\memory_get_peak_usage(true)) / 1024.0 / 1024.0));

            $endedAt = \microtime(true);
            $executionTime = ($endedAt - $startedAt);

            $logger->info('Total Execution Time: ' . $executionTime . ' seconds');
        }
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Throwable
     */
    protected function runExample(LoggerInterface $logger, InputInterface $input): void
    {
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

        $result = $processor->process();

        $logger->info('input lines: ' . $result->inputLineCount());
        $logger->info('input records: ' . $result->inputRecordCount());
        $logger->info('output lines: ' . $result->outputRecordCount());
        $logger->info('output records: ' . $result->outputRecordCount());

        if ($result->hasOutput()) {
            $logger->info('output: ', Arr::wrap($result->output()));
        }
    }

    protected function makeBuilder(): ProcessorBuilder
    {
        return new ProcessorBuilder();
    }

    /**
     * @throws \Throwable
     */
    protected function readFrom(ProcessorBuilder $builder, string $reader): ProcessorBuilder
    {
        $builder->debug('Reader: ' . $reader);

        $inputPath = $this->storagePath('input');

        return match ($reader) {
            'array' => $builder->readFromArray($this->sampleData()),

            'collection' => $builder->readFromCollection(new Collection($this->sampleData())),

            'csv' => $builder->readFromCSVFile($inputPath . '.csv'),

            'excel' => $builder->readFromExcelFile($inputPath . '.xlsx'),

            'iterator' => $builder->readFromIterator(new \ArrayIterator($this->sampleData())),

            'text' => $builder->readFromTextFile($inputPath . '.txt'),

            'pdo' => $this->readFromPDO($builder),

            default => throw new \InvalidArgumentException('Invalid reader: ' . $reader),
        };
    }

    /**
     * @throws \Throwable
     */
    protected function readFromPDO(ProcessorBuilder $builder): ProcessorBuilder
    {
        $pdo = $this->makeConnection('input.sqlite');
        $this->populateTable($pdo);

        return $builder->readFromPDO($pdo, 'SELECT "name", "email" FROM "users" ORDER BY "rowid" LIMIT 25');
    }

    /**
     * @throws \Throwable
     */
    protected function serializeTo(ProcessorBuilder $builder, string $serializer): ProcessorBuilder
    {
        $builder->debug('Serializer: ' . $serializer);

        $outputPath = $this->storagePath('output');

        return match ($serializer) {
            'array' => $builder->serializeToArray(),

            'collection' => $builder->serializeToCollection(),

            'csv' => $builder->serializeToCSVFile($outputPath . '.csv', function (SerializerConfigurator $configurator): void {
                $configurator->withHeader(['name', 'email']);
            }),

            'echo' => $builder->serializeToEcho(function (EchoSerializerConfigurator $configurator): void {
                $configurator->withPrefix('PERSIST');
            }),

            'excel' => $builder->serializeToExcelFile($outputPath . '.xlsx',
                function (ExcelFileSerializerConfigurator $configurator): void {
                    $configurator->withHeader(['name', 'email']);

                    $configurator->withTrailler(function (AddonContext $context): void {
                        $context->append([$context->recordCount() . ' records']);
                        $context->append([($context->lineCount() + 1) . ' lines']);
                    });

                    $configurator->withWorkbookConfigurator(function (WorkbookConfigurator $workbook): void {
                        $workbook->setTitle('Workbook title');
                        $workbook->setCreator('Creator');
                        $workbook->setCompany('Company');
                    });

                    $configurator->withWorksheetConfigurator(function (WorksheetConfigurator $worksheet): void {
                        $worksheet->setTitle('results', false);

                        $worksheet->withColumnFormat([
                            'A' => Formats::text(),
                            'B' => Formats::general(),
                        ]);

                        // header
                        $worksheet->freezeFirstRow();
                        $worksheet->configureCells('A1:B1', function ($cells): void {
                            $cells->setFontWeight('bold');
                            $cells->setBorder('node', 'none', 'solid', 'none');
                        });
                        $worksheet->getStyle('A1:B1')->getNumberFormat()->setFormatCode(Formats::text());
                    });
                }),

            'html' => $builder->serializeToHTMLTable(function (HTMLTableSerializerConfigurator $configurator): void {
                $configurator->withHeader(['name', 'email']);
                $configurator->withTrailler(static fn (AddonContext $context) => ['Total', $context->recordCount()]);
                $configurator->withTableClassAttribute('table table-condensed');
                $configurator->withTableIdAttribute('my-table');
            }),

            'json' => $builder->serializeToJSONFile($outputPath . '.json',
                function (JSONFileSerializerConfigurator $configurator): void {
                    $configurator->withEncodeOptions(JSONFileSerializer::JSON_ENCODE_OPTIONS | \JSON_PRETTY_PRINT);
                }),

            'log' => $builder->serializeToLog(function (LogSerializerConfigurator $configurator): void {
                $configurator->withPrefix('PERSIST');
            }),

            'pdo',
            'pdo-buffered' => $this->serializeToPDO($builder, $serializer === 'pdo-buffered'),

            'text' => $builder->serializeToTextFile($outputPath . '.txt'),

            default => throw new \InvalidArgumentException('Invalid serializer: ' . $serializer),
        };
    }

    /**
     * @throws \Throwable
     */
    protected function serializeToPDO(ProcessorBuilder $builder, bool $isBuffered): ProcessorBuilder
    {
        $pdo = $this->makeConnection('output.sqlite');

        return $builder->serializeToPDO($pdo, 'users', ['name', 'email'], $isBuffered);
    }

    protected function storagePath(string $file): string
    {
        return __DIR__ . '/../storage/' . $file;
    }

    /**
     * @throws \Throwable
     */
    protected function makeConnection(string $filename): \PDO
    {
        $filePath = $this->storagePath($filename);

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

        $connection->exec('CREATE TABLE IF NOT EXISTS "users" ("name" TEXT, "email" TEXT)');

        return $connection;
    }

    /**
     * @throws \Throwable
     */
    protected function populateTable(\PDO $connection): void
    {
        if ($this->hasData($connection)) {
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

    protected function hasData(\PDO $connection): bool
    {
        $result = $connection->query('SELECT COUNT(*) FROM "users"', \PDO::FETCH_COLUMN, 0);

        if ($result === false) {
            throw new \RuntimeException('Failed to query sample database');
        }

        return \intval($result->fetchColumn()) > 0;
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
