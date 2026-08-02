<?php

namespace App\Jobs;

use App\Models\Report;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateReport implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $type,
        public string $title,
        public array $parameters,
        public int $generatedBy,
    ) {}

    public function handle(): void
    {
        Report::create([
            'type' => $this->type,
            'title' => $this->title,
            'parameters' => $this->parameters,
            'generated_by' => $this->generatedBy,
            'file_path' => null,
        ]);
    }
}
