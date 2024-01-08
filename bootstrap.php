<?php

declare(strict_types=1);

/**
 * @copyright (c) 2009 - present. Mr Alexandre J-S William ELISÉ. All rights reserved.
 * @license       GNU Affero General Public License v3.0 or later (AGPL-3.0-or-later)
 */

ini_set('error_reporting', -1);
defined('APP_DIRECTORY') || define('APP_DIRECTORY', __DIR__);
defined('API_CONFIG_INI') || define('API_CONFIG_INI', __DIR__ . '/api-config.ini');

ini_set('memory_limit', '256M');

require_once __DIR__ . '/vendor/autoload.php';
