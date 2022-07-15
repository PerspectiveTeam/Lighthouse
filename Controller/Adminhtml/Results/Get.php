<?php

namespace Perspective\Lighthouse\Controller\Adminhtml\Results;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultFactory;

class Get extends Action implements ActionInterface
{
    /**
     * @var \Magento\Backend\App\Action\Context
     */
    private Context $context;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     */
    public function __construct(
        Context $context,
        ResultFactory $resultFactory
    ) {
        parent::__construct($context);
        $this->resultFactory = $resultFactory;
        $this->context = $context;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Raw $page */
        $page = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $path = base64_decode($this->getRequest()->getParam('path'));
        if ($path === 'empty') {
            /** @var \Magento\Framework\Controller\Result\Redirect $page */
            $page = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $page->setPath('lighthouse/results/index');
            $this->context->getMessageManager()->addNoticeMessage('No results found.');
            return $page;
        }
        $content = file_get_contents($path);
        $page->setContents($content);
        return $page;
    }
}
