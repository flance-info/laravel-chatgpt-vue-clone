<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SseController extends Controller
{
    public function index()
    {
        $response = new StreamedResponse();
        $response->setCallback(function () {
            // Example event
            echo "data: " . json_encode(['message' => 'Hello worlddddd']) . "\n\n";
            ob_flush();
            flush();
            sleep(0.1); // Wait for a second to simulate real-time data
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }
}
