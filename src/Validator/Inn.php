<?php

namespace Holdmann\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class Inn
 *
 * @Annotation
 * @Target({"PROPERTY"})
 * @package Holdmann\Validator
 */
class Inn extends Constraint
{
    /**
     * @var string
     */
    public $lengthFail = 'Should be 10 or 12 digits';

    /**
     * @var string
     */
    public $formatFail = 'Should consists only digits';

    /**
     * @var string
     */
    public $checkSumFail = 'Incorrect checksum';

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
