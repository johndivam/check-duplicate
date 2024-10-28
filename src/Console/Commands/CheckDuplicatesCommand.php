<?php

namespace Johndivam\CheckDuplicate\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckDuplicatesCommand extends Command
{
    protected $signature = 'duplicates:check';
    protected $description = 'Check for duplicate rows based on specified columns across multiple models';

    public function handle()
    {
        $modelsConfig = config('check-duplicates.models');
        
        if (!$modelsConfig || !is_array($modelsConfig)) {
            $this->error('Models configuration is not properly defined.');
            return;
        }

        foreach ($modelsConfig as $config) {
            $modelClass = $config['model'] ?? null;
            $columns = $config['columns'] ?? [];
            $withDeletes = $config['with_deletes'] ?? false;

            if (!$modelClass || empty($columns)) {
                $this->error("Model or columns not configured properly for one of the entries.");
                continue;
            }

            if (!is_array($columns)) {
                $this->error("The columns configuration for model {$modelClass} must be an array.");
                continue;
            }

            // Build the query to find duplicates based on the specified columns
            $query = $modelClass::select($columns)
                ->groupBy($columns)
                ->havingRaw('COUNT(*) > 1');

            // Include soft-deleted records if `with_deletes` is true and the model uses SoftDeletes
            if ($withDeletes && in_array(SoftDeletes::class, class_uses($modelClass))) {
                $query->withTrashed();
            }

            $duplicates = $query->get();

            if ($duplicates->isNotEmpty()) {
                $duplicateDetails = $duplicates->map(function ($duplicate) use ($columns) {
                    return $duplicate->only($columns);
                })->toJson();

                $this->logger("Duplicate entries found in model {$modelClass}. Details: {$duplicateDetails}");
            }
        }
    }

    /**
     * Custom logger method for writing to a specific file.
     */
    protected function logger($message)
    {
        $date = now()->format('Y-m-d');
        $logPath = storage_path("logs/check_duplicates-{$date}.log");
        $logger = new Logger('check_duplicates');
        $logger->pushHandler(new StreamHandler($logPath, Logger::NOTICE));
        $logger->notice($message);
    }
}
