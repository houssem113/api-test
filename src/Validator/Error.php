<?php

namespace App\Validator;

class Error 
{

    public function __construct(private string $key, private string $constraint)
    {
        
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($key): self
    {
        $this->key = $key;

        return $this;
    }

  
    /**
     * Get the value of constraint
     */ 
    public function getConstraint()
    {
        return $this->constraint;
    }

    /**
     * Set the value of constraint
     *
     * @return  self
     */ 
    public function setConstraint($constraint)
    {
        $this->constraint = $constraint;

        return $this;
    }
}