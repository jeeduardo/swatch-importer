<?php

namespace Josephson\SwatchImporter\Model\Import\Swatch;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class ApiClient
{
    const STATUS_OK = 200;

    const SWATCHIMPORTER_API_PATH = 'api/swatchimporter';

    protected $scopeConfig;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $apiCredentials;

    protected $stack;

    protected $middleware;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;

        $this->initConfig();

        $this->apiCredentials = $this->getApiCredentials();
        $this->stack = HandlerStack::create();
        $this->middleware = new Oauth1($this->apiCredentials);
        $this->stack->push($this->middleware);

        $this->client = new Client([
            'base_uri' => $this->config['base_uri'],
            'handler' => $this->stack,
        ]);
    }

    /**
     * Send POST API request to products/attributes/:attributeCode/swatches (custom API)
     * @param array $optionJson
     * @return boolean
     */
    public function sendPostRequest($optionJson)
    {
        $attributeCode = $optionJson['attribute_code'];
        $json = $optionJson['json'];
        $url = sprintf('products/attributes/%s/swatches', $attributeCode);

        try {
            /** @var \GuzzleHttp\Psr7\Response $response */
            $response = $this->client->request('POST', $url, ['auth' => 'oauth', 'json' => $json]);
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $this->logger->debug('========== ERROR WITH API REQUEST TO ADD SWATCH OPTION! :( ==========');
            $this->logger->debug('MESSAGE : '.$e->getMessage());
            $this->logger->debug($e->getTraceAsString());
            return false;
        }

        return ($response->getStatusCode() == self::STATUS_OK);
    }

    /**
     * Return API credentials
     * @return array
     */
    protected function getApiCredentials()
    {
        if (empty($this->apiCredentials)) {
            $this->apiCredentials = [
                'consumer_key' => $this->config['consumer_key'],
                'consumer_secret' => $this->config['consumer_secret'],
                'token' => $this->config['token'],
                'token_secret' => $this->config['token_secret'],
            ];
        }

        return $this->apiCredentials;
    }

    /**
     * Initialize system config values to be used for the API request
     * @param return array
     */
    protected function initConfig()
    {
        if (!$this->config)
        {
            $this->config = $this->scopeConfig->getValue(self::SWATCHIMPORTER_API_PATH);
        }

        return $this->config;
    }

}
