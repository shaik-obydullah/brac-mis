<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Branch;
use App\Models\Migrant;
use App\Models\Returnee;

class DashboardController extends Controller
{
    public function index()
    {
        $beneficiaries = Beneficiary::count();
        $activeMigrants = Migrant::where('status', 'deployed')->count();
        $returnees = Returnee::count();
        $branches = Branch::count();
        $recentBeneficiaries = Beneficiary::with('branch')->latest()->take(5)->get();

        return view('dashboard', compact('beneficiaries', 'activeMigrants', 'returnees', 'branches', 'recentBeneficiaries'));
    }
}
