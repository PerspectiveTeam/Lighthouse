<?php

namespace Perspective\Lighthouse\Controller\Adminhtml\Schedule;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Cron\Model\ResourceModel\Schedule\Collection;
use Magento\Cron\Model\Schedule;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Perspective\Lighthouse\Api\Data\JobCodeInterface;

class NewAction extends Action implements ActionInterface
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    private Collection $cronScheduleCollection;

    private ProductMetadataInterface $productMetadata;

    private TimezoneInterface $timezone;

    private DateTime $dateTime;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     */
    public function __construct(
        Context $context,
        ResultFactory $resultFactory,
        Collection $cronScheduleCollection,
        ProductMetadataInterface $productMetadata,
        TimezoneInterface $timezone,
        DateTime $dateTime
    ) {
        parent::__construct($context);
        $this->resultFactory = $resultFactory;
        $this->cronScheduleCollection = $cronScheduleCollection;
        $this->productMetadata = $productMetadata;
        $this->timezone = $timezone;
        $this->dateTime = $dateTime;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $error = false;
        $data = [];
        $message = '';
        try {
            $schedule = $this->cronScheduleCollection->getNewEmptyItem();
            $schedule
                ->setJobCode(JobCodeInterface::JOB_CODE_NAME)
                ->setStatus(Schedule::STATUS_RUNNING)
                ->setFinishedAt(date('Y-m-d H:i:s', $this->getCronTimestamp()))
                ->save();
            $message = __('Successfully scheduled.');
            $data = [
                'error' => true,
                'message' => $message
            ];
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $error = true;
            $message = $e->getMessage();
            $data = [
                'error' => true,
                'message' => $message,
                'trace' => $e->getTraceAsString()
            ];
        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        return $resultJson->setData([
            'message' => $message,
            'data' => $data,
            'error' => $error
        ]);
    }

    /**
     * Get timestamp used for time related database fields in the cron tables
     *
     * Note: The timestamp used will change from Magento 2.1.7 to 2.2.0 and
     *       these changes are branched by Magento version in this method.
     *
     * @return int
     */
    protected function getCronTimestamp()
    {
        /* @var $version string e.g. "2.1.7" */
        $version = $this->productMetadata->getVersion();

        if (version_compare($version, '2.2.0') >= 0) {
            return $this->dateTime->gmtTimestamp();
        }

        return $this->timezone->scopeTimeStamp();
    }

}
