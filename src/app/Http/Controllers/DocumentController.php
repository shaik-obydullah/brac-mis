<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\BeneficiaryDocument;
use App\Models\Migrant;
use App\Models\MigrantDocument;
use App\Services\AuditService;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function __construct(
        protected AuditService $auditService,
    ) {}

    public function beneficiaryIndex(Beneficiary $beneficiary)
    {
        $documents = $beneficiary->documents()->latest()->get();
        return view('documents.beneficiary-index', compact('beneficiary', 'documents'));
    }

    public function beneficiaryUpload(Request $request, Beneficiary $beneficiary)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $path = $request->file('file')->store("documents/beneficiaries/{$beneficiary->id}", 'public');

        $doc = BeneficiaryDocument::create([
            'beneficiary_id' => $beneficiary->id,
            'type' => $validated['type'],
            'file_path' => $path,
        ]);

        $this->auditService->logCreate($doc);

        return redirect()->route('beneficiaries.show', $beneficiary)
            ->with('success', 'Document uploaded successfully.');
    }

    public function beneficiaryDestroy(Beneficiary $beneficiary, BeneficiaryDocument $document)
    {
        $this->auditService->logDelete($document);
        $document->delete();

        return redirect()->route('beneficiaries.show', $beneficiary)
            ->with('success', 'Document deleted.');
    }

    public function migrantIndex(Migrant $migrant)
    {
        $documents = $migrant->documents()->latest()->get();
        return view('documents.migrant-index', compact('migrant', 'documents'));
    }

    public function migrantUpload(Request $request, Migrant $migrant)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $path = $request->file('file')->store("documents/migrants/{$migrant->id}", 'public');

        $doc = MigrantDocument::create([
            'migrant_id' => $migrant->id,
            'type' => $validated['type'],
            'file_path' => $path,
        ]);

        $this->auditService->logCreate($doc);

        return redirect()->route('migrants.show', $migrant)
            ->with('success', 'Document uploaded successfully.');
    }

    public function migrantDestroy(Migrant $migrant, MigrantDocument $document)
    {
        $this->auditService->logDelete($document);
        $document->delete();

        return redirect()->route('migrants.show', $migrant)
            ->with('success', 'Document deleted.');
    }
}
