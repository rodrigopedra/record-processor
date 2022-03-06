<?php

namespace RodrigoPedra\RecordProcessor\Concerns;

trait HasPrefix
{
    protected ?string $prefix = null;

    public function prefix(): ?string
    {
        return $this->prefix;
    }

    public function withPrefix(?string $prefix = null): static
    {
        $this->prefix = \blank($prefix) ? null : $prefix;

        return $this;
    }
}
