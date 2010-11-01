#!/bin/sh

TIMESTAMP=$1

php -r "echo date('Y-m-d H:i:s O',$TIMESTAMP); echo \"\n\"; "

