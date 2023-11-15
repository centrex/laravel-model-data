<?php

declare(strict_types=1);

namespace Centrex\ModelData\Commands;

use Illuminate\Console\Command;

class ModelDataCommand extends Command
{
    public $signature = 'model-data';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
