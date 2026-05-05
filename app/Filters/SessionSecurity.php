<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class SessionSecurity implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        if (!$session->get('logged_in')) {
            return;
        }
        
        // Cek IP Address
        $currentIP = $request->getIPAddress();
        $sessionIP = $session->get('session_ip');
        
        if ($sessionIP && $sessionIP !== $currentIP) {
            $session->destroy();
            return redirect()->to('/login')->with('error', 'IP Address berubah, silakan login ulang.');
        }
        
        // Cek User Agent
        $currentUserAgent = $request->getUserAgent()->getAgentString();
        $sessionUserAgent = $session->get('session_user_agent');
        
        if ($sessionUserAgent && $sessionUserAgent !== $currentUserAgent) {
            $session->destroy();
            return redirect()->to('/login')->with('error', 'Browser berbeda, silakan login ulang.');
        }
        
        // Regenerasi session ID setiap 5 menit
        $lastRegen = $session->get('last_regeneration');
        $now = time();
        
        if (!$lastRegen || ($now - $lastRegen) > 300) {
            $session->regenerate(true);
            $session->set('last_regeneration', $now);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}