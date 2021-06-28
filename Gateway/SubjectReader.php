<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simon\SecurionPay\Gateway;

use InvalidArgumentException;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Helper;
use Simon\SecurionPay\Gateway\Http\Data\Response;

/**
 * Short description...
 *
 * Long description
 * Broken down into several lines
 *
 * License notice...
 */
class SubjectReader
{
    /**
     * Reads response object from subject
     *
     * @param array $subject
     * @return object
     */
    public function readResponseObject(array $subject)
    {
        $response = Helper\SubjectReader::readResponse($subject);
        if (!isset($response['object']) || !is_object($response['object'])) {
            throw new InvalidArgumentException('Response object does not exist');
        }

        return $response['object'];
    }

    /**
     * Reads payment from subject
     *
     * @param array $subject
     * @return PaymentDataObjectInterface
     */
    public function readPayment(array $subject)
    {
        return Helper\SubjectReader::readPayment($subject);
    }

    /**
     * @param array $subject
     * @return Response
     * @throws InvalidArgumentException if the subject doesn't contain transaction details.
     */
    public function readTransaction(array $subject)
    {
        if (!isset($subject['object']) || !is_object($subject['object'])) {
            throw new InvalidArgumentException('Response object does not exist.');
        }

        if (!isset($subject['object'])
            || !$subject['object'] instanceof Response
        ) {
            throw new InvalidArgumentException('The object is not a class Response.');
        }

        return $subject['object'];
    }

    /**
     * Reads amount from subject
     *
     * @param array $subject
     * @return mixed
     */
    public function readAmount(array $subject)
    {
        return Helper\SubjectReader::readAmount($subject);
    }

    /**
     * Reads store's ID, otherwise returns null.
     *
     * @param array $subject
     * @return int|null
     */
    public function readStoreId(array $subject)
    {
        return $subject['store_id'] ?? null;
    }
}
