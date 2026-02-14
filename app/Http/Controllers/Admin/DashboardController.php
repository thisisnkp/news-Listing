<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Plan;
use App\Models\Language;
use App\Models\TableRow;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'packages_count' => Package::count(),
            'plans_count' => Plan::count(),
            'total_rows' => TableRow::count(),
            'languages' => Language::active()->count(),
        ];

        $recentPackages = Package::withCount('plans')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentPackages'));
    }
}
