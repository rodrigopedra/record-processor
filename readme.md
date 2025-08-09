# Record Processor

Process record-based sources using a pipeline approach

```php
<?php
    require __DIR__ . './vendor/autoload.php';

    use RodrigoPedra\RecordProcessor\ProcessorBuilder;
    
    $processor = (new ProcessorBuilder)
        ->readFromCSVFile(__DIR__ . '/storage/input.xlsx')
        ->serializeToHTMLTable()
        ->build();
    
    $output = $processor->process();
    
    echo $output->output(), \PHP_EOL;
    
    exit;
```

## Requirements

- PHP 8.2+
- PDO extension (optional, for database operations)

## Features

- **Pipeline Architecture**: Uses Laravel's Pipeline component for record processing
- **Multiple Data Sources**: CSV, Excel, PDO, Arrays, Collections, Iterators, Text files
- **Multiple Output Formats**: CSV, Excel, HTML, JSON, PDO, Echo, Log, Text files
- **Validation**: Built-in record validation and filtering
- **Aggregation**: Group records by key with customizable aggregation
- **Modern PHP**: Uses PHP 8.2+ features including readonly classes, enums, and type safety

## Development Commands

### Running Examples
```bash
# Run the console application with examples
php console examples <parser> <serializer>

# Available parsers: array|collection|csv|excel|iterator|pdo|text
# Available serializers: array|collection|csv|echo|excel|html|json|log|pdo|pdo-buffered|text

# Example usage:
php console examples csv html
php console examples excel json --log --aggregate
```

### Validation Commands
```bash
# Test all possible combinations of parsers and serializers
php console all-examples

# Test without PDO (if database is not available)
php console all-examples --skip-pdo

# Stop on first error (useful for CI/CD)
php console all-examples --stop-on-error

# Show detailed error messages
php console all-examples --verbose-errors
```

### Download Command
```bash
php console download
```
