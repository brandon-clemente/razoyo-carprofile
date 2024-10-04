<?php

namespace Razoyo\CarProfile\Block\Account;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Pricing\Helper\Data;
use Razoyo\CarProfile\Model\CarApi;

class CarProfile extends Template
{
    /**
     * @param Template\Context $context
     * @param Session $customerSession
     * @param CarApi $carApi
     */
    public function __construct(
        protected readonly Template\Context $context,
        protected readonly Session $customerSession,
        protected readonly Data $priceHelper,
        protected readonly CarApi $carApi,
    ) {
        parent::__construct($context);
    }

    /**
     * @return string|null
     */
    public function getCarId(): ?string
    {
        return $this->customerSession->getCustomer()->getCarProfileId() ?? null;
    }

    /**
     * @return string
     */
    public function getSaveUrl(): string
    {
        return $this->_urlBuilder->getUrl(
            'carprofile/account/save',
            ['_secure' => true]
        );
    }

    /**
     * @return array
     */
    public function getCarList()
    {
        return $this->carApi->getCarList();
    }

    /**
     * @param $carId
     * @return array
     */
    public function getCarById($carId): array
    {
        return $this->carApi->getCarListById($carId);
    }

    /**
     * @param $price
     * @return string
     */
    public function getFormattedPrice($price): string
    {
        return $this->priceHelper->currency($price, true, false);
    }
}
