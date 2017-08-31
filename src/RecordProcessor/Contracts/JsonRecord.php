<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

use Illuminate\Contracts\Support\Jsonable;

interface JsonRecord extends Record, Jsonable
{
}
