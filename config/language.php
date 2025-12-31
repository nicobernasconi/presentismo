<?php

/**
 * Configuración de idiomas del sistema
 */

return [
    // Idioma predeterminado
    'default' => 'es',

    // Idiomas soportados
    'supported' => [
        'es' => 'Español',
        'en' => 'English',
        'ca' => 'Català',
    ],

    // Ruta de archivos de idioma
    'path' => dirname(__DIR__) . '/resources/lang/',

    // Locale para PHP (date, strftime, etc)
    'locales' => [
        'es' => 'es_ES.UTF-8',
        'en' => 'en_US.UTF-8',
        'ca' => 'ca_ES.UTF-8',
    ],

    // Timezone
    'timezone' => 'Europe/Madrid',
];
