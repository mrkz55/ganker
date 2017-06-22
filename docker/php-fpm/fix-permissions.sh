#!/bin/bash
echo $OSTYPE | grep darwin | if [[ $? -eq 0 ]] ; then usermod -u 1000 www-data && groupmod -g 500 staff && groupmod -g 50 www-data ; fi