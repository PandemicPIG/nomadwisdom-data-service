<?php

array_map('unlink', glob("../cache/*"));

echo 'last run: ' . date('l jS \of F Y h:i:s A');