<?php
function maskName($name) {
    $parts = explode(" ", $name);
    $maskedParts = [];

    foreach ($parts as $part) {
        if (strlen($part) > 2) {
            $maskedParts[] = $part[0] . str_repeat("*", strlen($part) - 2) . $part[strlen($part) - 1];
        } else {
            $maskedParts[] = $part;
        }
    }

    return implode(" ", $maskedParts);
}
?>