<?php

namespace App\Enums;

enum JmOrderStatus: string
{
    case awaitingCheckPayment = '1';
    case paymentAccepted = '2';
    case processingInProgress = '3';
    case deliveryIsInProgress = '4';
    case delivered = '5';
    case canceled = '6';
    case refunded = '7';
    case test = '8';
    case onBackorderPaid = '9';
    case awaitingBankWirePayment = '10';
    case remotePaymentAccepted = '11';
    case onBackorderNotPaid = '12';
    case awaitingCashOnDelivery = '13';
    case cancellationByMerchant = '14';
    case cancellationByCustomer = '15';
    case internalError = '16';
    case duplicateOrder = '17';

    case awaitingPaymentOnDeliveryValidation = '19';

    public function label(): string
    {
        return match ($this) {
            self::awaitingCheckPayment => 'Awaiting check payment',
            self::paymentAccepted => 'Payment accepted',
            self::processingInProgress => 'Processing in progress',
            self::deliveryIsInProgress => 'Delivery Is In Progress',
            self::delivered => 'Delivered',
            self::canceled => 'Canceled',
            self::refunded => 'Refunded',
            self::test => 'Test',
            self::onBackorderPaid => 'On backorder (paid)',
            self::awaitingBankWirePayment => 'Awaiting bank wire payment',
            self::remotePaymentAccepted => 'Remote payment accepted',
            self::onBackorderNotPaid => 'On backorder (not paid)',
            self::awaitingCashOnDelivery => 'Awaiting Cash On Delivery validation',
            self::cancellationByMerchant => 'Cancellation by merchant',
            self::cancellationByCustomer => 'Cancellation by customer',
            self::internalError => 'Internal error',
            self::duplicateOrder => 'Duplicate order',
            self::awaitingPaymentOnDeliveryValidation => 'Awaiting Payment On Delivery Validation',
        };
    }

    public function slug(): string
    {
        $name = $this->name;

        $slug = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));

        return $slug;
        // return match ($this) {
        //     self::awaitingCheckPayment => 'cheque',
        //     self::paymentAccepted => 'preparation',
        //     self::processingInProgress => 'shipped',
        //     self::shipped => 'shipped',
        //     self::delivered => 'delivered',
        //     self::canceled => 'order_canceled',
        //     self::refunded => 'refund',
        //     self::test => 'payment_error',
        //     self::onBackorderPaid => 'outofstock',
        //     self::awaitingBankWirePayment => 'bankwire',
        //     self::remotePaymentAccepted => 'payment',
        //     self::onBackorderNotPaid => 'outofstock',
        //     self::awaitingCashOnDelivery => 'order_canceled',
        //     self::cancellationByMerchant => 'order_canceled',
        //     self::cancellationByCustomer => 'order_canceled',
        //     self::internalError => 'order_canceled',
        //     self::duplicateOrder => 'bankwire',
        // };
    }
}
