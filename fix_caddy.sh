#!/bin/bash

echo "Fixing Caddy permissions and directories..."

# 1. Create Log Directory
# Caddy fails if this directory doesn't exist or isn't writable
if [ ! -d "/var/log/caddy" ]; then
    echo "Creating /var/log/caddy..."
    sudo mkdir -p /var/log/caddy
    sudo chown -R caddy:caddy /var/log/caddy
fi

# 2. Fix Certificate Permissions
# Caddy runs as 'caddy' user and often can't read /etc/letsencrypt
DOMAIN="petmelo.com"
CERT_PATH="/etc/letsencrypt/live/$DOMAIN"

if [ -d "$CERT_PATH" ]; then
    echo "Fixing permissions for $DOMAIN certificates..."
    # Allow read access to the directory structure
    sudo chmod 755 /etc/letsencrypt/live
    sudo chmod 755 /etc/letsencrypt/archive
    
    # Allow read access to the specific cert files
    sudo chmod 644 "$CERT_PATH/fullchain.pem"
    sudo chmod 644 "$CERT_PATH/privkey.pem"
    
    echo "Permissions updated."
else
    echo "WARNING: Certificate directory $CERT_PATH not found."
    echo "If you haven't generated certificates yet, Caddy will fail to start with the 'tls' directive."
fi

# 3. Check PHP Socket
PHP_SOCKET="/run/php/php8.3-fpm.sock"
if [ ! -S "$PHP_SOCKET" ]; then
    echo "WARNING: PHP Socket $PHP_SOCKET not found."
    echo "Please check your PHP version (ls /run/php/) and update the Caddyfile."
fi

echo "Done. Try restarting Caddy: sudo systemctl restart caddy"
