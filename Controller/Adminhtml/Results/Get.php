<?php

namespace Perspective\Lighthouse\Controller\Adminhtml\Results;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultFactory;

class Get extends Action implements ActionInterface
{
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
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $page = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $path = base64_decode($this->getRequest()->getParam('path'));
        $content = file_get_contents($path);
        $page->setContents($content);
        return $page;
    }
}