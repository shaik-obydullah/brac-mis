<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Branch;
use App\Models\Migrant;
use App\Models\Report;
use App\Models\Returnee;
use App\Models\Staff;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function export(Request $request, string $type, string $format)
    {
        $method = 'export' . str_replace(' ', '', ucwords(str_replace('-', ' ', $type))) . ucfirst($format);
        if (method_exists($this, $method)) {
            $response = $this->$method();
            if (auth()->check()) {
                $title = str_replace('-', ' ', $type);
                Report::create([
                    'type' => $type,
                    'title' => ucwords($title) . " ({$format})",
                    'parameters' => ['format' => $format, 'exported_at' => now()->toDateTimeString()],
                    'generated_by' => auth()->id(),
                    'file_path' => null,
                ]);
            }
            return $response;
        }
        return redirect()->route('reports.index')->with('error', 'Export type not supported.');
    }

    public function exportBeneficiariesCsv()
    {
        $beneficiaries = Beneficiary::with('branch')->get();
        $csv = $this->toCsv($beneficiaries->toArray(), ['brac_id', 'name', 'gender', 'phone', 'occupation', 'monthly_income', 'status']);
        return response($csv, 200)->header('Content-Type', 'text/csv')->header('Content-Disposition', 'attachment; filename="beneficiaries.csv"');
    }

    public function exportBeneficiariesPdf()
    {
        $beneficiaries = Beneficiary::with('branch')->get();
        $pdf = Pdf::loadView('exports.pdf', ['title' => 'Beneficiaries Report', 'headers' => ['BRAC ID', 'Name', 'Gender', 'Phone', 'Occupation', 'Monthly Income', 'Status'], 'rows' => $beneficiaries->map(fn($b) => [$b->brac_id, $b->name, $b->gender, $b->phone, $b->occupation, $b->monthly_income, $b->status])]);
        return $pdf->download('beneficiaries.pdf');
    }

    public function exportBeneficiariesExcel()
    {
        return Excel::download(new \App\Exports\GenericExport('App\\Models\\Beneficiary', ['brac_id', 'name', 'gender', 'phone', 'occupation', 'monthly_income', 'status']), 'beneficiaries.xlsx');
    }

    public function exportMigrantsCsv()
    {
        $migrants = Migrant::with('destinationCountry')->get();
        $data = $migrants->map(fn($m) => ['brac_id' => $m->brac_id, 'name' => $m->name, 'gender' => $m->gender, 'phone' => $m->phone, 'destination' => $m->destinationCountry?->name, 'status' => $m->status])->toArray();
        $csv = $this->toCsv($data);
        return response($csv, 200)->header('Content-Type', 'text/csv')->header('Content-Disposition', 'attachment; filename="migrants.csv"');
    }

    public function exportMigrantsPdf()
    {
        $migrants = Migrant::with('destinationCountry')->get();
        $rows = $migrants->map(fn($m) => [$m->brac_id, $m->name, $m->gender, $m->phone, $m->destinationCountry?->name, $m->status]);
        $pdf = Pdf::loadView('exports.pdf', ['title' => 'Migrants Report', 'headers' => ['BRAC ID', 'Name', 'Gender', 'Phone', 'Destination', 'Status'], 'rows' => $rows]);
        return $pdf->download('migrants.pdf');
    }

    public function exportMigrantsExcel()
    {
        return Excel::download(new \App\Exports\GenericExport('App\\Models\\Migrant', ['brac_id', 'name', 'gender', 'phone', 'status']), 'migrants.xlsx');
    }

    public function exportReturneesCsv()
    {
        $returnees = Returnee::with('originCountry', 'migrant')->get();
        $data = $returnees->map(fn($r) => ['name' => $r->migrant?->name ?? 'N/A', 'return_date' => $r->return_date, 'origin' => $r->originCountry?->name, 'status' => $r->current_status])->toArray();
        $csv = $this->toCsv($data);
        return response($csv, 200)->header('Content-Type', 'text/csv')->header('Content-Disposition', 'attachment; filename="returnees.csv"');
    }

    public function exportReturneesPdf()
    {
        $returnees = Returnee::with('originCountry', 'migrant')->get();
        $rows = $returnees->map(fn($r) => [$r->migrant?->name ?? 'N/A', $r->return_date, $r->originCountry?->name, $r->current_status]);
        $pdf = Pdf::loadView('exports.pdf', ['title' => 'Returnees Report', 'headers' => ['Name', 'Return Date', 'Origin Country', 'Status'], 'rows' => $rows]);
        return $pdf->download('returnees.pdf');
    }

    public function exportReturneesExcel()
    {
        return Excel::download(new \App\Exports\GenericExport('App\\Models\\Returnee', ['id', 'return_date', 'return_reason', 'current_status']), 'returnees.xlsx');
    }

    public function exportStaffCsv()
    {
        $staff = Staff::with('user', 'branch')->get();
        $data = $staff->map(fn($s) => ['employee_id' => $s->employee_id, 'name' => $s->user?->name, 'email' => $s->user?->email, 'designation' => $s->designation, 'branch' => $s->branch?->name, 'phone' => $s->phone])->toArray();
        $csv = $this->toCsv($data);
        return response($csv, 200)->header('Content-Type', 'text/csv')->header('Content-Disposition', 'attachment; filename="staff.csv"');
    }

    public function exportStaffPdf()
    {
        $staff = Staff::with('user', 'branch')->get();
        $rows = $staff->map(fn($s) => [$s->employee_id, $s->user?->name, $s->user?->email, $s->designation, $s->branch?->name, $s->phone]);
        $pdf = Pdf::loadView('exports.pdf', ['title' => 'Staff Report', 'headers' => ['Employee ID', 'Name', 'Email', 'Designation', 'Branch', 'Phone'], 'rows' => $rows]);
        return $pdf->download('staff.pdf');
    }

    public function exportStaffExcel()
    {
        return Excel::download(new \App\Exports\GenericExport('App\\Models\\Staff', ['employee_id', 'designation', 'phone']), 'staff.xlsx');
    }

    public function exportBranchesCsv()
    {
        $branches = Branch::all();
        $csv = $this->toCsv($branches->toArray(), ['name', 'code', 'district', 'division', 'status']);
        return response($csv, 200)->header('Content-Type', 'text/csv')->header('Content-Disposition', 'attachment; filename="branches.csv"');
    }

    public function exportBranchesPdf()
    {
        $branches = Branch::all();
        $rows = $branches->map(fn($b) => [$b->name, $b->code, $b->district, $b->division, $b->status ? 'Active' : 'Inactive']);
        $pdf = Pdf::loadView('exports.pdf', ['title' => 'Branches Report', 'headers' => ['Name', 'Code', 'District', 'Division', 'Status'], 'rows' => $rows]);
        return $pdf->download('branches.pdf');
    }

    public function exportBranchesExcel()
    {
        return Excel::download(new \App\Exports\GenericExport('App\\Models\\Branch', ['name', 'code', 'district', 'division', 'status']), 'branches.xlsx');
    }

    public function exportBeneficiariesByBranchCsv()
    {
        $groups = Beneficiary::selectRaw('branch_id, count(*) as total')->groupBy('branch_id')->with('branch')->get();
        $data = $groups->map(fn($g) => ['branch' => $g->branch?->name ?? 'N/A', 'total' => $g->total])->toArray();
        $csv = $this->toCsv($data, ['branch', 'total']);
        return response($csv, 200)->header('Content-Type', 'text/csv')->header('Content-Disposition', 'attachment; filename="beneficiaries-by-branch.csv"');
    }

    public function exportBeneficiariesByBranchPdf()
    {
        $groups = Beneficiary::selectRaw('branch_id, count(*) as total')->groupBy('branch_id')->with('branch')->get();
        $rows = $groups->map(fn($g) => [$g->branch?->name ?? 'N/A', $g->total]);
        $pdf = Pdf::loadView('exports.pdf', ['title' => 'Beneficiaries by Branch', 'headers' => ['Branch', 'Total'], 'rows' => $rows]);
        return $pdf->download('beneficiaries-by-branch.pdf');
    }

    public function exportBeneficiariesByGenderCsv()
    {
        $groups = Beneficiary::selectRaw('gender, count(*) as total')->groupBy('gender')->get();
        $data = $groups->map(fn($g) => ['gender' => $g->gender, 'total' => $g->total])->toArray();
        $csv = $this->toCsv($data, ['gender', 'total']);
        return response($csv, 200)->header('Content-Type', 'text/csv')->header('Content-Disposition', 'attachment; filename="beneficiaries-by-gender.csv"');
    }

    public function exportBeneficiariesByGenderPdf()
    {
        $groups = Beneficiary::selectRaw('gender, count(*) as total')->groupBy('gender')->get();
        $rows = $groups->map(fn($g) => [$g->gender, $g->total]);
        $pdf = Pdf::loadView('exports.pdf', ['title' => 'Beneficiaries by Gender', 'headers' => ['Gender', 'Total'], 'rows' => $rows]);
        return $pdf->download('beneficiaries-by-gender.pdf');
    }

    public function exportMigrantsByDestinationCsv()
    {
        $groups = Migrant::selectRaw('destination_country_id, count(*) as total')->groupBy('destination_country_id')->with('destinationCountry')->get();
        $data = $groups->map(fn($g) => ['destination' => $g->destinationCountry?->name ?? 'N/A', 'total' => $g->total])->toArray();
        $csv = $this->toCsv($data, ['destination', 'total']);
        return response($csv, 200)->header('Content-Type', 'text/csv')->header('Content-Disposition', 'attachment; filename="migrants-by-destination.csv"');
    }

    public function exportMigrantsByDestinationPdf()
    {
        $groups = Migrant::selectRaw('destination_country_id, count(*) as total')->groupBy('destination_country_id')->with('destinationCountry')->get();
        $rows = $groups->map(fn($g) => [$g->destinationCountry?->name ?? 'N/A', $g->total]);
        $pdf = Pdf::loadView('exports.pdf', ['title' => 'Migrants by Destination', 'headers' => ['Destination', 'Total'], 'rows' => $rows]);
        return $pdf->download('migrants-by-destination.pdf');
    }

    public function exportMonthlySummaryPdf()
    {
        $groups = Beneficiary::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, count(*) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->get();
        $rows = $groups->map(fn($g) => [date('F', mktime(0, 0, 0, $g->month, 1)), $g->total]);
        $pdf = Pdf::loadView('exports.pdf', ['title' => 'Monthly Summary - ' . date('Y'), 'headers' => ['Month', 'Total Beneficiaries'], 'rows' => $rows]);
        return $pdf->download('monthly-summary.pdf');
    }

    public function exportMonthlySummaryExcel()
    {
        return Excel::download(new \App\Exports\GenericExport('App\\Models\\Beneficiary', ['id', 'brac_id', 'name', 'gender', 'created_at']), 'monthly-summary.xlsx');
    }

    public function exportYearlyOverviewPdf()
    {
        $groups = Beneficiary::selectRaw('YEAR(created_at) as year, count(*) as total')
            ->groupBy(DB::raw('YEAR(created_at)'))
            ->orderBy(DB::raw('YEAR(created_at)'))
            ->get();
        $rows = $groups->map(fn($g) => [$g->year, $g->total]);
        $pdf = Pdf::loadView('exports.pdf', ['title' => 'Yearly Overview', 'headers' => ['Year', 'Total Beneficiaries'], 'rows' => $rows]);
        return $pdf->download('yearly-overview.pdf');
    }

    public function exportYearlyOverviewExcel()
    {
        return Excel::download(new \App\Exports\GenericExport('App\\Models\\Beneficiary', ['id', 'brac_id', 'name', 'gender', 'created_at']), 'yearly-overview.xlsx');
    }

    public function exportFullSystemExcel()
    {
        return Excel::download(new \App\Exports\GenericExport('App\\Models\\Beneficiary', ['id', 'name']), 'full-system.xlsx');
    }

    private function toCsv(array $data, ?array $columns = null): string
    {
        $columns = $columns ?? (count($data) > 0 ? array_keys($data[0]) : []);
        $output = fopen('php://temp', 'r+');
        fputcsv($output, $columns);
        foreach ($data as $row) {
            $vals = [];
            foreach ($columns as $col) {
                $vals[] = $row[$col] ?? '';
            }
            fputcsv($output, $vals);
        }
        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);
        return $content;
    }
}
