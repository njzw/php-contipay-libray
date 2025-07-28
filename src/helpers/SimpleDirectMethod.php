<?php

namespace Contipay\Helpers;

use Contipay\Helpers\Payload\PayloadGenerator;

/**
 * SimpleDirectMethod (refactored to use PayloadGenerator).
 *
 * @deprecated Use PayloadGenerator instead. This class is kept for backward compatibility.
 */
class SimpleDirectMethod
{
    /**
     * @var PayloadGenerator
     */
    protected PayloadGenerator $payloadGenerator;

    /**
     * Constructor for initializing PayloadGenerator.
     *
     * @param int    $merchantId  The merchant ID.
     * @param string $webhookUrl  The URL for webhook notifications.
     * @param string $successUrl  The URL to redirect to upon successful transaction.
     * @param string $cancelUrl   The URL to redirect to upon canceled transaction.
     */
    public function __construct(int $merchantId, string $webhookUrl, string $successUrl = '', string $cancelUrl = '')
    {
        $this->payloadGenerator = new PayloadGenerator($merchantId, $webhookUrl, $successUrl, $cancelUrl);
    }

    /**
     * Proxy to PayloadGenerator::setUpProviders
     */
    public function setUpProvider(string $providerName = 'Ecocash', string $providerCode = 'EC'): self
    {
        $this->payloadGenerator->setUpProviders($providerName, $providerCode);
        return $this;
    }

    /**
     * Proxy to PayloadGenerator::simpleDirectPayload
     */
    public function preparePayload(
        float $amount,
        string $account,
        string $currency = 'ZWG',
        ?string $ref = null,
        string $description = "",
        string $cell = ""
    ): array {
        return $this->payloadGenerator->simpleDirectPayload($amount, $account, $currency, $ref, $description, $cell);
    }
}
