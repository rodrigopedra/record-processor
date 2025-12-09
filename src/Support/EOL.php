<?php

namespace RodrigoPedra\RecordProcessor\Support;

enum EOL: string
{
    case WINDOWS = "\r\n";
    case UNIX = "\n";
}
