@props(['title', 'message', 'actionLabel' => null, 'actionHref' => null])

<div class="bg-white rounded-2xl border border-dashed border-slate-200 p-8 text-center">
    <div class="mx-auto h-11 w-11 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center mb-3">
        {{ $slot }}
    </div>
    <p class="text-sm font-semibold text-slate-700">{{ $title }}</p>
    <p class="text-sm text-slate-500 mt-1">{{ $message }}</p>
    @if($actionLabel && $actionHref)
        <a href="{{ $actionHref }}" class="inline-block mt-4 text-sm font-semibold text-brand-700 hover:text-brand-800">{{ $actionLabel }} →</a>
    @endif
</div>
