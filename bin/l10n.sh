#!/bin/bash

aws_command="/usr/local/bin/aws"

if [ ! -e "$aws_command" ]; then
    echo "aws command not found, please install awscli"
    exit 1
fi

/usr/local/bin/aws --profile="genesis-dev" s3 cp s3://genesis-ap-southeast-1/msp/lang/php/ ../app/language/ --include '*.php' --recursiv

echo "[OK] download l10n file to local"
exit 0
