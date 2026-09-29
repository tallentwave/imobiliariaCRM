<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $contacts = Contact::query()
            ->where('organization_id', $user->organization_id)
            ->when(! $user->hasRole('admin') && ! $user->hasRole('financeiro') && ! $user->hasRole('compliance'), fn ($q) => $q->where('owner_user_id', $user->id))
            ->when($request->q, function ($q, $v) {
                $q->where(function ($q) use ($v) {
                    $q->where('full_name', 'like', "%{$v}%")
                        ->orWhere('email', 'like', "%{$v}%")
                        ->orWhere('mobile', 'like', "%{$v}%")
                        ->orWhere('cpf', 'like', "%{$v}%");
                });
            })
            ->with('owner')
            ->orderBy('full_name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.contacts.index', compact('contacts'));
    }

    public function show(Contact $contact)
    {
        $this->authorizeAccess($contact);

        $contact->load([
            'addresses', 'relationships.relatedContact', 'tags', 'owner',
            'leads.property', 'opportunities.proposals', 'ownedProperties',
            'consents', 'documents',
        ]);

        return view('admin.contacts.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        $this->authorizeAccess($contact);

        return view('admin.contacts.edit', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $this->authorizeAccess($contact);

        $data = $request->validate([
            'type' => ['required', 'in:PERSON,COMPANY'],
            'full_name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'cpf' => ['nullable', 'string', 'max:14'],
            'cnpj' => ['nullable', 'string', 'max:18'],
            'email' => ['nullable', 'email', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'birth_date' => ['nullable', 'date'],
            'status' => ['required', 'in:ACTIVE,INACTIVE,ARCHIVED'],
        ]);

        $contact->update($data);

        return back()->with('success', 'Contato atualizado.');
    }

    private function authorizeAccess(Contact $contact): void
    {
        $user = Auth::user();

        abort_unless(
            $user->hasRole('admin') || $user->hasRole('financeiro') || $user->hasRole('compliance') || $contact->owner_user_id === $user->id,
            403
        );
    }
}
