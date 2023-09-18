<?php

declare(strict_types=1);

/**
 * @copyright (c) 2009 - present. Mr Alexandre J-S William ELISÉ. All rights reserved.
 * @license       GNU Affero General Public License v3.0 or later (AGPL-3.0-or-later)
 */

namespace Tests\Benchmark;

use AlexApi\Console\Routefinder\Command\WebServiceRoutesFindCommand;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

final class WebServiceRoutesFindCommandBench
{
    /**
     * #[Bench\Assert('mode(variant.time.avg) < 10ms')]
     */
    public function benchWebServiceRoutesFindCommand()
    {
        $command = new WebServiceRoutesFindCommand();

        $input  = new ArgvInput();
        $output = new ConsoleOutput(OutputInterface::VERBOSITY_QUIET);

        $command->execute($input, $output);
    }
}
