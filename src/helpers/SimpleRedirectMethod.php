<?php

namespace Contipay\Helpers;

use Contipay\Helpers\Payload\PayloadGenerator;

/**
 * SimpleRedirectMethod (refactored to use PayloadGenerator).
 *
 * @deprecated Use PayloadGenerator instead. This class is kept for backward compatibility.
 */
class SimpleRedirectMethod
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
    public function __construct(int $merchantId, string $webhookUrl, string $successUrl, string $cancelUrl)
    {
        $this->payloadGenerator = new PayloadGenerator($merchantId, $webhookUrl, $successUrl, $cancelUrl);
    }

    /**
     * Proxy to PayloadGenerator::simpleRedirectPayload
     */
    public function preparePayload(
        float $amount,
        string $account,
        string $currency = 'USD',
        ?string $ref = null,
        string $description = "",
        string $cell = "",
        bool $isCod = false,
        bool $isCoc = false
    ): array {
        return $this->payloadGenerator->simpleRedirectPayload($amount, $account, $currency, $ref, $description, $cell, $isCod, $isCoc);
    }
}
