<?php

namespace Perspective\Lighthouse\Api\Data;

interface JobCodeInterface
{
    public const JOB_CODE_NAME = 'lighthouse_schedule_job';
    const MODULE_GROUP = 'perspective_lighthouse';
    const CRON_STRING_PATH = 'crontab/' . self::MODULE_GROUP . '/jobs/' . self::JOB_CODE_NAME . '/schedule/cron_expr';
    const CRON_MODEL_PATH = 'crontab/' . self::MODULE_GROUP . '/jobs/' . self::JOB_CODE_NAME . '/run/model';
}
