<?php
/**
 * Router
 *
 * File    : router.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Kelas Router sederhana berbasis associative array.
 * Tidak bergantung pada framework apapun.
 *
 * Fitur:
 *   - Route statis  : '/perpustakaan'
 *   - Route dinamis : '/perpustakaan/{slug}'
 *   - Redirect route: '/admin' → external URL
 *   - Custom 404 handler
 *   - Middleware sederhana (callable)
 *
 * Usage (di web.php):
 *   $router = new Router();
 *   $router->get('/', 'custom/pages/landing.php');
 *   $router->get('/perpustakaan/{slug}', 'custom/pages/libraries/library-detail.php');
 *   $router->redirect('/admin', '/baca-di-teras/slims/admin/');
 *   $router->set404('custom/pages/404.php');
 *   $router->dispatch();
 */

class Router
{
    /** @var array<string, array{file: string, params: array, middleware: array}> */
    private array $routes = [];

    /** @var array<string, string> Redirect routes: path → target URL */
    private array $redirects = [];

    /** @var string Path ke file 404 */
    private string $notFoundPage = '';

    /** @var string Base path dari RewriteBase (contoh: '/baca-di-teras') */
    private string $basePath = '';

    /**
     * @param string $basePath  Harus sesuai RewriteBase di .htaccess
     */
    public function __construct(string $basePath = '')
    {
        $this->basePath = rtrim($basePath, '/');
    }

    // ── Registrasi Route ──────────────────────────────────────

    /**
     * Daftarkan route GET.
     *
     * @param  string   $path        Contoh: '/' atau '/perpustakaan/{slug}'
     * @param  string   $file        Path relatif ke file PHP view
     * @param  callable[] $middleware Array callable yang dijalankan sebelum view
     * @return self
     */
    public function get(string $path, string $file, array $middleware = []): self
    {
        $this->routes[trim($path, '/')] = [
            'file'       => $file,
            'middleware' => $middleware,
            'params'     => [],
        ];
        return $this;
    }

    /**
     * Daftarkan redirect permanen (301) atau sementara (302).
     *
     * @param  string $path    Route yang ditangkap
     * @param  string $target  URL tujuan
     * @param  int    $code    301 atau 302
     * @return self
     */
    public function redirect(string $path, string $target, int $code = 302): self
    {
        $this->redirects[trim($path, '/')] = [
            'target' => $target,
            'code'   => $code,
        ];
        return $this;
    }

    /**
     * Set custom 404 page.
     *
     * @param  string $file  Path file 404
     * @return self
     */
    public function set404(string $file): self
    {
        $this->notFoundPage = $file;
        return $this;
    }

    // ── Dispatch ──────────────────────────────────────────────

    /**
     * Proses request dan jalankan route yang cocok.
     */
    public function dispatch(): void
    {
        $requestUri  = $_SERVER['REQUEST_URI'] ?? '/';
        $requestPath = $this->parseRequestPath($requestUri);

        // 1. Cek redirect route
        if (isset($this->redirects[$requestPath])) {
            $redirect = $this->redirects[$requestPath];
            http_response_code($redirect['code']);
            header('Location: ' . $redirect['target']);
            exit;
        }

        // 2. Cek route statis (exact match)
        if (isset($this->routes[$requestPath])) {
            $route = $this->routes[$requestPath];
            $this->runMiddleware($route['middleware']);
            $this->loadView($route['file'], []);
            return;
        }

        // 3. Cek route dinamis (dengan parameter {slug}, {id}, dst.)
        foreach ($this->routes as $pattern => $route) {
            $params = $this->matchDynamic($pattern, $requestPath);
            if ($params !== null) {
                $this->runMiddleware($route['middleware']);
                $this->loadView($route['file'], $params);
                return;
            }
        }

        // 4. Tidak ditemukan → 404
        $this->show404();
    }

    // ── Internal helpers ──────────────────────────────────────

    /**
     * Parse path dari REQUEST_URI (hapus base path dan query string).
     *
     * @param  string $requestUri
     * @return string Path bersih, contoh: 'perpustakaan/perpustakaan-utama'
     */
    private function parseRequestPath(string $requestUri): string
    {
        // Hapus query string
        $path = parse_url($requestUri, PHP_URL_PATH) ?? '/';

        // Hapus base path
        if ($this->basePath !== '' && str_starts_with($path, $this->basePath)) {
            $path = substr($path, strlen($this->basePath));
        }

        return trim($path, '/');
    }

    /**
     * Cocokkan route dinamis, kembalikan array params atau null jika tidak cocok.
     *
     * Contoh: pattern='perpustakaan/{slug}', path='perpustakaan/perpustakaan-utama'
     *         → ['slug' => 'perpustakaan-utama']
     *
     * @param  string     $pattern
     * @param  string     $path
     * @return array|null
     */
    private function matchDynamic(string $pattern, string $path): ?array
    {
        // Ubah {param} menjadi named regex group (?P<param>[^/]+)
        $regex = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $path, $matches)) {
            return null;
        }

        // Ambil hanya named captures (bukan numerik)
        $params = [];
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }
        return $params;
    }

    /**
     * Jalankan semua middleware secara berurutan.
     * Middleware adalah callable yang menerima tidak ada argumen
     * dan harus memanggil exit() jika ingin menghentikan request.
     *
     * @param  callable[] $middleware
     */
    private function runMiddleware(array $middleware): void
    {
        foreach ($middleware as $fn) {
            if (is_callable($fn)) {
                $fn();
            }
        }
    }

    /**
     * Load file view PHP. Parameter route tersedia via $routeParams.
     *
     * @param  string $file
     * @param  array  $routeParams  Array parameter dinamis dari URL
     */
    private function loadView(string $file, array $routeParams): void
    {
        // Buat variabel $routeParams tersedia di view
        // View bisa mengakses: $routeParams['slug'], $routeParams['id'], dst.
        extract(['routeParams' => $routeParams]);

        $fullPath = ROOT_PATH . '/' . ltrim($file, '/');

        if (!file_exists($fullPath)) {
            error_log("[BDT Router] View file tidak ditemukan: {$fullPath}");
            $this->show404();
            return;
        }

        require $fullPath;
    }

    /**
     * Tampilkan halaman 404.
     */
    private function show404(): void
    {
        http_response_code(404);

        $file404 = ROOT_PATH . '/' . ltrim($this->notFoundPage, '/');

        if ($this->notFoundPage && file_exists($file404)) {
            require $file404;
        } else {
            echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>404 – Halaman Tidak Ditemukan</title></head>'
               . '<body style="font-family:sans-serif;text-align:center;padding:80px;">'
               . '<h1>404</h1><p>Halaman tidak ditemukan.</p>'
               . '<a href="/baca-di-teras/">Kembali ke Beranda</a>'
               . '</body></html>';
        }
    }
}
