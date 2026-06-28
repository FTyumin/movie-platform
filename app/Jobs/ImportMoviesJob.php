<?php

namespace App\Jobs;

use App\Services\ImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportMoviesJob implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1200;
    public function __construct(public int $count, public string $method)
    {
        $this->count = $count;
        $this->method = $method;
    }

    /**
     * Execute the job.
     */
    public function handle(ImportService $movieService): void
    {
        $movieService->importTopMovies($this->count, $this->method);
    }
}
