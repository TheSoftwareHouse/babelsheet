#!/bin/sh
set -e
echo $SECRET > /src/translator/client_secret.json
exec "$@"
