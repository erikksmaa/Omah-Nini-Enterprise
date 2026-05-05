<?php

if (!function_exists('getServerStartTime')) {
    function getServerStartTime()
    {
        $file = WRITEPATH . 'server_start.txt';
        if (!file_exists($file)) {
            file_put_contents($file, time());
        }
        return (int) file_get_contents($file);
    }
}

function updateServerStartTimeOnRestart()
{
    // Hanya dipanggil secara manual jika ingin mereset (opsional)
    $file = WRITEPATH . 'server_start.txt';
    file_put_contents($file, time());
}