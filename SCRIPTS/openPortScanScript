!# /bin/bash

echo "enter the first ip:"
read firstIpToScan

echo "enter the last octat:"
read LastOctatToScan

echo "enter the port number:"
read  Port

nmap -sT $firstIpToScan-$LastOctatToScan -p $Port > /div/null -oG scannedIps1
cat scannedIps1 | grep open > scannedIps2
cat scannedIps2