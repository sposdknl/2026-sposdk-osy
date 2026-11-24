#!/bin/sh
set -eu
expiry=$(echo | timeout 10 openssl s_client -connect sposdk.cz:443 -servername sposdk.cz 2>/dev/null | openssl x509 -noout -enddate | cut -d= -f2-)
[ -n "$expiry" ] || exit 1
end=$(date -j -f '%b %e %T %Y %Z' "$expiry" '+%s')
now=$(date '+%s')
echo $(( (end - now) / 86400 ))
