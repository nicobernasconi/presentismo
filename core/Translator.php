<?php

namespace Core;

class Translator
{
    /**
     * Idioma actual
     * @var string
     */
    private static $language;

    /**
     * Cache de traducciones cargadas
     * @var array
     */
    private static $translations = [];

    /**
     * Inicializar el traductor con un idioma específico
     * @param string $language Código de idioma (ej: 'es', 'en')
     */
    public static function init($language = null)
    {
        $config = config('language');
        
        // Si no se especifica idioma, usar el del usuario o el predeterminado
        if (!$language) {
            // Primero verificar si hay idioma en sesión (PRIORITARIO)
            if (!empty($_SESSION['language'])) {
                $language = $_SESSION['language'];
            }
            // Luego intentar obtener del usuario autenticado
            elseif (class_exists('\Core\Auth') && \Core\Auth::check()) {
                $user = \Core\Auth::user();
                if ($user && (isset($user->language) || isset($user['language']))) {
                    $language = is_object($user) ? $user->language : $user['language'];
                } else {
                    $language = $config['default'];
                }
            } else {
                $language = $config['default'];
            }
        }

        // Validar que el idioma sea soportado
        if (!isset($config['supported'][$language])) {
            $language = $config['default'];
        }

        self::$language = $language;
    }

    /**
     * Obtener una traducción
     * @param string $key Clave de traducción (ej: 'common.save', 'dashboard.title')
     * @param array $replace Reemplazos en la traducción (ej: [':name' => 'Juan'])
     * @return string Traducción
     */
    public static function get($key, $replace = [])
    {
        if (!self::$language) {
            self::init();
        }

        // Dividir la clave en archivo y clave
        $parts = explode('.', $key, 2);
        if (count($parts) !== 2) {
            return $key; // Retornar la clave si no es válida
        }

        $file = $parts[0];
        $itemKey = $parts[1];

        // Cargar el archivo de traducción si no está en caché
        if (!isset(self::$translations[self::$language][$file])) {
            self::loadFile($file);
        }

        // Obtener la traducción
        $translation = self::$translations[self::$language][$file][$itemKey] ?? $key;

        // Hacer reemplazos si existen
        if (!empty($replace)) {
            foreach ($replace as $search => $value) {
                $translation = str_replace($search, $value, $translation);
            }
        }

        return $translation;
    }

    /**
     * Cargar un archivo de traducción
     * @param string $file Nombre del archivo sin extensión
     */
    private static function loadFile($file)
    {
        $config = config('language');
        $path = $config['path'] . self::$language . '/' . $file . '.php';

        if (!file_exists($path)) {
            self::$translations[self::$language][$file] = [];
            return;
        }

        self::$translations[self::$language][$file] = require $path;
    }

    /**
     * Obtener el idioma actual
     * @return string
     */
    public static function getLanguage()
    {
        if (!self::$language) {
            self::init();
        }
        return self::$language;
    }

    /**
     * Cambiar el idioma actual
     * @param string $language
     */
    public static function setLanguage($language)
    {
        $config = config('language');
        
        if (isset($config['supported'][$language])) {
            self::$language = $language;
            // Limpiar caché COMPLETAMENTE
            self::$translations = [];
        }
    }

    /**
     * Verificar si existe una traducción
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        if (!self::$language) {
            self::init();
        }

        $parts = explode('.', $key, 2);
        if (count($parts) !== 2) {
            return false;
        }

        $file = $parts[0];
        $itemKey = $parts[1];

        if (!isset(self::$translations[self::$language][$file])) {
            self::loadFile($file);
        }

        return isset(self::$translations[self::$language][$file][$itemKey]);
    }
}
