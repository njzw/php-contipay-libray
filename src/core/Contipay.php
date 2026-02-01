<?php

namespace Contipay\Core;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Nigel\Utils\Core\Checksums\ContipayChecksum;

class Contipay
{
    /**
     * @var string API token for authentication
     */
    protected string $token;
    /**
     * @var string API secret for authentication
     */
    protected string $secret;
    /**
     * @var string Current base URL (UAT or Live)
     */
    protected string $url;
    /**
     * @var string HTTP method for payment requests (POST or PUT)
     */
    protected string $paymentMethod = 'POST';
    /**
     * @var string Endpoint for acquiring payments
     */
    protected string $acquireUrl = 'acquire/payment';
    /**
     * @var string Endpoint for disbursing payments
     */
    protected string $disburseUrl = 'disburse/payment';
    /**
     * @var string UAT (test) API URL
     */
    protected string $uatURL = 'https://api-uat.contipay.net';
    /**
     * @var string Live API URL
     */
    protected string $liveURL = 'https://api.contipay.net';
    /**
     * @var Client|null Guzzle HTTP client instance
     */
    protected ?Client $client = null;
    /**
     * @var string Last generated checksum
     */
    protected string $checksum;

    /**
     * Contipay constructor.
     *
     * @param string $token  API token
     * @param string $secret API secret
     */
    public function __construct(string $token, string $secret)
    {
        $this->token = $token;
        $this->secret = $secret;
    }

    /**
     * Set ContiPay environment mode (UAT or Live).
     *
     * @param string $mode  'DEV' for UAT, 'LIVE' for production
     * @return self
     */
    public function setAppMode(string $mode = 'DEV'): self
    {
        $this->url = (strtoupper($mode) === 'DEV') ? $this->uatURL : $this->liveURL;
        $this->initHttpClient();
        return $this;
    }

    /**
     * Update the UAT and Live API URLs.
     *
     * @param string $devURL  Custom UAT URL
     * @param string $liveURL Custom Live URL
     * @return self
     */
    public function updateURL(string $devURL, string $liveURL): self
    {
        $this->uatURL = $devURL;
        $this->liveURL = $liveURL;
        return $this;
    }

    /**
     * Set HTTP method for payment requests.
     *
     * @param string $method  'direct' for POST, anything else for PUT
     * @return self
     */
    public function setPaymentMethod(string $method = 'direct'): self
    {
        $this->paymentMethod = (strtolower($method) === 'direct') ? 'POST' : 'PUT';
        return $this;
    }

    /**
     * Process a payment request.
     *
     * @param array $payload Payment data
     * @return string JSON response from API
     */
    public function process(array $payload): string
    {
        try {
            $response = $this->client->request($this->paymentMethod, "/{$this->acquireUrl}", [
                'auth' => [$this->token, $this->secret],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json',
                ],
                'json' => $payload
            ]);
            return $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            return json_encode([
                'status' => 'Error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Disburse (send) a payment with checksum.
     *
     * @param array $payload    Payment data
     * @param string $privateKey PEM-encoded private key for checksum
     * @return string JSON response from API
     */
    public function disburse(array $payload, string $privateKey): string
    {
        try {
            $this->generateChecksum($payload, $privateKey);
            $response = $this->client->request($this->paymentMethod, "/{$this->disburseUrl}", [
                'auth' => [$this->token, $this->secret],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json',
                    'checksum' => $this->checksum
                ],
                'json' => $payload
            ]);
            return $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            return json_encode([
                'status' => 'Error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate checksum for the given payload using the provided private key.
     *
     * @param array $payload    Payment data (must include transaction and account details)
     * @param string $privateKey PEM-encoded private key
     * @return self
     * @throws Exception If private key retrieval fails
     */
    public function generateChecksum(array $payload, string $privateKey): self
    {
        $reference = $payload['transaction']['reference'] ?? '';
        $merchantId = $payload['transaction']['merchantId'] ?? '';
        $accountNumber = $payload['accountDetails']['accountNumber'] ?? '';
        $amount = $payload['transaction']['amount'] ?? '';

        $dataToEncrypt = $this->token . $reference . $merchantId . $accountNumber . $amount;
        $privateKeyResource = openssl_get_privatekey($privateKey, "");
        if (!$privateKeyResource) {
            throw new Exception("Failed to retrieve private key");
        }
        $this->checksum = (new ContipayChecksum())->generateChecksum($dataToEncrypt, true, $privateKeyResource);
        return $this;
    }


    /**
     * Initialize the Guzzle HTTP client with the current base URL.
     *
     * @return void
     */
    protected function initHttpClient(): void
    {
        $this->client = new Client(['base_uri' => $this->url]);
    }
}
