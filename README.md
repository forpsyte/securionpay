# SecurionPay Payments

Module Simon\SecurionPay implements integration with the SecurionPay payment system.

## Overview

This module implements integration with SecurionPay payment system via Custom Form or the Checkout solution provided by
SecurionPay.

## Available Payment Methods
* Credit Card
    * Visa
    * Mastercard
    * American Express
    * Discover
    * JCB
    * Diners
    * Maestro International

## Features
* SecurionPay custom form payment method
* SecurionPay checkout payment method
* 3D Secure payment services
* Stored payment methods for customers

## Installation
### Composer (Recommended)
In your Magento 2 root directory run  
`composer require simon/magento2-module-securionpay`  
`php bin/magento module:enable Simon_SecurionPay`  
`php bin/magento setup:upgrade`

### Manual
In your Magento 2 root directory run  
`mkdir -p app/code/Simon/`  
`git clone https://github.com/jsimon-development/securionpay.git app/code/Simon/SecurionPay`  
`composer require securionpay/securionpay-php:^2.2.0`  
`php bin/magento module:enable Simon_SecurionPay`  
`php bin/magento setup:upgrade`

## Configuration
The configuration can be found in the Magento 2 admin panel under  
Store->Configuration->Sales->Payment Methods->SecurionPay

## Magento Version Support
| Module Version | Magento Version |
| -------------- | --------------- |
| v1.x.x         | v2.3.x, v2.4.x  |
