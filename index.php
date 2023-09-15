<?php

declare(strict_types=1);

/**
 *
 * @author        Mr Alexandre J-S William ELISÉ <code@apiadept.com>
 * @copyright (c) 2009 - present. Mr Alexandre J-S William ELISÉ. All rights reserved.
 * @license       AGPL-3.0-or-later
 * @link          https://apiadept.com
 */

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;

require_once __DIR__ . '/vendor/autoload.php';

$inDir  = __DIR__ . '/extracted/plugins/webservices';
$outDir = __DIR__ . '/dist';
//Force overwrite routes found file
$overwrite = true;
if (empty($inDir)) {
    echo 'inDir is empty. Cannot continue.';

    return;
}

if (empty($outDir)) {
    echo 'outDir is empty. Cannot continue.';

    return;
}

$iterator = new RecursiveDirectoryIterator(
    $inDir,
    FilesystemIterator::SKIP_DOTS | FilesystemIterator::KEY_AS_FILENAME | FilesystemIterator::CURRENT_AS_FILEINFO
);
$iterator = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::LEAVES_ONLY);

$files = [];

foreach ($iterator as $item) {
    /**
     * @var \SplFileInfo $item
     */
    if ($item->getExtension() !== 'php') {
        continue;
    }
    $files[$item->getFilename()] = $item;
}

ksort($files);

echo sprintf('There is %1$d webservices plugins available at the moment', count($files)) . PHP_EOL;
echo implode(PHP_EOL, array_keys($files));

$parser     = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
$nodeFinder = new NodeFinder();

$apiRouterFilename = __DIR__ . '/extracted/libraries/src/Router/ApiRouter.php';

// Fail early to not do useless work
if (!file_exists($apiRouterFilename)) {
    throw new RuntimeException(
        'Required ApiRouter does not seem to exist in libraries directory. Cannot continue', 422
    );
}

try {
    $apiRouterAst     = $parser->parse(file_get_contents($apiRouterFilename));
    $validMethods     = $nodeFinder->find($apiRouterAst, function (Node $node) {
        return (($node instanceof Node\Expr\Assign) && (($node->var->name ?? '') === 'validMethods'));
    });
    $validHttpMethods = array_column(array_column($validMethods[0]->expr->items, 'value'), 'value');

    $routesClassFound = $nodeFinder->find($apiRouterAst, function (Node $node) {
        return ($node instanceof New_)
            && ($node->class->parts !== [])
            && ($node->class->parts[0] === 'Route');
    });
    $routesFoundArgs  = array_column($routesClassFound, 'args');

    $routesCRUD = array_map(
        function ($routeArgItem) {
            $routeRules = [];
            foreach ($routeArgItem[3]->value->items as $rule) {
                $routeRules[$rule->key->value] = $rule->value->value;
            }
            $computedRoutes = [];

            $columsHttpMethods = array_column($routeArgItem[0]->value->items, 'value');

            $pathVariables = $routeArgItem[1]->value->right->value ?? '';

            foreach ($columsHttpMethods as $routeHttpMethod) {
                $computedRoutes[$routeHttpMethod->value] = [
                    'pathVariables' => $pathVariables,
                    'rules'         => $routeRules,
                ];
            }

            return $computedRoutes;
        },
        $routesFoundArgs
    );
} catch (Error $apiRouteError) {
    echo sprintf('%s code parse error %s%s', basename($apiRouterFilename), $apiRouteError->getMessage(), PHP_EOL);
    exit(1);
}
$routesFoundArgKeys = [
];
$routes             = [];
foreach ($files as $currentFile => $currentFileInfo) {
    $routeKey = $currentFileInfo->getBasename('.' . $currentFileInfo->getExtension());

    // skip already processed routes
    if (!empty($routes[$routeKey])) {
        continue;
    }

    $fileObject = $currentFileInfo->openFile();
    $code       = $fileObject->fread($fileObject->getSize());

    try {
        $ast         = $parser->parse($code);
        $routesFound = $nodeFinder->find($ast, function (Node $node) {
            return (($node instanceof String_) && (mb_strpos($node->value, 'v1/', 0) !== false));
        });

        $routes[$routeKey] = array_fill_keys(array_column($routesFound, 'value'), $routesCRUD);
    } catch (Error $error) {
        echo sprintf('Parse error: %s%s', $error->getMessage(), PHP_EOL);

        return;
    }
}
$routesExport = var_export($routes, true);
$output       = <<<CODE
<?php
declare(strict_types=1);

/**
 *
 * @author        Mr Alexandre J-S William ELISÉ <code@apiadept.com>
 * @copyright (c) 2009 - present. Mr Alexandre J-S William ELISÉ. All rights reserved.
 * @license       AGPL-3.0-or-later
 * @link          https://apiadept.com
 */

//exported routes
return $routesExport;

CODE;

$outFile = $outDir . '/routes.php';

try {
    // Keep same file unless overwrite is true or file is modified after 15 minutes (900 seconds)
    if ($overwrite || (filemtime($outFile) + 900) < time()) {
        file_put_contents($outFile, $output);
    }
} catch (Throwable $e) {
    echo sprintf(
        'An error occured when exporting Web Services routes: possible reason %s %s %d %s',
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        PHP_EOL
    );
}
