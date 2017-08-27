# Record Processor

Process record-based sources using a pipeline approach

```php
<?php
    require __DIR__ . './vendor/autoload.php';

    use RodrigoPedra\RecordProcessor\ProcessorBuilder;
    
    $processor = ( new ProcessorBuilder )
        ->readFromExcel( __DIR__ . '/storage/input.xlsx' )
        ->writeToHTMLTable()
        ->build();
    
    $html = $processor->process();
    
    echo $html->getOutput();
```

## TODO

Add documentation
