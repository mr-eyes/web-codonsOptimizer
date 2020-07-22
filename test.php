<?php
ini_set("allow_url_fopen", 1);
if (ini_get("allow_url_fopen") == 1) {
echo "allow_url_fopen is ON";
} else {
echo "allow_url_fopen is OFF";
}
print ini_get("allow_url_fopen");