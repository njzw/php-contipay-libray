<?php

namespace Contipay\Helpers;

use Contipay\Helpers\Payload\PayloadGenerator;

/**
 * Direct and disbursement payload class (refactored to use PayloadGenerator).
 *
 * @deprecated Use PayloadGenerator instead. This class is kept for backward compatibility.
 */
class DirectMethod
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
     */
    public function __construct(int $merchantCode, string $webhookUrl)
    {
        $this->payloadGenerator = new PayloadGenerator($merchantCode, $webhookUrl);
    }

    /**
     * Proxy to PayloadGenerator::setUpProviders
     */
    public function setUpProviders(string $providerName = 'Ecocash', string $providerCode = 'EC'): self
    {
        $this->payloadGenerator->setUpProviders($providerName, $providerCode);
        return $this;
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
     * Proxy to PayloadGenerator::setUpAccountDetails
     */
    public function setUpAccountDetails(
        string $account = '',
        string $accountName = '-',
        string $accountExpiry = '-',
        string $cvv = ''
    ): self {
        $this->payloadGenerator->setUpAccountDetails($account, $accountName, $accountExpiry, $cvv);
        return $this;
    }

    /**
     * Proxy to PayloadGenerator::directPayload
     */
    public function preparePayload(): array
    {
        return $this->payloadGenerator->directPayload();
    }
}
