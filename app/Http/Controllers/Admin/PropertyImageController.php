<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PropertyImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PropertyImageController extends Controller
{
    public function destroy(PropertyImage $image)
    {
        $this->authorizeAgainstProperty($image);

        if (! str_starts_with($image->path, 'http')) {
            Storage::disk('public')->delete($image->path);
        }

        $image->delete();

        return back()->with('success', 'Imagem removida.');
    }

    public function setCover(PropertyImage $image)
    {
        $this->authorizeAgainstProperty($image);

        $image->property->images()->update(['is_cover' => false]);
        $image->update(['is_cover' => true]);

        return back()->with('success', 'Foto de capa atualizada.');
    }

    private function authorizeAgainstProperty(PropertyImage $image): void
    {
        $user = Auth::user();

        abort_unless(
            $user->hasRole('admin') || $image->property->agent_id === $user->id,
            403
        );
    }
}
