<?php

declare(strict_types=1);

namespace Razoyo\CarProfile\Controller\Account;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\View\Result\Page;
use Magento\Customer\Model\Session;

class Index implements ActionInterface
{
    /**
     * @param PageFactory $resultPageFactory
     * @param RedirectFactory $resultRedirectFactory
     * @param Session $customerSession
     */
    public function __construct(
        protected readonly PageFactory $resultPageFactory,
        protected readonly RedirectFactory $resultRedirectFactory,
        protected readonly Session $customerSession
    ) {
    }

    /**
     * @return Page|Redirect
     */
    public function execute(): Page|Redirect
    {
        if (!$this->customerSession->isLoggedIn()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('My Car'));
        return $resultPage;
    }
}
