<?php

class StaticOutOfClassScopeUsage
{
    public function dummyMethod()
    {
        echo "I'm a dummy method";
    }
}

static::dummyMethod();
