<?php

namespace Forpsyte\SecurionPay\Setup\Patch\Data;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Forpsyte\SecurionPay\Api\CurrencyRepositoryInterface;
use Forpsyte\SecurionPay\Model\CurrencyFactory;

/**
 * Add currency data.
 */
class Currency implements DataPatchInterface
{
    /**
     * @var CurrencyRepositoryInterface
     */
    protected $currencyRepository;

    /**
     * @var CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var array
     */
    protected $currency = [
        ['code' => 'USD', 'name' => 'United States Dollar', 'decimals' => 2],
        ['code' => 'EUR', 'name' => 'Euro', 'decimals' => 2],
        ['code' => 'BGN', 'name' => 'Bulgarian Lev', 'decimals' => 2],
        ['code' => 'HRK', 'name' => 'Croatian Kuna', 'decimals' => 2],
        ['code' => 'CZK', 'name' => 'Czech Koruna', 'decimals' => 2],
        ['code' => 'DKK', 'name' => 'Danish Krone', 'decimals' => 2],
        ['code' => 'GIP', 'name' => 'Gibraltar Pound', 'decimals' => 2],
        ['code' => 'HUF', 'name' => 'Hungarian Forint', 'decimals' => 2],
        ['code' => 'ISK', 'name' => 'Icelandic Króna', 'decimals' => 0],
        ['code' => 'GBP', 'name' => 'Pound Sterling', 'decimals' => 2],
        ['code' => 'ILS', 'name' => 'Israeli New Shekel', 'decimals' => 2],
        ['code' => 'CHF', 'name' => 'Swiss Franc', 'decimals' => 2],
        ['code' => 'NOK', 'name' => 'Norwegian Krone', 'decimals' => 2],
        ['code' => 'PLN', 'name' => 'Polish Złoty', 'decimals' => 2],
        ['code' => 'RON', 'name' => 'Romanian Leu', 'decimals' => 2],
        ['code' => 'SEK', 'name' => 'Swedish Krona', 'decimals' => 2],
        ['code' => 'CHE', 'name' => 'WIR euro', 'decimals' => 2],
        ['code' => 'CHW', 'name' => 'WIR franc', 'decimals' => 2],
        ['code' => 'AFN', 'name' => 'Afghan Afghani', 'decimals' => 2],
        ['code' => 'DZD', 'name' => 'Algerian Dinar', 'decimals' => 2],
        ['code' => 'ARS', 'name' => 'Argentine Peso', 'decimals' => 2],
        ['code' => 'AUD', 'name' => 'Australian Dollar', 'decimals' => 2],
        ['code' => 'BHD', 'name' => 'Bahraini Dinar', 'decimals' => 3],
        ['code' => 'BDT', 'name' => 'Bangladeshi Taka', 'decimals' => 2],
        ['code' => 'BYR', 'name' => 'Belarusian Ruble', 'decimals' => 2],
        ['code' => 'BAM', 'name' => 'Bosnia and Herzegovina Convertible Mark', 'decimals' => 2],
        ['code' => 'BWP', 'name' => 'Botswana Pula', 'decimals' => 2],
        ['code' => 'BRL', 'name' => 'Brazilian Real', 'decimals' => 2],
        ['code' => 'BND', 'name' => 'Brunei Dollar', 'decimals' => 2],
        ['code' => 'CAD', 'name' => 'Canadian Dollar', 'decimals' => 2],
        ['code' => 'CLP', 'name' => 'Chilean Peso', 'decimals' => 0],
        ['code' => 'CNY', 'name' => 'Chinese Yuan', 'decimals' => 2],
        ['code' => 'COP', 'name' => 'Colombian Peso', 'decimals' => 2],
        ['code' => 'KMF', 'name' => 'Comoro Franc', 'decimals' => 0],
        ['code' => 'DJF', 'name' => 'Djiboutian Franc', 'decimals' => 0],
        ['code' => 'DOP', 'name' => 'Dominican Peso', 'decimals' => 2],
        ['code' => 'EGP', 'name' => 'Egyptian Pound', 'decimals' => 2],
        ['code' => 'ETB', 'name' => 'Ethiopian Birr', 'decimals' => 2],
        ['code' => 'ERN', 'name' => 'Eritrean Nakfa', 'decimals' => 2],
        ['code' => 'GEL', 'name' => 'Georgian Lari', 'decimals' => 2],
        ['code' => 'HKD', 'name' => 'Hong Kong Dollar', 'decimals' => 2],
        ['code' => 'INR', 'name' => 'Indian Rupee', 'decimals' => 2],
        ['code' => 'IDR', 'name' => 'Indonesian Rupiah', 'decimals' => 2],
        ['code' => 'IRR', 'name' => 'Iranian Rial', 'decimals' => 2],
        ['code' => 'IQD', 'name' => 'Iraqi Dinar', 'decimals' => 3],
        ['code' => 'JMD', 'name' => 'Jamaican Dollar', 'decimals' => 2],
        ['code' => 'JPY', 'name' => 'Japanese Yen', 'decimals' => 0],
        ['code' => 'JOD', 'name' => 'Jordanian Dinar', 'decimals' => 3],
        ['code' => 'KZT', 'name' => 'Kazakhstani Tenge', 'decimals' => 2],
        ['code' => 'KES', 'name' => 'Kenyan Shilling', 'decimals' => 2],
        ['code' => 'KWD', 'name' => 'Kuwaiti Dinar', 'decimals' => 3],
        ['code' => 'KGS', 'name' => 'Kyrgyzstani Som', 'decimals' => 2],
        ['code' => 'LVL', 'name' => 'Latvian Lats', 'decimals' => 2],
        ['code' => 'LBP', 'name' => 'Lebanese Pound', 'decimals' => 2],
        ['code' => 'LTL', 'name' => 'Lithuanian Litas', 'decimals' => 2],
        ['code' => 'MOP', 'name' => 'Macanese Pataca', 'decimals' => 2],
        ['code' => 'MKD', 'name' => 'Macedonian Denar', 'decimals' => 2],
        ['code' => 'MGA', 'name' => 'Malagasy Ariary', 'decimals' => 2],
        ['code' => 'MWK', 'name' => 'Malawian Kwacha', 'decimals' => 2],
        ['code' => 'MYR', 'name' => 'Malaysian Ringgit', 'decimals' => 2],
        ['code' => 'MUR', 'name' => 'Mauritian Rupee', 'decimals' => 2],
        ['code' => 'MXN', 'name' => 'Mexican Peso', 'decimals' => 2],
        ['code' => 'MDL', 'name' => 'Moldovan Leu', 'decimals' => 2],
        ['code' => 'MAD', 'name' => 'Moroccan Dirham', 'decimals' => 2],
        ['code' => 'MZN', 'name' => 'Mozambican Metical', 'decimals' => 2],
        ['code' => 'NAD', 'name' => 'Namibian Dollar', 'decimals' => 2],
        ['code' => 'NPR', 'name' => 'Nepalese Rupee', 'decimals' => 2],
        ['code' => 'ANG', 'name' => 'Netherlands Antillean Guilder', 'decimals' => 2],
        ['code' => 'NZD', 'name' => 'New Zealand Dollar', 'decimals' => 2],
        ['code' => 'OMR', 'name' => 'Omani Rial', 'decimals' => 3],
        ['code' => 'PKR', 'name' => 'Pakistani Rupee', 'decimals' => 2],
        ['code' => 'PEN', 'name' => 'Peruvian Sol', 'decimals' => 2],
        ['code' => 'PHP', 'name' => 'Philippine Peso', 'decimals' => 2],
        ['code' => 'QAR', 'name' => 'Qatari Riyal', 'decimals' => 2],
        ['code' => 'RUB', 'name' => 'Russian Ruble', 'decimals' => 2],
        ['code' => 'SAR', 'name' => 'Saudi Riyal', 'decimals' => 2],
        ['code' => 'RSD', 'name' => 'Serbian Dinar', 'decimals' => 2],
        ['code' => 'SGD', 'name' => 'Singapore Dollar', 'decimals' => 2],
        ['code' => 'ZAR', 'name' => 'South African Rand', 'decimals' => 2],
        ['code' => 'KRW', 'name' => 'South Korean Won', 'decimals' => 0],
        ['code' => 'LKR', 'name' => 'Sri Lankan Rupee', 'decimals' => 2],
        ['code' => 'SYP', 'name' => 'Syrian Pound', 'decimals' => 2],
        ['code' => 'TWD', 'name' => 'New Taiwan Dollar', 'decimals' => 2],
        ['code' => 'TZS', 'name' => 'Tanzanian Shilling', 'decimals' => 2],
        ['code' => 'THB', 'name' => 'Thai Baht', 'decimals' => 2],
        ['code' => 'TND', 'name' => 'Tunisian Dinar', 'decimals' => 3],
        ['code' => 'TRY', 'name' => 'Turkish Lira', 'decimals' => 2],
        ['code' => 'UAH', 'name' => 'Ukrainian Hryvnia', 'decimals' => 2],
        ['code' => 'AED', 'name' => 'United Arab Emirates Dirham', 'decimals' => 2],
        ['code' => 'VEB', 'name' => 'Venezuelan Bolívar', 'decimals' => 2],
        ['code' => 'VEF', 'name' => 'Venezuelan Bolívar Fuerte', 'decimals' => 2],
        ['code' => 'VND', 'name' => 'Vietnamese Đồng', 'decimals' => 0],
        ['code' => 'XOF', 'name' => 'CFA Franc BCEAO', 'decimals' => 0],
        ['code' => 'YER', 'name' => 'Yemeni Rial', 'decimals' => 2],
        ['code' => 'ZMK', 'name' => 'Zambian Kwacha', 'decimals' => 2]
    ];

    /**
     * Currency constructor.
     * @param CurrencyRepositoryInterface $currencyRepository
     * @param CurrencyFactory $currencyFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        CurrencyRepositoryInterface $currencyRepository,
        CurrencyFactory $currencyFactory,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->currencyRepository = $currencyRepository;
        $this->currencyFactory = $currencyFactory;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        foreach ($this->currency as $currency) {
            $currencyModel = $this->currencyFactory->create();
            $currencyModel->setData($currency);
            $this->currencyRepository->save($currencyModel);
        }
        $this->moduleDataSetup->getConnection()->endSetup();
    }
}
