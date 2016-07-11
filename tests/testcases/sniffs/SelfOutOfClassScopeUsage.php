<?php

class SelfOutOfClassScopeUsage
{
    public function dummyMethod()
    {
        echo "I'm a dummy method";
    }
}

self::dummyMethod();
