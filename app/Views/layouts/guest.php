<!DOCTYPE html>
<html lang="es">
<head>
        <?php
        // URLs para assets y rutas
        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
        
        // $assetsUrl siempre apunta directamente a la carpeta public
        if (!isset($assetsUrl)) {
            $assetsUrl = $scriptDir;
        }
        
        // $baseUrl para rutas (puede incluir index.php?route=)
        if (!isset($baseUrl)) {
            if (function_exists('isModRewriteEnabled') && !isModRewriteEnabled()) {
                $baseUrl = $scriptDir . '/index.php?route=';
            } else {
                $baseUrl = '/presentismo/public';
            }
        }
        ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= \Core\Session::csrf() ?>">
    <title><?= $title ?? 'Sistema de Presentismo' ?></title>
    
    <!-- Tailwind CSS (cache busting) -->
    <?php $cssVersion = (defined('PUBLIC_PATH') && file_exists(PUBLIC_PATH . '/css/styles.css')) ? filemtime(PUBLIC_PATH . '/css/styles.css') : time(); ?>
    <link rel="stylesheet" href="<?= $assetsUrl ?>/css/styles.css?v=<?= $cssVersion ?>">
    
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
        <?= $content ?>

    <footer class="absolute bottom-4 inset-x-0 flex justify-center gap-4 text-sm text-gray-600">
        <a href="<?= $baseUrl ?>/privacidad" class="hover:text-primary-700">Privacidad</a>
        <span aria-hidden="true">•</span>
        <a href="<?= $baseUrl ?>/cookies" class="hover:text-primary-700">Cookies</a>
        <span aria-hidden="true">•</span>
        <a href="<?= $baseUrl ?>/terminos" class="hover:text-primary-700">Términos</a>
    </footer>

        <!-- Global Dialogs (Alpine.js) -->
        <div x-data="dialog()" x-init="init()" x-cloak>
                <div x-show="open" class="fixed inset-0 bg-black/40 z-50" x-transition.opacity></div>
                <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
                        <div class="w-full max-w-md bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-200">
                                        <h3 class="text-lg font-semibold text-gray-900" x-text="title"></h3>
                                </div>
                                <div class="px-6 py-5 text-gray-700">
                                        <p x-text="message"></p>
                                </div>
                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                                        <button x-show="type==='confirm'" @click="cancel()" type="button"
                                                        class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100">Cancelar</button>
                                        <button @click="ok()" type="button"
                                                        class="px-4 py-2 rounded-lg bg-primary-600 hover:bg-primary-700 text-white"
                                                        x-text="type==='alert' ? 'Entendido' : 'Confirmar'"></button>
                                </div>
                        </div>
                </div>
        </div>

        <script>
        function dialog() {
            return {
                open: false,
                type: 'alert',
                title: 'Confirmación',
                message: '',
                _resolve: null,
                init() {
                    window.Dialog = {
                        alert: (msg, title = 'Aviso') => this.show('alert', msg, title),
                        confirm: (msg, title = 'Confirmación') => this.show('confirm', msg, title)
                    };
                    document.addEventListener('submit', (e) => {
                        const form = e.target;
                        const msg = form?.dataset?.confirm;
                        if (msg) {
                            e.preventDefault();
                            window.Dialog.confirm(msg).then((ok) => { if (ok) form.submit(); });
                        }
                    }, true);
                    document.addEventListener('click', (e) => {
                        const el = e.target.closest('[data-confirm]');
                        if (el && el.tagName !== 'FORM') {
                            e.preventDefault();
                            const msg = el.getAttribute('data-confirm');
                            const href = el.getAttribute('href');
                            window.Dialog.confirm(msg).then((ok) => { if (ok && href) window.location.href = href; });
                        }
                    });
                },
                show(type, msg, title) {
                    this.type = type;
                    this.title = title;
                    this.message = msg;
                    this.open = true;
                    return new Promise((resolve) => { this._resolve = resolve; });
                },
                ok() { this.open = false; this._resolve?.(true); },
                cancel() { this.open = false; this._resolve?.(false); },
            }
        }
        </script>

</body>
</html>
