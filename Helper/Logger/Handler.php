<?php


namespace Perspective\Lighthouse\Helper\Logger;

use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Logger\Handler\Base;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Monolog\Logger;
use DateTime;

class Handler extends Base
{
    public $root = '/var/log/logger';

    /**
     * @return mixed|string
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param mixed|string $root
     */
    public function setRoot($root): void
    {
        $this->root = $root;
    }

    /**
     * @return mixed|string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param mixed|string $filename
     */
    public function setFilename($filename): void
    {
        $this->filename = $filename;
    }

    /**
     * @return mixed|string
     */
    public function getFolderDateFormat()
    {
        return $this->folderDateFormat;
    }

    /**
     * @param mixed|string $folderDateFormat
     */
    public function setFolderDateFormat($folderDateFormat): void
    {
        $this->folderDateFormat = $folderDateFormat;
    }

    /**
     * @return int
     */
    public function getLoggerType(): int
    {
        return $this->loggerType;
    }

    /**
     * @param int $loggerType
     */
    public function setLoggerType(int $loggerType): void
    {
        $this->loggerType = $loggerType;
    }

    public $filename = 'log.log';

    public $folderDateFormat = 'd_m_Y';

    /**
     * {@inheritDoc}
     */
    protected $loggerType = Logger::INFO;

    /**
     * @var DateTime
     */
    protected $_date;

    public function __construct(
        TimezoneInterface $timezone,
        DriverInterface $filesystem,
        $filePath = null,
        $filename = null,
        $root = null,
        $folderDateFormat = null
    ) {
        $this->_date = $timezone->date();
        if ($root) {
            $this->root = $root;
        }
        if ($filename) {
            $this->filename = $filename;
        }
        if ($folderDateFormat) {
            $this->folderDateFormat = $folderDateFormat;
        }
        parent::__construct(
            $filesystem,
            $filePath,
            $this->getFilePath()
        );
    }

    /**
     * Get date instance.
     *
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->_date;
    }

    /**
     * Get date folder.
     *
     * @return string
     */
    protected function getDateFolderName(): string
    {
        return $this->getDate()->format($this->folderDateFormat);
    }

    /**
     * Get full file path,
     *
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->root
            . '/'
            . $this->getDateFolderName()
            . '/'
            . $this->filename;
    }
}
