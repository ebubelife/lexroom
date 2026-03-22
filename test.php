<?php
echo "LexRoom Test - Server is working!";
echo "<br>Current directory: " . __DIR__;
echo "<br>Files in directory: ";
print_r(scandir('.'));
?>