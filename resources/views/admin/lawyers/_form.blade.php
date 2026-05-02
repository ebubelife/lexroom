@php $l = $lawyer ?? null; @endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="text-xs mb-1 block" style="color: var(--text-secondary);">Full Name *</label>
        <input type="text" name="name" value="{{ old('name', $l?->name) }}" required
               class="w-full px-3 py-2 rounded-lg text-sm outline-none"
               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
    </div>
    <div>
        <label class="text-xs mb-1 block" style="color: var(--text-secondary);">Email *</label>
        <input type="email" name="email" value="{{ old('email', $l?->email) }}" required
               class="w-full px-3 py-2 rounded-lg text-sm outline-none"
               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
    </div>
    <div>
        <label class="text-xs mb-1 block" style="color: var(--text-secondary);">Phone *</label>
        <input type="text" name="phone" value="{{ old('phone', $l?->phone) }}" required
               class="w-full px-3 py-2 rounded-lg text-sm outline-none"
               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
    </div>
    <div>
        <label class="text-xs mb-1 block" style="color: var(--text-secondary);">Bar Number</label>
        <input type="text" name="bar_number" value="{{ old('bar_number', $l?->bar_number) }}"
               class="w-full px-3 py-2 rounded-lg text-sm outline-none"
               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
    </div>
    <div>
        <label class="text-xs mb-1 block" style="color: var(--text-secondary);">Jurisdiction *</label>
        <select name="jurisdiction" required class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
            @foreach($jurisdictions as $j)
                <option value="{{ $j }}" {{ old('jurisdiction', $l?->jurisdiction) === $j ? 'selected' : '' }}>{{ $j }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-xs mb-1 block" style="color: var(--text-secondary);">Speciality *</label>
        <select name="speciality" required class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
            @foreach($specialities as $s)
                <option value="{{ $s }}" {{ old('speciality', $l?->speciality) === $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-xs mb-1 block" style="color: var(--text-secondary);">Years Experience *</label>
        <input type="number" name="years_experience" value="{{ old('years_experience', $l?->years_experience ?? 0) }}" min="0" required
               class="w-full px-3 py-2 rounded-lg text-sm outline-none"
               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
    </div>
    <div>
        <label class="text-xs mb-1 block" style="color: var(--text-secondary);">Commission Rate (%) *</label>
        <input type="number" name="commission_rate" value="{{ old('commission_rate', $l?->commission_rate ?? 20) }}" min="0" max="100" step="0.5" required
               class="w-full px-3 py-2 rounded-lg text-sm outline-none"
               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
    </div>
</div>

<div>
    <label class="text-xs mb-1 block" style="color: var(--text-secondary);">Bio</label>
    <textarea name="bio" rows="3" maxlength="500"
              class="w-full px-3 py-2 rounded-lg text-sm outline-none resize-none"
              style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">{{ old('bio', $l?->bio) }}</textarea>
</div>

<div class="flex gap-6">
    <label class="flex items-center gap-2 cursor-pointer">
        <input type="checkbox" name="verified" value="1" {{ old('verified', $l?->verified) ? 'checked' : '' }}
               class="rounded">
        <span class="text-sm" style="color: var(--text-primary);">Verified</span>
    </label>
    <label class="flex items-center gap-2 cursor-pointer">
        <input type="checkbox" name="active" value="1" {{ old('active', $l?->active ?? true) ? 'checked' : '' }}
               class="rounded">
        <span class="text-sm" style="color: var(--text-primary);">Active (visible to users)</span>
    </label>
</div>

@if($errors->any())
    <div class="p-3 rounded-lg text-sm" style="background: rgba(220,38,38,0.1); color: #DC2626;">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
