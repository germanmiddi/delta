<?php

return [

    /*
     * A dónde reporta: la API de NEXUS (sin /api) y el token de este
     * proyecto, que se genera en el panel. Sin alguno de los dos, el agente
     * no manda nada (y lo dice en el log del scheduler).
     */
    'url' => env('NEXUS_URL'),

    'token' => env('NEXUS_TOKEN'),

    // Para apagarlo en un entorno sin tocar el código (local, staging).
    'enabled' => env('NEXUS_ENABLED', true),

    // Segundos. Si NEXUS no responde, ese minuto se pierde: es una foto.
    'timeout' => 10,

    /*
     * Anotar cada request web (latencia, 4xx/5xx, usuarios logueados): un
     * append a un archivo por request. Se apaga con NEXUS_REQUESTS=false.
     */
    'requests' => env('NEXUS_REQUESTS', true),

    /*
     * Cuántos de los últimos requests van en cada reporte (método, ruta sin
     * query ni tokens, código, ms e IP), para verlos desde NEXUS. 0: ninguno.
     */
    'recent_requests' => env('NEXUS_RECENT_REQUESTS', 50),

    // Colas a medir de la conexión por defecto. Vacío: la cola por defecto.
    'queues' => [],

    // Dónde se anotan requests y excepciones. Null: storage/framework/cache/nexus-agent.
    'path' => null,

];
