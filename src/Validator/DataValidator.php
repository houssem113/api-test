<?php

namespace App\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Validator\Error;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class DataValidator
{

    public function __construct(
        private ValidatorInterface $validator, 
        private ConstraintViolationListInterface | null $constraints = null, 
         private array $errors = [])
    {
    }

    public function validate($object): bool
    {

        $constraints = $this->validator->validate($object);

         $isValid = count($constraints) === 0;

         $this->constraints =  $constraints;

        return $isValid;

    }

    /**
     * @return Error[]
     */

    public function getErrors()
    {

        $errors = [];

        $offsets = count($this->constraints);

        for($offset = 0; $offset < $offsets; $offset++) {
            
            $error = $this->constraints->get($offset);

            $message = $error->getMessage();

            $key = $error->getPropertyPath();

            $dataError = new Error($key, $message);

            array_push($errors, $dataError);

            
        }
    
        $this->errors = $errors;

        return $this->errors;
    }
}
