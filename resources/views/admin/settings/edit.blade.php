<x-admin-layout title="Configurações">
    <h1 class="text-2xl font-bold text-slate-900 mb-6">Configurações do site</h1>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-4xl">
        @csrf @method('PATCH')

        <div class="bg-white rounded-2xl border border-slate-100 p-6 space-y-4 lg:col-span-2">
            <h2 class="font-semibold text-slate-800">Identidade</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500">Nome da imobiliária</label>
                    <input type="text" name="site_name" required value="{{ old('site_name', $settings->site_name) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">CRECI (pessoa jurídica)</label>
                    <input type="text" name="creci_company" value="{{ old('creci_company', $settings->creci_company) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">Slogan</label>
                <input type="text" name="slogan" value="{{ old('slogan', $settings->slogan) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">Sobre a imobiliária</label>
                <textarea name="about_text" rows="4" class="mt-1 w-full rounded-lg border-slate-200 text-sm">{{ old('about_text', $settings->about_text) }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500">Logo</label>
                    <input type="file" name="logo" accept="image/*" class="mt-1 w-full text-sm">
                    @if($settings->logo_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($settings->logo_path) }}" class="h-10 mt-2">
                    @endif
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">Cor principal</label>
                    <input type="color" name="primary_color" value="{{ old('primary_color', $settings->primary_color) }}" class="mt-1 h-10 w-full rounded-lg border-slate-200">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 p-6 space-y-4">
            <h2 class="font-semibold text-slate-800">Contato</h2>
            <div>
                <label class="text-xs font-semibold text-slate-500">Telefone</label>
                <input type="text" name="phone" value="{{ old('phone', $settings->phone) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">WhatsApp (DDI+DDD+número, ex: 5511999999999)</label>
                <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $settings->whatsapp_number) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">E-mail</label>
                <input type="email" name="email" value="{{ old('email', $settings->email) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 p-6 space-y-4">
            <h2 class="font-semibold text-slate-800">Endereço e redes sociais</h2>
            <div>
                <label class="text-xs font-semibold text-slate-500">Endereço</label>
                <input type="text" name="address" value="{{ old('address', $settings->address) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500">Cidade</label>
                    <input type="text" name="city" value="{{ old('city', $settings->city) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">Estado</label>
                    <input type="text" name="state" maxlength="2" value="{{ old('state', $settings->state) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">Instagram</label>
                <input type="url" name="instagram_url" value="{{ old('instagram_url', $settings->instagram_url) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">Facebook</label>
                <input type="url" name="facebook_url" value="{{ old('facebook_url', $settings->facebook_url) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
            </div>
        </div>

        <div class="lg:col-span-2">
            <button type="submit" class="rounded-lg bg-brand-700 text-white font-semibold px-6 py-2.5 hover:bg-brand-800">
                Salvar configurações
            </button>
        </div>
    </form>
</x-admin-layout>
