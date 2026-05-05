<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class ServerRestartCheck implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Pastikan helper terload
        helper('server');
        
        $session = session();
        if (!$session->get('logged_in')) {
            return;
        }

        $currentServerStart = getServerStartTime();
        $sessionServerStart = $session->get('server_start_time');

        if (!$sessionServerStart || (int)$currentServerStart !== (int)$sessionServerStart) {
            $session->destroy();
            return redirect()->to('/login')->with('error', 'Server telah direstart. Silakan login ulang.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}