<?php

require_once './kibu/core/System/Utility.php';

echo "Rand: ".Utility::generateRandStr(5);

echo "<br />";

echo "Guid: ".Utility::guidGen();

?>