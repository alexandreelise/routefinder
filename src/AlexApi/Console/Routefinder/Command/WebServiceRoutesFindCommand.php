<?php

declare(strict_types=1);
/**
 * @copyright (c) 2009 - present. Mr Alexandre J-S William ELISÉ. All rights reserved.
 * @license       GNU Affero General Public License v3.0 or later (AGPL-3.0-or-later)
 */

namespace AlexApi\Console\Routefinder\Command;

use DomainException;
use Error;
use FilesystemIterator;
use Joomla\Console\Command\AbstractCommand;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

use function array_column;
use function array_fill_keys;
use function array_keys;
use function array_map;
use function basename;
use function count;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function filemtime;
use function implode;
use function ksort;
use function sprintf;
use function time;
use function var_export;

use const APP_DIRECTORY;
use const PHP_EOL;

final class WebServiceRoutesFindCommand extends AbstractCommand
{

	/**
	 * The default command name
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	protected static $defaultName = 'routefinder:webservices:routes:find';

	private InputInterface|null $input = null;

	private OutputInterface|null $output = null;


	private const ASCII_BANNER = <<<TEXT
    __  __     ____         _____                              __                      __              
   / / / ___  / / ____     / ___/__  ______  ___  _____       / ____  ____  ____ ___  / ___  __________
  / /_/ / _ \/ / / __ \    \__ \/ / / / __ \/ _ \/ ___/  __  / / __ \/ __ \/ __ `__ \/ / _ \/ ___/ ___/
 / __  /  __/ / / /_/ /   ___/ / /_/ / /_/ /  __/ /     / /_/ / /_/ / /_/ / / / / / / /  __/ /  (__  ) 
/_/ /_/\___/_/_/\____/   /____/\__,_/ .___/\___/_/      \____/\____/\____/_/ /_/ /_/_/\___/_/  /____/  
                                   /_/                                                                 
TEXT;

	/**
	 * @inheritDoc
	 */
	protected function doExecute(InputInterface $input, OutputInterface $output): int
	{
		$this->input  = $input;
		$this->output = $output;

		$symfonyStyle = new SymfonyStyle($input, $output);

		// Show ASCII BANNER Before running the script
		$output->writeln(self::ASCII_BANNER);

		$symfonyStyle->title('Routefinder');
		try
		{
			$inDir             = APP_DIRECTORY . '/extracted/plugins/webservices';
			$outDir            = APP_DIRECTORY . '/dist';
			$apiRouterFilename = APP_DIRECTORY . '/extracted/libraries/src/Router/ApiRouter.php';

			$result = $this->find($inDir, $outDir, $apiRouterFilename);

			if ($result === false)
			{
				throw new DomainException('Could not write found Joomla Web Services routes to file', 500);
			}

			if (!empty($result))
			{
				$symfonyStyle->success(sprintf('%d bytes written to %s/routes.php', $result, $outDir));

				return Command::SUCCESS;
			}

			return Command::FAILURE;
		}
		catch (Throwable $e)
		{
			$symfonyStyle->error(
				sprintf(
					'The Web Services routes could not be extracted. The operation might have failed or your platform is not supported. Possible reasons: %s %d %s %s',
					$e->getMessage(),
					$e->getLine(),
					$e->getTraceAsString(),
					$e->getPrevious() ? $e->getPrevious()->getTraceAsString() : ''
				)
			);
		}

		return Command::FAILURE;
	}

	private function find(string $inDir, string $outDir, string $apiRouterFilename, bool $overwrite = false)
	{
		//Force overwrite routes found file
		$overwrite = true;
		if (empty($inDir))
		{
			throw new DomainException('inDir is empty. Cannot continue.', 422);
		}

		if (empty($outDir))
		{
			throw new DomainException('outDir is empty. Cannot continue.', 422);
		}

		$iterator = new RecursiveDirectoryIterator(
			$inDir,
			FilesystemIterator::SKIP_DOTS | FilesystemIterator::KEY_AS_FILENAME | FilesystemIterator::CURRENT_AS_FILEINFO
		);
		$iterator = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::LEAVES_ONLY);

		$files = [];

		foreach ($iterator as $item)
		{
			/**
			 * @var SplFileInfo $item
			 */
			if ($item->getExtension() !== 'php')
			{
				continue;
			}
			$files[$item->getFilename()] = $item;
		}

		ksort($files);

		$this->output->writeln(sprintf('There is %1$d webservices plugins available at the moment', count($files)));
		$this->output->writeln(implode(PHP_EOL, array_keys($files)));

        $parser = (new ParserFactory())->createForNewestSupportedVersion();
		$nodeFinder = new NodeFinder();


// Fail early to not do useless work
		if (!file_exists($apiRouterFilename))
		{
			throw new RuntimeException(
				'Required ApiRouter does not seem to exist in libraries directory. Cannot continue', 422
			);
		}

		try
		{
			$apiRouterAst     = $parser->parse(file_get_contents($apiRouterFilename));
			$validMethods     = $nodeFinder->find($apiRouterAst, function (Node $node) {
				return (($node instanceof Node\Expr\Assign) && (($node->var->name ?? '') === 'validMethods'));
			});
			$validHttpMethods = array_column(array_column($validMethods[0]->expr->items, 'value'), 'value');

			$routesClassFound = $nodeFinder->find($apiRouterAst, function (Node $node) {
				return ($node instanceof New_)
					&& ($node->class->getParts() !== [])
					&& ($node->class->getParts()[0] === 'Route');
			});
			$routesFoundArgs  = array_column($routesClassFound, 'args');

			$routesCRUD = array_map(
				function ($routeArgItem) {
					$routeRules = [];
					foreach ($routeArgItem[3]->value->items as $rule)
					{
						$routeRules[$rule->key->value] = $rule->value->value;
					}
					$computedRoutes = [];

					$columnsHttpMethods = array_column($routeArgItem[0]->value->items, 'value');

					$pathVariables = $routeArgItem[1]->value->right->value ?? '';

					foreach ($columnsHttpMethods as $routeHttpMethod)
					{
						$computedRoutes[$routeHttpMethod->value] = [
							'pathVariables' => $pathVariables,
							'rules'         => $routeRules,
						];
					}

					return $computedRoutes;
				},
				$routesFoundArgs
			);
		}
		catch (Error $apiRouteError)
		{
			throw new Error(
				sprintf(
					'%s code parse error %s',
					basename($apiRouterFilename),
					$apiRouteError->getMessage()
				)
			);
		}
		$routesFoundArgKeys = [
		];
		$routes             = [];
		foreach ($files as $currentFileInfo)
		{
			$routeKey = $currentFileInfo->getBasename('.' . $currentFileInfo->getExtension());

			// skip already processed routes
			if (!empty($routes[$routeKey]))
			{
				continue;
			}

			$fileObject = $currentFileInfo->openFile();
			$code       = $fileObject->fread($fileObject->getSize());

			try
			{
				$ast         = $parser->parse($code);
				$routesFound = $nodeFinder->find($ast, function (Node $node) {
					return (($node instanceof String_) && (mb_strpos($node->value, 'v1/', 0) !== false));
				});

				$routes[$routeKey] = array_fill_keys(array_column($routesFound, 'value'), $routesCRUD);
			}
			catch (Error $error)
			{
				throw new Error(sprintf('Parse error: %s', $error->getMessage()));
			}
		}
		$routesExport = var_export($routes, true);
		$output       = <<<CODE
<?php
declare(strict_types=1);

//exported routes
return $routesExport;

CODE;

		$outFile = $outDir . '/routes.php';

		try
		{
			// Keep same file unless overwrite is true or file is modified after 15 minutes (900 seconds)
			if ($overwrite || (filemtime($outFile) + 900) < time())
			{
				return file_put_contents($outFile, $output);
			}
		}
		catch (Throwable $e)
		{
			throw new DomainException(
				sprintf(
					'An error occured when exporting Web Services routes: possible reason %s %s %d',
					$e->getMessage(),
					$e->getFile(),
					$e->getLine()
				)
			);
		}

		return false;
	}
}
