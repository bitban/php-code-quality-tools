<?php

class ThisOutOfClassScopeUsage
{
    public function dummyMethod()
    {
        echo "I'm a dummy method";
    }
}

$this->dummyMethod();
