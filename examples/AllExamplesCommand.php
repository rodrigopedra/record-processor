<?php

namespace RodrigoPedra\RecordProcessor\Examples;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;

class AllExamplesCommand extends ExamplesCommand
{
    protected ?string $currentParser = null;
    protected ?string $currentSerializer = null;

    protected function configure()
    {
        $this->setName('all-examples');
        $this->setDescription('Tests all combinations of parsers and serializers to validate no errors exist');

        $this->addOption('stop-on-error', 's', InputOption::VALUE_NONE, 'Stop execution on first error');
        $this->addOption('verbose-errors', null, InputOption::VALUE_NONE, 'Show detailed error messages');
        $this->addOption('skip-pdo', null, InputOption::VALUE_NONE, 'Skip PDO-related tests');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $parsers = $this->getParsers($input->getOption('skip-pdo'));
        $serializers = $this->getSerializers($input->getOption('skip-pdo'));

        $totalCombinations = count($parsers) * count($serializers);
        $results = [];
        $errors = [];

        $output->writeln("<info>Testing {$totalCombinations} combinations...</info>");
        $output->writeln("<comment>Parsers: " . implode(', ', $parsers) . "</comment>");
        $output->writeln("<comment>Serializers: " . implode(', ', $serializers) . "</comment>");
        $output->writeln('');

        $progressBar = new ProgressBar($output, $totalCombinations);
        $progressBar->setFormat('verbose');
        $progressBar->start();

        foreach ($parsers as $parser) {
            foreach ($serializers as $serializer) {
                $combination = "{$parser} → {$serializer}";

                try {
                    // Set current context for file naming
                    $this->currentParser = $parser;
                    $this->currentSerializer = $serializer;
                    
                    $this->testCombination($parser, $serializer);
                    $results[] = ['combination' => $combination, 'status' => 'SUCCESS', 'error' => null];
                    $progressBar->setMessage("✅ {$combination}", 'status');
                } catch (\Throwable $e) {
                    $errorMessage = $e->getMessage();
                    $results[] = ['combination' => $combination, 'status' => 'FAILED', 'error' => $errorMessage];
                    $errors[] = ['combination' => $combination, 'error' => $errorMessage];

                    $progressBar->setMessage("❌ {$combination}", 'status');

                    if ($input->getOption('stop-on-error')) {
                        $progressBar->finish();
                        $output->writeln('');
                        $output->writeln("<error>Stopped on first error: {$combination}</error>");
                        if ($input->getOption('verbose-errors')) {
                            $output->writeln("<error>{$errorMessage}</error>");
                        }
                        return Command::FAILURE;
                    }
                } finally {
                    // Clear context after test
                    $this->currentParser = null;
                    $this->currentSerializer = null;
                }

                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $output->writeln('');
        $output->writeln('');

        $this->displayResults($output, $results, $errors, $input->getOption('verbose-errors'));

        return empty($errors) ? Command::SUCCESS : Command::FAILURE;
    }

    protected function getParsers(bool $skipPdo): array
    {
        $parsers = ['array', 'collection', 'csv', 'excel', 'iterator', 'text'];

        if (!$skipPdo) {
            $parsers[] = 'pdo';
        }

        return $parsers;
    }

    protected function getSerializers(bool $skipPdo): array
    {
        $serializers = ['array', 'collection', 'csv', 'echo', 'excel', 'html', 'json', 'log', 'text'];

        if (!$skipPdo) {
            $serializers[] = 'pdo';
            $serializers[] = 'pdo-buffered';
        }

        return $serializers;
    }

    protected function testCombination(string $parser, string $serializer): void
    {
        // Capture output to prevent verbose testing output
        ob_start();

        try {
            // Create a minimal test to verify the combination works
            $builder = $this->makeBuilder();

            // Use a null logger to suppress output during testing
            $builder->setLogger(new class implements \Psr\Log\LoggerInterface {
                public function emergency($message, array $context = []): void {}
                public function alert($message, array $context = []): void {}
                public function critical($message, array $context = []): void {}
                public function error($message, array $context = []): void {}
                public function warning($message, array $context = []): void {}
                public function notice($message, array $context = []): void {}
                public function info($message, array $context = []): void {}
                public function debug($message, array $context = []): void {}
                public function log($level, $message, array $context = []): void {}
            });

            $builder->withRecordParser(new RecordObjects\ExampleRecordParser());
            $builder->withRecordSerializer(new RecordObjects\ExampleRecordSerializer());

            $this->readFrom($builder, $parser);

            $builder->onlyValidRecords();

            if (in_array($serializer, ['echo', 'log'])) {
                $builder->serializeToArray();
            } else {
                $this->serializeTo($builder, $serializer);
            }

            $processor = $builder->build();
            $result = $processor->process();

            if ($result->inputRecordCount() === 0) {
                throw new \RuntimeException('No records were processed');
            }
        } finally {
            ob_end_clean();
        }
    }

    protected function displayResults(OutputInterface $output, array $results, array $errors, bool $verboseErrors): void
    {
        $successCount = count($results) - count($errors);
        $failureCount = count($errors);
        $totalCount = count($results);

        // Summary
        $output->writeln("<info>Results Summary:</info>");
        $output->writeln("✅ Success: {$successCount}/{$totalCount}");
        $output->writeln("❌ Failed: {$failureCount}/{$totalCount}");
        $output->writeln('');

        if (!empty($errors)) {
            $output->writeln("<error>Failed Combinations:</error>");

            $table = new Table($output);
            $table->setHeaders(['Parser → Serializer', 'Error']);

            foreach ($errors as $error) {
                $errorMsg = $verboseErrors ? $error['error'] : $this->truncateError($error['error']);
                $table->addRow([$error['combination'], $errorMsg]);
            }

            $table->render();
            $output->writeln('');
        }

        $successRate = ($successCount / $totalCount) * 100;
        $status = $successRate === 100.0 ? 'info' : ($successRate >= 80.0 ? 'comment' : 'error');
        $output->writeln("<{$status}>Success Rate: " . number_format($successRate, 1) . "%</{$status}>");
    }

    protected function truncateError(string $error, int $maxLength = 80): string
    {
        if (strlen($error) <= $maxLength) {
            return $error;
        }

        return substr($error, 0, $maxLength - 3) . '...';
    }

    protected function storagePath(string $file): string
    {
        if (str_starts_with($file, 'input')) {
            return parent::storagePath($file);
        }

        // Get the current parser and serializer from the test context
        $parser = $this->currentParser ?? 'unknown';
        $serializer = $this->currentSerializer ?? 'unknown';
        
        $pathInfo = pathinfo($file);
        $filename = $parser . '_' . $serializer;
        $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';

        return __DIR__ . '/../storage/' . $filename . $extension;
    }
}
