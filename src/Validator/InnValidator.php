<?php

namespace Holdmann\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\OutOfBoundsException;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

/**
 * Class InnValidator
 *
 * @package Holdmann\Validator
 */
class InnValidator extends ConstraintValidator
{
    /**
     * @var \Symfony\Component\Validator\Constraint
     */
    private $constraint;

    /**
     * Validate CRM URL
     *
     * @param mixed      $value inn from form
     * @param Constraint $constraint Restriction for validation
     */
    public function validate($value, Constraint $constraint): void
    {
        try {
            $this->constraint = $constraint;

            // custom constraints should ignore null and empty values to allow
            // other constraints (NotBlank, NotNull, etc.) to take care of that
            if (null === $value || '' === $value) {
                throw new OutOfBoundsException();
            }

            $this->validateInnSymbols($value);

            $innSymbols = str_split($value);
            $innLength  = mb_strlen($value);

            if ($innLength === 10) {
                $this->validateCheckSum($innSymbols, 9);
            } elseif ($innLength === 12) {
                $this->validateCheckSum($innSymbols, 10);
                $this->validateCheckSum($innSymbols, 11);
            } else {
                throw new InvalidArgumentException($constraint->lengthFail);
            }
        } catch (InvalidArgumentException $e) {
            $this->context->buildViolation($e->getMessage())->addViolation();
        } catch (OutOfBoundsException $e) {
            // do nothing here
        }
    }

    /**
     * @param string $inn
     * @param $constraint
     * @return void
     */
    private function validateInnSymbols(string $inn)
    {
        if (1 !== preg_match('/\d+/', $inn)) {
            throw new InvalidArgumentException($this->constraint->formatFail);
        }
    }

    /**
     * @param array $innSymbols
     * @param int   $checkInnIndex
     *
     * @return void
     */
    private function validateCheckSum(array $innSymbols, int $checkInnIndex): void
    {
        $checksum = 0;
        $offset = 11 - $checkInnIndex;
        $coefficients = [3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8];
        for ($innIndex = 0; isset($coefficients[$innIndex + $offset]); $innIndex++) {
            $checksum += (int)$innSymbols[$innIndex] * $coefficients[$innIndex + $offset];
        }
        if ((int)$innSymbols[$checkInnIndex] !== $checksum % 11 % 10) {
            throw new InvalidArgumentException($this->constraint->checkSumFail);
        }
    }
}
