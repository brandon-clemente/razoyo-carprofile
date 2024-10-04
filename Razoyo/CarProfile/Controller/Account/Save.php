<?php

declare(strict_types=1);

namespace Razoyo\CarProfile\Controller\Account;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\View\Result\Page;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

class Save implements ActionInterface
{
    /**
     * @param PageFactory $resultPageFactory
     * @param RedirectFactory $resultRedirectFactory
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param RequestInterface $request
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        protected readonly PageFactory $resultPageFactory,
        protected readonly RedirectFactory $resultRedirectFactory,
        protected readonly Session $customerSession,
        protected readonly CustomerRepositoryInterface $customerRepository,
        protected readonly RequestInterface $request,
        protected readonly ManagerInterface $messageManager
    ) {
    }

    /**
     * @return Page|Redirect
     */
    public function execute(): Page|Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($this->customerSession->isLoggedIn()) {

            try {
                $customerId = $this->customerSession->getCustomer()->getId();
                $carProfileId = $this->request->getParam('car_profile_id');
                $customer = $this->customerRepository->getById($customerId);
                $customer->setCustomAttribute('car_profile_id', $carProfileId);
                $this->customerRepository->save($customer);

                $this->messageManager->addSuccessMessage(__('Car profile saved.'));
            } catch (\Exception $e) {
                $this->messageManager->addSuccessMessage(__('Error saving car profile.'));
            }
        }

        $resultRedirect->setPath('*/*');
        return $resultRedirect;

    }
}
