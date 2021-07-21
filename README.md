# SecurionPay Payments

Module Forpsyte\SecurionPay implements integration with the SecurionPay payment system.

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
* Fraud detection

## Installation
### Composer (Recommended)
In your Magento 2 root directory run  
`composer require forpsyte/magento2-module-securionpay`  
`php bin/magento module:enable Forpsyte_SecurionPay`  
`php bin/magento setup:upgrade`

### Manual
In your Magento 2 root directory run  
`git clone https://github.com/forpsyte/securionpay.git app/code/Forpsyte/SecurionPay`  
`composer require securionpay/securionpay-php:^2.2.0`  
`php bin/magento module:enable Forpsyte_SecurionPay`  
`php bin/magento setup:upgrade`

## Configuration
The configuration can be found in the Magento 2 admin panel under  
Store->Configuration->Sales->Payment Methods->SecurionPay

## Magento Version Support
| Module Version | Magento Version |
| -------------- | --------------- |
| v1.x.x         | v2.3.x, v2.4.x  |

## Demo Site
A <a href="http://securionpay.jsimon.me/" target="_blank">demo site</a> is available for this module. Use the test
cards below when making a payment. Click <a href="https://securionpay.com/docs/testing">here</a> for 
a full list of the test cards.

### Test card numbers
| Card number | Card type |
| ---------------- | --------------- |
| 4012000100000007 | Visa |
| 5555555555554444 | MasterCard |
| 6759649826438453 | Maestro |
| 378282246310005 | American Express |
| 6011000990139424 | Discover |
| 30569309025904 | Diners Club |
| 3530111333300000 | JCB |


