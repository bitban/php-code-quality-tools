<?php

class VariableUsageOk
{
    public function dummyMethod()
    {
        $a = 1;
        $b = $a + 10;
        echo $b;

    }
}
