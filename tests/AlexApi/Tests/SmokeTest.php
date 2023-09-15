<?php

namespace AlexApi\Tests;

use Generator;
use Joomla\Http\Response;
use Joomla\Http\Transport\Curl;
use Joomla\Http\Transport\Stream;
use Joomla\Http\TransportInterface;
use Joomla\Uri\Uri;
use PHPUnit\Framework\TestCase;

use function http_build_query;
use function parse_ini_file;
use function sprintf;
use function str_replace;
use function trim;

use const API_CONFIG_INI;
use const APP_DIRECTORY;
use const CURLOPT_CAINFO;

class SmokeTest extends TestCase
{
    private TransportInterface $client;
    private static array $apiConfig = [];

    private static array $endpointCapabilities = [];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$apiConfig = parse_ini_file(API_CONFIG_INI, true);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        self::$apiConfig = [];
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->client = new Curl(
            [
                'transport.curl' => [
                    CURLOPT_CAINFO => '/etc/nginx/ssl/routefinder.test.certificate.pem',
                ],
            ]
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
        echo print_r(self::$endpointCapabilities, true) . PHP_EOL;
        unset($this->client);
    }

    public function testStreamTransportIsSupported()
    {
        $this->assertTrue(Stream::isSupported());
    }

    public function testCurlTransportIsSupported()
    {
        $this->assertTrue(Curl::isSupported());
    }

    public function testUrlProviderIsValid()
    {
        $actual = $this->getUrlMap();

        $expected = [
            'v1/banners'                                  =>
                [
                    0 => 'v1/banners',
                    1 => '',
                ],
            'v1/banners/clients'                          =>
                [
                    0 => 'v1/banners/clients',
                    1 => '',
                ],
            'v1/banners/categories'                       =>
                [
                    0 => 'v1/banners/categories',
                    1 => '',
                ],
            'v1/banners/:id/contenthistory'               =>
                [
                    0 => 'v1/banners/:id/contenthistory',
                    1 => '',
                ],
            'v1/banners/:id/contenthistory/keep'          =>
                [
                    0 => 'v1/banners/:id/contenthistory/keep',
                    1 => '',
                ],
            'v1/config/application'                       =>
                [
                    0 => 'v1/config/application',
                    1 => '',
                ],
            'v1/config/com_banners'                       =>
                [
                    0 => 'v1/config/com_banners',
                    1 => '',
                ],
            'v1/config/com_config'                        =>
                [
                    0 => 'v1/config/com_config',
                    1 => '',
                ],
            'v1/config/com_contact'                       =>
                [
                    0 => 'v1/config/com_contact',
                    1 => '',
                ],
            'v1/config/com_content'                       =>
                [
                    0 => 'v1/config/com_content',
                    1 => '',
                ],
            'v1/config/com_installer'                     =>
                [
                    0 => 'v1/config/com_installer',
                    1 => '',
                ],
            'v1/config/com_languages'                     =>
                [
                    0 => 'v1/config/com_languages',
                    1 => '',
                ],
            'v1/config/com_media'                         =>
                [
                    0 => 'v1/config/com_media',
                    1 => '',
                ],
            'v1/config/com_menus'                         =>
                [
                    0 => 'v1/config/com_menus',
                    1 => '',
                ],
            'v1/config/com_messages'                      =>
                [
                    0 => 'v1/config/com_messages',
                    1 => '',
                ],
            'v1/config/com_modules'                       =>
                [
                    0 => 'v1/config/com_modules',
                    1 => '',
                ],
            'v1/config/com_newsfeeds'                     =>
                [
                    0 => 'v1/config/com_newsfeeds',
                    1 => '',
                ],
            'v1/config/com_plugins'                       =>
                [
                    0 => 'v1/config/com_plugins',
                    1 => '',
                ],
            'v1/config/com_privacy'                       =>
                [
                    0 => 'v1/config/com_privacy',
                    1 => '',
                ],
            'v1/config/com_redirect'                      =>
                [
                    0 => 'v1/config/com_redirect',
                    1 => '',
                ],
            'v1/config/com_tags'                          =>
                [
                    0 => 'v1/config/com_tags',
                    1 => '',
                ],
            'v1/config/com_templates'                     =>
                [
                    0 => 'v1/config/com_templates',
                    1 => '',
                ],
            'v1/config/com_users'                         =>
                [
                    0 => 'v1/config/com_users',
                    1 => '',
                ],
            'v1/contacts/form/:id'                        =>
                [
                    0 => 'v1/contacts/form/:id',
                    1 => '',
                ],
            'v1/contacts'                                 =>
                [
                    0 => 'v1/contacts',
                    1 => '',
                ],
            'v1/contacts/categories'                      =>
                [
                    0 => 'v1/contacts/categories',
                    1 => '',
                ],
            'v1/fields/contacts/contact'                  =>
                [
                    0 => 'v1/fields/contacts/contact',
                    1 => '',
                ],
            'v1/fields/contacts/mail'                     =>
                [
                    0 => 'v1/fields/contacts/mail',
                    1 => '',
                ],
            'v1/fields/contacts/categories'               =>
                [
                    0 => 'v1/fields/contacts/categories',
                    1 => '',
                ],
            'v1/fields/groups/contacts/contact'           =>
                [
                    0 => 'v1/fields/groups/contacts/contact',
                    1 => '',
                ],
            'v1/fields/groups/contacts/mail'              =>
                [
                    0 => 'v1/fields/groups/contacts/mail',
                    1 => '',
                ],
            'v1/fields/groups/contacts/categories'        =>
                [
                    0 => 'v1/fields/groups/contacts/categories',
                    1 => '',
                ],
            'v1/contacts/:id/contenthistory'              =>
                [
                    0 => 'v1/contacts/:id/contenthistory',
                    1 => '',
                ],
            'v1/contacts/:id/contenthistory/keep'         =>
                [
                    0 => 'v1/contacts/:id/contenthistory/keep',
                    1 => '',
                ],
            'v1/content/articles'                         =>
                [
                    0 => 'v1/content/articles',
                    1 => '',
                ],
            'v1/content/categories'                       =>
                [
                    0 => 'v1/content/categories',
                    1 => '',
                ],
            'v1/fields/content/articles'                  =>
                [
                    0 => 'v1/fields/content/articles',
                    1 => '',
                ],
            'v1/fields/content/categories'                =>
                [
                    0 => 'v1/fields/content/categories',
                    1 => '',
                ],
            'v1/fields/groups/content/articles'           =>
                [
                    0 => 'v1/fields/groups/content/articles',
                    1 => '',
                ],
            'v1/fields/groups/content/categories'         =>
                [
                    0 => 'v1/fields/groups/content/categories',
                    1 => '',
                ],
            'v1/content/articles/:id/contenthistory'      =>
                [
                    0 => 'v1/content/articles/:id/contenthistory',
                    1 => '',
                ],
            'v1/content/articles/:id/contenthistory/keep' =>
                [
                    0 => 'v1/content/articles/:id/contenthistory/keep',
                    1 => '',
                ],
            'v1/extensions'                               =>
                [
                    0 => 'v1/extensions',
                    1 => '',
                ],
            'v1/languages/content'                        =>
                [
                    0 => 'v1/languages/content',
                    1 => '',
                ],
            'v1/languages/overrides/search'               =>
                [
                    0 => 'v1/languages/overrides/search',
                    1 => '',
                ],
            'v1/languages/overrides/search/cache/refresh' =>
                [
                    0 => 'v1/languages/overrides/search/cache/refresh',
                    1 => '',
                ],
            'v1/languages/overrides/site/'                =>
                [
                    0 => 'v1/languages/overrides/site/',
                    1 => '',
                ],
            'v1/languages/overrides/administrator/'       =>
                [
                    0 => 'v1/languages/overrides/administrator/',
                    1 => '',
                ],
            'v1/languages'                                =>
                [
                    0 => 'v1/languages',
                    1 => '',
                ],
            'v1/media/adapters'                           =>
                [
                    0 => 'v1/media/adapters',
                    1 => '',
                ],
            'v1/media/files'                              =>
                [
                    0 => 'v1/media/files',
                    1 => '',
                ],
            'v1/menus/site'                               =>
                [
                    0 => 'v1/menus/site',
                    1 => '',
                ],
            'v1/menus/administrator'                      =>
                [
                    0 => 'v1/menus/administrator',
                    1 => '',
                ],
            'v1/menus/site/items'                         =>
                [
                    0 => 'v1/menus/site/items',
                    1 => '',
                ],
            'v1/menus/administrator/items'                =>
                [
                    0 => 'v1/menus/administrator/items',
                    1 => '',
                ],
            'v1/menus/site/items/types'                   =>
                [
                    0 => 'v1/menus/site/items/types',
                    1 => '',
                ],
            'v1/menus/administrator/items/types'          =>
                [
                    0 => 'v1/menus/administrator/items/types',
                    1 => '',
                ],
            'v1/messages'                                 =>
                [
                    0 => 'v1/messages',
                    1 => '',
                ],
            'v1/modules/types/site'                       =>
                [
                    0 => 'v1/modules/types/site',
                    1 => '',
                ],
            'v1/modules/types/administrator'              =>
                [
                    0 => 'v1/modules/types/administrator',
                    1 => '',
                ],
            'v1/modules/site'                             =>
                [
                    0 => 'v1/modules/site',
                    1 => '',
                ],
            'v1/modules/administrator'                    =>
                [
                    0 => 'v1/modules/administrator',
                    1 => '',
                ],
            'v1/newsfeeds/feeds'                          =>
                [
                    0 => 'v1/newsfeeds/feeds',
                    1 => '',
                ],
            'v1/newsfeeds/categories'                     =>
                [
                    0 => 'v1/newsfeeds/categories',
                    1 => '',
                ],
            'v1/plugins'                                  =>
                [
                    0 => 'v1/plugins',
                    1 => '',
                ],
            'v1/plugins/:id'                              =>
                [
                    0 => 'v1/plugins/:id',
                    1 => '',
                ],
            'v1/privacy/requests'                         =>
                [
                    0 => 'v1/privacy/requests',
                    1 => '',
                ],
            'v1/privacy/requests/:id'                     =>
                [
                    0 => 'v1/privacy/requests/:id',
                    1 => '',
                ],
            'v1/privacy/requests/export/:id'              =>
                [
                    0 => 'v1/privacy/requests/export/:id',
                    1 => '',
                ],
            'v1/privacy/consents'                         =>
                [
                    0 => 'v1/privacy/consents',
                    1 => '',
                ],
            'v1/privacy/consents/:id'                     =>
                [
                    0 => 'v1/privacy/consents/:id',
                    1 => '',
                ],
            'v1/redirects'                                =>
                [
                    0 => 'v1/redirects',
                    1 => '',
                ],
            'v1/tags'                                     =>
                [
                    0 => 'v1/tags',
                    1 => '',
                ],
            'v1/templates/styles/site'                    =>
                [
                    0 => 'v1/templates/styles/site',
                    1 => '',
                ],
            'v1/templates/styles/administrator'           =>
                [
                    0 => 'v1/templates/styles/administrator',
                    1 => '',
                ],
            'v1/users'                                    =>
                [
                    0 => 'v1/users',
                    1 => '',
                ],
            'v1/users/groups'                             =>
                [
                    0 => 'v1/users/groups',
                    1 => '',
                ],
            'v1/users/levels'                             =>
                [
                    0 => 'v1/users/levels',
                    1 => '',
                ],
            'v1/fields/users'                             =>
                [
                    0 => 'v1/fields/users',
                    1 => '',
                ],
            'v1/fields/groups/users'                      =>
                [
                    0 => 'v1/fields/groups/users',
                    1 => '',
                ],
        ];
        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider urlProvider
     */
    public function testPageEndpointCapabilities(string $path, string $query)
    {
        $baseUrl  = 'https://routefinder.test';
        $basePath = '/api/index.php';
        $uri      = new Uri($baseUrl);

        $variables = [
            ':id' => '',
        ];

        $pathModified  = $this->replaceVariables($path, $variables);
        $queryModified = $this->replaceVariables($query, $variables);

        $uri->setPath(sprintf('%s/%s', $basePath, $pathModified));
        $uri->setQuery($queryModified);
        $userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36';
        // Don't send payload to server
        $dataString = null;
        // HTTP request headers
        $headers = [
            'Accept'         => 'application/vnd.api+json',
            'X-Joomla-Token' => trim(self::$apiConfig['routefinder']['JOOMLA_API_TOKEN_1']),
        ];
        // Timeout in seconds
        $timeout   = 1;
        $generator = $this->asyncRequest('OPTIONS', $uri, $dataString, $headers, $timeout, $userAgent);
        /**
         * @var Response $response
         */
        foreach ($generator as $response) {
            if ($response->getStatusCode() === 204) {
                self::$endpointCapabilities[$pathModified] = $response->getHeaders();
            }

            $this->assertSame(
                204,
                $response->getStatusCode(),
                sprintf('Unexpected response %d for uri: %s', $response->getStatusCode(), $uri->toString())
            );
        }
    }


    /**
     * @dataProvider urlProvider
     */
    public function testPageHasBadRequest(string $path, string $query)
    {
        $baseUrl  = 'https://routefinder.test';
        $basePath = '/api/index.php';
        $uri      = new Uri($baseUrl);

        $variables = [
            ':id' => '',
        ];

        $query = http_build_query([
            'filter' => [
                'state' => 'bad_request',
            ],
        ]);

        $pathModified  = $this->replaceVariables($path, $variables);
        $queryModified = $this->replaceVariables($query, $variables);

        $uri->setPath(sprintf('%s/%s', $basePath, $pathModified));
        $uri->setQuery($queryModified);
        $userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36';
        // Don't send payload to server
        $dataString = null;
        // HTTP request headers
        $headers = [
            'Accept'         => 'application/vnd.api+json',
            'X-Joomla-Token' => trim(self::$apiConfig['routefinder']['JOOMLA_API_TOKEN_1']),
        ];
        // Timeout in seconds
        $timeout   = 1;
        $generator = $this->asyncRequest('GET', $uri, $dataString, $headers, $timeout, $userAgent);
        foreach ($generator as $response) {
            $this->assertSame(
                400,
                $response->getStatusCode(),
                sprintf('Unexpected response %d for uri: %s', $response->getStatusCode(), $uri->toString())
            );
        }
    }


    /**
     * @dataProvider urlProvider
     */
    public function testPageIsNotFound(string $path, string $query)
    {
        $baseUrl  = 'https://routefinder.test';
        $basePath = '/api/index.php';
        $uri      = new Uri($baseUrl);

        $path = 'notfound';

        $variables = [
            ':id' => '',
        ];

        $pathModified  = $this->replaceVariables($path, $variables);
        $queryModified = $this->replaceVariables($query, $variables);

        $uri->setPath(sprintf('%s/%s', $basePath, $pathModified));
        $uri->setQuery($queryModified);
        $userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36';
        // Don't send payload to server
        $dataString = null;
        // HTTP request headers
        $headers = [
            'Accept'         => 'application/vnd.api+json',
            'X-Joomla-Token' => trim(self::$apiConfig['routefinder']['JOOMLA_API_TOKEN_1']),
        ];
        // Timeout in seconds
        $timeout   = 1;
        $generator = $this->asyncRequest('GET', $uri, $dataString, $headers, $timeout, $userAgent);
        foreach ($generator as $response) {
            $this->assertSame(
                404,
                $response->getStatusCode(),
                sprintf('Unexpected response %d for uri: %s', $response->getStatusCode(), $uri->toString())
            );
        }
    }


    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful(string $path, string $query)
    {
        $baseUrl  = 'https://routefinder.test';
        $basePath = '/api/index.php';
        $uri      = new Uri($baseUrl);

        $variables = [
            ':id' => '',
        ];

        $pathModified  = $this->replaceVariables($path, $variables);
        $queryModified = $this->replaceVariables($query, $variables);

        $uri->setPath(sprintf('%s/%s', $basePath, $pathModified));
        $uri->setQuery($queryModified);
        $userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36';
        // Don't send payload to server
        $dataString = null;
        // HTTP request headers
        $headers = [
            'Accept'         => 'application/vnd.api+json',
            'X-Joomla-Token' => trim(self::$apiConfig['routefinder']['JOOMLA_API_TOKEN_1']),
        ];
        // Timeout in seconds
        $timeout   = 1;
        $generator = $this->asyncRequest('GET', $uri, $dataString, $headers, $timeout, $userAgent);
        foreach ($generator as $response) {
            $this->assertSame(
                200,
                $response->getStatusCode(),
                sprintf('Unexpected response %d for uri: %s', $response->getStatusCode(), $uri->toString())
            );
        }
    }

    /**
     * @return array
     */
    public function urlProvider(): array
    {
        return $this->getUrlMap();
    }

    private function asyncRequest($verb, $uri, $dataString, $headers, $timeout, $userAgent): Generator
    {
        yield $this->client->request(strtoupper($verb), $uri, $dataString, $headers, $timeout, $userAgent);
    }


    /**
     * @return array<array<string, string>>
     */
    private function getUrlMap(): array
    {
        $routes      = require APP_DIRECTORY . '/dist/routes.php';
        $urlRouteMap = [];
        $components  = array_keys($routes);
        foreach ($routes as $routeCollection) {
            foreach ($routeCollection as $routeItem => $crud) {
                foreach ($components as $component) {
                    $urlRouteMapKey               = str_replace(
                        ':component_name',
                        sprintf('com_%s', $component),
                        $routeItem
                    );
                    $urlRouteMap[$urlRouteMapKey] = [$urlRouteMapKey, ''];
                }
            }
        }

        return $urlRouteMap;
    }

    private function replaceVariables(string $subject, array $variables)
    {
        return str_replace(array_keys($variables), array_values($variables), $subject);
    }
}
