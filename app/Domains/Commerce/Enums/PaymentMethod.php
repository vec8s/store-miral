<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Enums;

enum PaymentMethod: string
{
    case CashOnDelivery = 'cod';
    case CreditCard = 'credit_card';
    case BankTransfer = 'bank_transfer';
    case ApplePay = 'apple_pay';
    case StcPay = 'stc_pay';
    case Mada = 'mada';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::CashOnDelivery => 'Cash on Delivery',
            self::CreditCard => 'Credit Card',
            self::BankTransfer => 'Bank Transfer',
            self::ApplePay => 'Apple Pay',
            self::StcPay => 'STC Pay',
            self::Mada => 'mada',
            self::Other => 'Other',
        };
    }
}
