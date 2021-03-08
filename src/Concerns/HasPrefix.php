<?php

namespace RodrigoPedra\RecordProcessor\Concerns;

trait HasPrefix
{
    protected ?string $prefix;

    public function prefix(): string
    {
        return $this->prefix;
    }

    public function withPrefix(?string $prefix = null): self
    {
        $this->prefix = \blank($prefix) ? null : $prefix;

        return $this;
    }
}
