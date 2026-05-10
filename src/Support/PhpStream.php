<?php

namespace RodrigoPedra\RecordProcessor\Support;

enum PhpStream: string
{
    case OUTPUT = 'php://output';
    case TEMP = 'php://temp';
    case MEMORY = 'php://memory';

    public static function isOutputFile(\SplFileInfo|string $pathName): bool
    {
        if ($pathName instanceof \SplFileInfo) {
            $pathName = $pathName->getPathname();
        }

        return \str_starts_with($pathName, self::OUTPUT->value);
    }

    public static function isTempFile(\SplFileInfo|string $pathName): bool
    {
        if ($pathName instanceof \SplFileInfo) {
            $pathName = $pathName->getPathname();
        }

        return \str_starts_with($pathName, self::TEMP->value);
    }

    public static function isMemoryFile(\SplFileInfo|string $pathName): bool
    {
        if ($pathName instanceof \SplFileInfo) {
            $pathName = $pathName->getPathname();
        }

        return \str_starts_with($pathName, self::MEMORY->value);
    }
}
