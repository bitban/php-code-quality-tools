<?php

$value = rand(0, 10) < 5 ? 'less than five' : '';
if (empty($value)) {
    var_dump("value was higher or equal than five");
} else {
    var_dump ("value was lower than five");
}
