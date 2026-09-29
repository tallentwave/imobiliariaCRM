<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function storeForLead(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
        ]);

        $file = $request->file('file');
        $path = $file->store("documents/leads/{$lead->id}", 'local');

        $lead->documents()->create([
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'uploaded_by' => Auth::id(),
        ]);

        return back()->with('success', 'Documento enviado com sucesso.');
    }

    public function download(Document $document)
    {
        $this->authorizeAccess($document);

        return Storage::disk('local')->download($document->path, $document->name);
    }

    public function destroy(Document $document)
    {
        $this->authorizeAccess($document);

        Storage::disk('local')->delete($document->path);
        $document->delete();

        return back()->with('success', 'Documento removido.');
    }

    private function authorizeAccess(Document $document): void
    {
        $documentable = $document->documentable;

        if ($documentable instanceof Lead) {
            $this->authorize('view', $documentable);

            return;
        }

        abort_unless(Auth::user()->hasRole('admin'), 403);
    }
}
