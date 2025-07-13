<?php

namespace Mgamal92\FilamentDemoGenerator\Events;

use Illuminate\Queue\SerializesModels;

class DemoDataGenerated
{
    use SerializesModels;

    public string $model;
    public int $count;
    public array $ids;

    public function __construct(string $model, int $count, array $ids)
    {
        $this->model = $model;
        $this->count = $count;
        $this->ids = $ids;
    }
}
