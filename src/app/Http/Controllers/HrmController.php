<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Designation;
use App\Models\Staff;
use App\Models\StaffIncentive;

class HrmController extends Controller
{
    public function index()
    {
        $totalStaff = Staff::count();
        $totalDesignations = Designation::count();
        $totalBranches = Branch::count();

        $paidIncentives = StaffIncentive::where('status', StaffIncentive::STATUS_PAID)->sum('amount');
        $pendingIncentives = StaffIncentive::where('status', StaffIncentive::STATUS_PENDING)->count();

        $recentIncentives = StaffIncentive::with('staff.user')
            ->latest('incentive_date')
            ->latest('id')
            ->take(5)
            ->get();

        $staffByBranch = Branch::withCount('staff')
            ->orderByDesc('staff_count')
            ->take(5)
            ->get();

        $staffByDesignation = Staff::selectRaw('designation, count(*) as total')
            ->whereNotNull('designation')
            ->where('designation', '!=', '')
            ->groupBy('designation')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        return view('hrm.index', compact(
            'totalStaff',
            'totalDesignations',
            'totalBranches',
            'paidIncentives',
            'pendingIncentives',
            'recentIncentives',
            'staffByBranch',
            'staffByDesignation',
        ));
    }
}
