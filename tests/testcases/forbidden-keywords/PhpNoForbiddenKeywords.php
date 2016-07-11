<?php

echo "This PHP file has no forbidden keywords";
// empty keyword without a call should not be detected
echo "within a string, empty keyword should not be detected";
// var_dump keyword used without a call
// var_dump() call within a comment should not be detected
