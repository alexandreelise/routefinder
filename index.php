<?php

declare(strict_types=1);

/**
 * @copyright (c) 2009 - present. Mr Alexandre J-S William ELISÉ. All rights reserved.
 * @license       GNU Affero General Public License v3.0 or later (AGPL-3.0-or-later)
 */

use AlexApi\Console\Routefinder\Command\WebServiceRoutesFindCommand;

require_once __DIR__ . '/vendor/autoload.php';

$command = new WebServiceRoutesFindCommand();

$input  = new Symfony\Component\Console\Input\ArgvInput();
$output = new Symfony\Component\Console\Output\ConsoleOutput();

$command->execute($input, $output);
