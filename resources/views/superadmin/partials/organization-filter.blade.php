<form method="GET" action="{{ $action }}" class="mb-6 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4.5 shadow-soft sm:flex-row sm:items-end">
    <label class="flex-1">
        <span class="mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wider">Filter perusahaan</span>
        <div class="relative">
            <select name="organization" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3.5 text-sm font-semibold text-slate-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all shadow-sm appearance-none">
                <option value="">Semua perusahaan</option>
                @foreach($organizationOptions as $organizationOption)
                    <option value="{{ $organizationOption->id }}" @selected($selectedOrganization?->id === $organizationOption->id)>{{ $organizationOption->name }}</option>
                @endforeach
            </select>
            <!-- Custom chevron arrow -->
            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
            </span>
        </div>
    </label>
    <button class="h-11 rounded-xl bg-slate-900 px-5.5 text-sm font-bold text-white hover:bg-slate-800 active:scale-[0.98] transition-all-300 shadow-sm shrink-0">Terapkan filter</button>
    @if($selectedOrganization)
        <a href="{{ $action }}" class="grid h-11 place-items-center rounded-xl border border-slate-200 bg-white hover:bg-slate-50 active:scale-[0.98] px-5.5 text-sm font-bold text-slate-600 transition-all-300 shadow-sm shrink-0">Atur ulang</a>
    @endif
</form>
