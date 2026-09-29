<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\ListingAgreement;
use App\Models\Property;
use App\Models\PropertyOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListingAgreementController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $agreements = ListingAgreement::query()
            ->whereHas('property', fn ($q) => $q->where('organization_id', $user->organization_id))
            ->when(! $user->hasRole('admin'), fn ($q) => $q->where('captor_user_id', $user->id))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->with(['property', 'captor'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.listings.index', compact('agreements'));
    }

    public function create()
    {
        $properties = Property::orderBy('title')->get();

        return view('admin.listings.create', compact('properties'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $agreement = ListingAgreement::create($data);

        $this->syncOwner($request, $agreement->property_id);

        return redirect()->route('admin.listings.edit', $agreement)->with('success', 'Captação registrada.');
    }

    public function edit(ListingAgreement $listing)
    {
        $properties = Property::orderBy('title')->get();
        $listing->load('property.owners');

        return view('admin.listings.edit', ['agreement' => $listing, 'properties' => $properties]);
    }

    public function update(Request $request, ListingAgreement $listing)
    {
        $data = $this->validated($request);

        $listing->update($data);

        $this->syncOwner($request, $listing->property_id);

        return back()->with('success', 'Captação atualizada.');
    }

    public function destroy(ListingAgreement $listing)
    {
        $listing->delete();

        return redirect()->route('admin.listings.index')->with('success', 'Captação removida.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'property_id' => ['required', 'exists:properties,id'],
            'captor_user_id' => ['nullable', 'exists:users,id'],
            'listing_type' => ['required', 'in:OPEN,EXCLUSIVE,SIGNATURE'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'commission_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'status' => ['required', 'in:PROSPECT,CONTACTED,MEETING,VALUATION,PROPOSAL,CONTRACT,ONBOARDING,ACTIVE,EXPIRED,CANCELLED'],
        ]);
    }

    private function syncOwner(Request $request, int $propertyId): void
    {
        if (! $request->filled('owner_name')) {
            return;
        }

        $owner = Contact::firstOrCreate(
            [
                'organization_id' => Auth::user()->organization_id,
                'email' => $request->input('owner_email'),
            ],
            [
                'type' => 'PERSON',
                'full_name' => $request->input('owner_name'),
                'mobile' => $request->input('owner_mobile'),
                'owner_user_id' => Auth::id(),
                'status' => 'ACTIVE',
            ]
        );

        PropertyOwner::firstOrCreate(
            ['property_id' => $propertyId, 'contact_id' => $owner->id],
            ['primary_contact' => true, 'authorization_status' => 'AUTHORIZED']
        );
    }
}
