#!/bin/bash
if [ "$EUID" -ne 0 ]
then
    echo "Please run this as root or with sudo!"
    exit
fi
rm /var/www/sample/*
cp ./* /var/www/sample/