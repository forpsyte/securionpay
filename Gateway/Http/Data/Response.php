<?php

namespace Simon\SecurionPay\Gateway\Http\Data;

use Magento\Framework\Serialize\Serializer\Json as Serializer;

/**
 * Payment provider response object
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Response implements \ArrayAccess
{
    const ID = 'id';
    const CODE = 'code';
    const MESSAGE = 'message';
    const ERROR_TYPE = 'type';
    const CHARGE_TYPE = 'type';
    const FAILURE_CODE = 'failureCode';
    const FAILURE_MESSAGE = 'failureMessage';
    const FRAUD_DETAILS = 'fraudDetails';
    const FRAUD_DETAIL_STATUS = 'status';
    const FRAUD_STATUS_SAFE = 'safe';
    const FRAUD_STATUS_SUSPICIOUS = 'suspicious';
    const FRAUD_STATUS_FRAUDULENT = 'fraudulent';
    const FRAUD_STATUS_IN_PROGRESS = 'in_progress';
    const DATA = 'data';
    const LAST_4 = 'last4';
    const BRAND = 'brand';
    const EXP_MONTH = 'expMonth';
    const EXP_YEAR = 'expYear';

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * Response constructor.
     * @param Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @var int
     */
    protected $status;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var array
     */
    protected $decodedBody;

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string $header
     * @return string
     */
    public function getHeader($header)
    {
        return $this->headers[$header];
    }

    /**
     * @return string
     */
    public function getBodyAsString()
    {
        return $this->body;
    }

    /**
     * @return array|string
     */
    public function getBody()
    {
        return $this->getDecodedBody() ?: $this->__toString();
    }

    /**
     * @return array
     */
    public function getDecodedBody()
    {
        return $this->decodedBody;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param $body
     */
    public function setBody($body)
    {
        if (is_array($body)) {
            $this->decodedBody = $body;
            $this->body = $this->serializer->serialize($body);
        } else {
            $this->body = $body;
            $this->decodedBody = $this->serializer->unserialize($body);
        }
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->decodedBody) && isset($this->decodedBody[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->decodedBody[$offset] : null;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->decodedBody[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->decodedBody[$offset]);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->body;
    }
}
