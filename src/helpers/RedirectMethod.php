<?php

namespace Contipay\Helpers;

use Contipay\Helpers\Payload\PayloadGenerator;

/**
 * Redirect payload class (refactored to use PayloadGenerator).
 *
 * @deprecated Use PayloadGenerator instead. This class is kept for backward compatibility.
 */
class RedirectMethod
{
    /**
     * @var PayloadGenerator
     */
    protected PayloadGenerator $payloadGenerator;

    /**
     * Constructor for initializing PayloadGenerator.
     *
     * @param int    $merchantCode  The merchant code.
     * @param string $webhookUrl    The URL for webhook notifications.
     * @param string $successUrl    The URL to redirect to upon successful transaction.
     * @param string $cancelUrl     The URL to redirect to upon canceled transaction.
     */
    public function __construct(int $merchantCode, string $webhookUrl, string $successUrl, string $cancelUrl)
    {
        $this->payloadGenerator = new PayloadGenerator($merchantCode, $webhookUrl, $successUrl, $cancelUrl);
    }

    /**
     * Proxy to PayloadGenerator::setUpCustomer
     */
    public function setUpCustomer(
        string $firstName,
        string $lastName,
        string $cell,
        string $countryCode = 'ZW',
        string $email = "",
        string $middleName = "-",
        string $nationalId = "-"
    ): self {
        $this->payloadGenerator->setUpCustomer($firstName, $lastName, $cell, $countryCode, $email, $middleName, $nationalId);
        return $this;
    }

    /**
     * Proxy to PayloadGenerator::setUpTransaction
     */
    public function setUpTransaction(
        float $amount,
        string $currency = 'ZWG',
        string $transactionRef = '',
        string $transactionDescription = ''
    ): self {
        $this->payloadGenerator->setUpTransaction($amount, $currency, $transactionRef, $transactionDescription);
        return $this;
    }

    /**
     * Proxy to PayloadGenerator::redirectPayload
     */
    public function preparePayload(bool $isCoc = false, bool $isCod = false): array
    {
        return $this->payloadGenerator->redirectPayload($isCoc, $isCod);
    }
}
