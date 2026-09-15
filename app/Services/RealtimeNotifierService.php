<?php

namespace App\Services;

class RealtimeNotifierService
{
    /**
     * Mempublikasikan Event Real-time ke sistem/cache/broker
     *
     * @param string $eventName Nama Event (e.g. OrderCreated, OrderStatusUpdated, ProductAvailabilityUpdated)
     * @param array $payload Payload Data Event
     */
    public function broadcast(string $eventName, array $payload = []): void
    {
        $logFile = WRITEPATH . 'cache/realtime_events.json';
        
        $eventData = [
            'event'     => $eventName,
            'timestamp' => microtime(true),
            'data'      => $payload,
        ];

        // Simpan event snapshot untuk dibaca oleh SSE/Polling/WebSocket Engine
        file_put_contents($logFile, json_encode($eventData));
    }
}