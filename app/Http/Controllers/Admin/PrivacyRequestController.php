<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrivacyRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class PrivacyRequestController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:admin|compliance'),
        ];
    }

    public function index()
    {
        $requests = PrivacyRequest::with(['contact', 'handledBy'])->latest()->paginate(20);

        return view('admin.privacy.index', compact('requests'));
    }

    public function update(Request $request, PrivacyRequest $privacyRequest)
    {
        $data = $request->validate([
            'status' => ['required', 'in:RECEIVED,IDENTITY_VERIFICATION,ANALYSIS,IN_PROGRESS,ANSWERED,CLOSED'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['handled_by'] = Auth::id();

        if ($data['status'] === 'CLOSED') {
            $data['closed_at'] = now();
        }

        $privacyRequest->update($data);

        return back()->with('success', 'Solicitação atualizada.');
    }
}
