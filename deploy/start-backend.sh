#!/bin/bash
date
cd /var/www/html
/usr/local/bin/php -S 0.0.0.0:80 -t . index.php > aaaa.log 2>&1 &
