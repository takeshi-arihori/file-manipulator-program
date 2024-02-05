<?php

echo "What is your favorite food?\n";
$food = fgets(STDIN);
echo 'Thanks for letting me know your favorite food is ' . trim($food) . ".\n";

?>
