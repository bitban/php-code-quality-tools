<?php

class UndefinedVariableUsage
{
    private function dummyMethod($c)
    {
        $a = 1;
        $b = $aa + 3;
        echo $b + $c;
    }
}


