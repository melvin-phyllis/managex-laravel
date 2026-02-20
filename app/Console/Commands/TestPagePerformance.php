<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Models\User;

class TestPagePerformance extends Command
{
    protected $signature = 'test:performance';
    protected $description = 'Test page render performance for all main routes';

    public function handle()
    {
        $this->info('');
        $this->info('===============================================');
        $this->info('  ManageX Performance Test Report');
        $this->info('  ' . now()->format('Y-m-d H:i:s'));
        $this->info('===============================================');
        $this->newLine();

        // ========== ADMIN PAGES ==========
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $this->error('No admin user found!');
            return 1;
        }

        $this->info("▶ ADMIN pages (user: {$admin->name})");

        $adminRoutes = [
            ['Admin Dashboard', '/admin/dashboard'],
            ['Admin Analytics', '/admin/analytics'],
            ['Admin Employees', '/admin/employees'],
            ['Admin Presences', '/admin/presences'],
            ['Admin Tasks', '/admin/tasks'],
            ['Admin Leaves', '/admin/leaves'],
            ['Admin Announcements', '/admin/announcements'],
            ['Admin Surveys', '/admin/surveys'],
            ['Admin Payrolls', '/admin/payrolls'],
            ['Admin Documents', '/admin/documents'],
            ['Admin Settings', '/admin/settings'],
            ['Admin Global Docs', '/admin/global-documents'],
            ['Admin Geolocation', '/admin/geolocation-zones'],
            ['Admin Doc Requests', '/admin/document-requests'],
        ];

        $allResults = [];

        Auth::login($admin);
        $adminResults = $this->testPages($adminRoutes);
        $allResults = array_merge($allResults, $adminResults);
        Auth::logout();

        // ========== EMPLOYEE PAGES ==========
        $employee = User::where('role', 'employee')->first();
        if (!$employee) {
            $this->warn('No employee user found! Skipping.');
            return 1;
        }

        $this->newLine();
        $this->info("▶ EMPLOYEE pages (user: {$employee->name})");

        $employeeRoutes = [
            ['Employee Dashboard', '/employee/dashboard'],
            ['Employee Presences', '/employee/presences'],
            ['Employee Tasks', '/employee/tasks'],
            ['Employee Leaves', '/employee/leaves'],
            ['Employee Documents', '/employee/documents'],
            ['Employee Surveys', '/employee/surveys'],
            ['Employee Announcements', '/employee/announcements'],
            ['Employee Settings', '/employee/settings'],
        ];

        Auth::login($employee);
        $empResults = $this->testPages($employeeRoutes);
        $allResults = array_merge($allResults, $empResults);
        Auth::logout();

        // ========== PUBLIC PAGES ==========
        $this->newLine();
        $this->info("▶ PUBLIC pages");

        $publicRoutes = [
            ['Landing Page', '/'],
            ['Login Page', '/login'],
        ];

        $pubResults = $this->testPages($publicRoutes);
        $allResults = array_merge($allResults, $pubResults);

        // ========== SUMMARY ==========
        $this->newLine();
        $this->info('===============================================');
        $this->info('  SUMMARY');
        $this->info('===============================================');

        $validResults = array_filter($allResults, fn($r) => $r['time'] > 0);
        $avgTime = count($validResults) > 0
            ? round(array_sum(array_column($validResults, 'time')) / count($validResults))
            : 0;

        $slowPages = array_filter($validResults, fn($r) => $r['time'] >= 1000);
        $sorted = collect($validResults)->sortByDesc('time');
        $fastest = $sorted->last();
        $slowest = $sorted->first();

        $this->info("  Pages tested:       " . count($validResults));
        $this->info("  Average render:     {$avgTime}ms");
        if ($fastest) $this->info("  Fastest page:       {$fastest['name']} ({$fastest['time']}ms)");
        if ($slowest) $this->info("  Slowest page:       {$slowest['name']} ({$slowest['time']}ms)");

        $heavyQueryPages = array_filter($validResults, fn($r) => $r['queries'] > 30);
        if (count($heavyQueryPages) > 0) {
            $this->newLine();
            $this->warn('  ⚠️  HEAVY QUERY PAGES (>30 queries):');
            usort($heavyQueryPages, fn($a, $b) => $b['queries'] <=> $a['queries']);
            foreach ($heavyQueryPages as $p) {
                $this->warn("     - {$p['name']}: {$p['queries']} queries ({$p['time']}ms)");
            }
        }

        if (count($slowPages) > 0) {
            $this->newLine();
            $this->warn('  ⚠️  SLOW PAGES (>1s):');
            usort($slowPages, fn($a, $b) => $b['time'] <=> $a['time']);
            foreach ($slowPages as $sp) {
                $this->warn("     - {$sp['name']}: {$sp['time']}ms ({$sp['queries']} queries)");
            }
        } else {
            $this->info('  ✅ All pages loaded under 1 second!');
        }

        $this->info('===============================================');
        $this->newLine();

        return 0;
    }

    private function testPages(array $routes): array
    {
        $results = [];
        $headers = ['Page', 'Status', 'Time', 'Size', 'Queries', 'Rating'];
        $rows = [];

        foreach ($routes as [$label, $uri]) {
            $result = $this->measurePage($label, $uri);
            $results[] = $result;
            $rows[] = [
                $result['name'],
                $result['status'],
                $result['time'] . 'ms',
                $result['size'],
                $result['queries'],
                $result['rating'],
            ];
        }

        $this->table($headers, $rows);

        return $results;
    }

    private function measurePage(string $label, string $uri): array
    {
        try {
            DB::enableQueryLog();
            DB::flushQueryLog();

            $request = \Illuminate\Http\Request::create($uri, 'GET');

            // Copy auth to the request
            if (Auth::check()) {
                $request->setUserResolver(fn() => Auth::user());
                $request->setLaravelSession(app('session.store'));
            }

            $startTime = microtime(true);
            $response = app()->handle($request);
            $renderTime = round((microtime(true) - $startTime) * 1000);

            $queries = count(DB::getQueryLog());
            DB::disableQueryLog();

            $status = $response->getStatusCode();
            $content = $response->getContent();
            $sizeKB = round(strlen($content) / 1024, 1) . 'KB';

            // Detect redirect (not real page)
            if ($status >= 300 && $status < 400) {
                $sizeKB = '-';
            }

            // Rating based on time
            if ($status >= 300 && $status < 400) {
                $rating = '↪ Redirect';
            } elseif ($status >= 400) {
                $rating = '❌ Error';
            } elseif ($renderTime < 300) {
                $rating = '✅ Fast';
            } elseif ($renderTime < 700) {
                $rating = '⚡ Good';
            } elseif ($renderTime < 1000) {
                $rating = '🔶 OK';
            } elseif ($renderTime < 2000) {
                $rating = '⚠️ Slow';
            } else {
                $rating = '❌ Very Slow';
            }

            // Reset app state to prevent bleeding between requests
            app()->forgetInstance('request');

            return [
                'name' => $label,
                'status' => $status,
                'time' => $renderTime,
                'size' => $sizeKB,
                'queries' => $queries,
                'rating' => $rating,
            ];
        } catch (\Throwable $e) {
            DB::disableQueryLog();
            $msg = substr($e->getMessage(), 0, 35);
            return [
                'name' => $label,
                'status' => 'ERR',
                'time' => 0,
                'size' => '-',
                'queries' => 0,
                'rating' => "❌ $msg",
            ];
        }
    }
}
