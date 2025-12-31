<!DOCTYPE html>
<html lang="es">
<head>
        <?php
        // URLs para assets y rutas
        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
        
        if (!isset($assetsUrl)) {
            $assetsUrl = $scriptDir;
        }
        
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
    <title><?= htmlspecialchars($title ?? 'Panel Administrativo') ?> - Presentismo</title>
    <?php $cssVersion = (defined('PUBLIC_PATH') && file_exists(PUBLIC_PATH . '/css/styles.css')) ? filemtime(PUBLIC_PATH . '/css/styles.css') : time(); ?>
    <link rel="stylesheet" href="<?= $assetsUrl ?>/css/styles.css?v=<?= $cssVersion ?>">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Sidebar -->
        <aside class="fixed top-0 left-0 z-50 w-64 h-screen bg-slate-900 text-white transform lg:translate-x-0 lg:relative lg:static transition-transform"
               x-data="{ open: false }"
               @click.outside="open = false"
               :class="{ '-translate-x-full': !open, 'translate-x-0': open }">
            
            <div class="p-6 border-b border-slate-700">
                <h1 class="text-xl font-bold">
                    <i class="fas fa-crown text-amber-400 mr-2"></i>Presentismo Admin
                </h1>
            </div>

            <nav class="p-4 space-y-2">
                <a href="<?= $baseUrl ?>/admin/dashboard" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-slate-800">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
                <a href="<?= $baseUrl ?>/admin/empresas" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-slate-800">
                    <i class="fas fa-building"></i> Empresas
                </a>
                <a href="<?= $baseUrl ?>/admin/usuarios" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-slate-800">
                    <i class="fas fa-users"></i> Superadmins
                </a>
                <a href="<?= $baseUrl ?>/admin/planes" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-slate-800">
                    <i class="fas fa-layer-group"></i> Planes
                </a>
            </nav>

            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-700">
                <div class="text-sm text-slate-300 mb-3">
                    <p><?= htmlspecialchars(\Core\AdminAuth::name() ?? 'Admin') ?></p>
                </div>
                <a href="<?= $baseUrl ?>/admin/logout" class="flex items-center gap-2 px-4 py-2 text-red-400 hover:bg-red-900/20 rounded-lg">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex flex-col flex-1 w-full">
            <!-- Header -->
            <header class="sticky top-0 z-40 bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-6 py-4">
                    <button @click="open = !open" class="lg:hidden text-gray-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h2 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($title ?? 'Panel Administrativo') ?></h2>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-600"><?= date('d/m/Y H:i') ?></span>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            <div class="px-6 py-4">
                <?php if (\Core\Session::has('success')): ?>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <p class="text-green-700"><i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars(\Core\Session::get('success')) ?></p>
                </div>
                <?php endif; ?>

                <?php if (\Core\Session::has('error')): ?>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <p class="text-red-700"><i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars(\Core\Session::get('error')) ?></p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Page Content -->
            <div class="flex-1 px-6 pb-6">
                <?php include $viewPath; ?>
            </div>
            <footer class="px-6 pb-6 text-sm text-gray-500 flex flex-wrap gap-3">
                <a href="<?= $baseUrl ?>/privacidad" class="hover:text-primary-400">Privacidad</a>
                <span aria-hidden="true">•</span>
                <a href="<?= $baseUrl ?>/cookies" class="hover:text-primary-400">Cookies</a>
                <span aria-hidden="true">•</span>
                <a href="<?= $baseUrl ?>/terminos" class="hover:text-primary-400">Términos</a>
            </footer>
        </main>
        </div>

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
