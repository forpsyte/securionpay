<?php

namespace Forpsyte\SecurionPay\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Forpsyte\SecurionPay\Model\Customer;

/**
 * CustomerRepository Interface
 */
interface CustomerRepositoryInterface
{

    /**
     * get by id
     *
     * @param int $id
     * @return Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * get by id
     *
     * @param int $customerId
     * @return Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCustomerId($customerId);

    /**
     * get by id
     *
     * @param Data\CustomerInterface|Customer $customer
     * @return Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\CustomerInterface $customer);

    /**
     * get list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
    /**
     * delete
     *
     * @param Data\CustomerInterface|Customer $customer
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\CustomerInterface $customer);
    /**
     * delete by id
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}
