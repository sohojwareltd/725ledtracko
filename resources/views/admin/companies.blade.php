@extends('layouts.app')

@section('title', 'Companies & Modules')

@section('styles')
<style>
/* ── selector row ───────────────────────────────────── */
.cm-selector-row {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    flex-wrap: wrap;
}
.cm-selector-row select {
    flex: 1;
    min-width: 0;
    height: 42px;
    padding: 0 0.9rem;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    background: var(--surface);
    color: var(--text-main);
    font-size: 0.92rem;
    font-family: inherit;
    outline: none;
    cursor: pointer;
    transition: border-color 0.15s, box-shadow 0.15s;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 16 16'%3E%3Cpath fill='%2364748b' d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.9rem center;
    padding-right: 2.5rem;
}
.cm-selector-row select:focus {
    border-color: var(--brand);
    box-shadow: 0 0 0 3px rgba(34,179,193,0.12);
}
.cm-add-company-btn {
    height: 42px;
    white-space: nowrap;
    flex-shrink: 0;
}
/* ── add company inline form ────────────────────────── */
.cm-new-company-row {
    display: none;
    gap: 0.6rem;
    align-items: center;
    margin-top: 0.75rem;
}
.cm-new-company-row.is-open { display: flex; }
.cm-new-company-row input {
    flex: 1;
    height: 42px;
    padding: 0 0.9rem;
    border: 1px solid var(--brand);
    border-radius: var(--radius-sm);
    font-size: 0.9rem;
    font-family: inherit;
    outline: none;
    color: var(--text-main);
    background: var(--surface);
    box-shadow: 0 0 0 3px rgba(34,179,193,0.1);
}
/* ── module panel ───────────────────────────────────── */
.cm-panel {
    display: none;
    margin-top: 1.5rem;
    border-top: 1px solid var(--border);
    padding-top: 1.25rem;
    animation: fadeIn 0.18s ease both;
}
.cm-panel.is-active { display: block; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: none; } }
.cm-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
    gap: 0.75rem;
    flex-wrap: wrap;
}
.cm-panel-title {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    font-weight: 600;
    font-size: 0.95rem;
    color: var(--text-main);
}
.cm-badge {
    font-size: 0.72rem;
    font-weight: 600;
    background: var(--surface-muted);
    color: var(--text-subtle);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 0.15rem 0.55rem;
}
.cm-del-company {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    background: none;
    border: 1px solid var(--border);
    color: var(--text-subtle);
    border-radius: var(--radius-sm);
    padding: 0.3rem 0.75rem;
    font-size: 0.8rem;
    font-family: inherit;
    cursor: pointer;
    transition: color 0.15s, background 0.15s, border-color 0.15s;
}
.cm-del-company:hover { color: var(--danger); background: #fff0f0; border-color: #fcc; }
/* ── tags ───────────────────────────────────────────── */
.cm-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
    min-height: 1.5rem;
    margin-bottom: 1rem;
}
.cm-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    background: var(--surface-muted);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 0.28rem 0.55rem 0.28rem 0.8rem;
    font-size: 0.82rem;
    font-weight: 500;
    color: var(--text-main);
}
.cm-tag-del {
    background: none;
    border: none;
    color: var(--text-subtle);
    cursor: pointer;
    padding: 0 0.1rem;
    line-height: 1;
    font-size: 1rem;
    display: flex;
    align-items: center;
    transition: color 0.15s;
}
.cm-tag-del:hover { color: var(--danger); }
.cm-empty-msg {
    font-size: 0.82rem;
    color: var(--text-subtle);
    font-style: italic;
}
/* ── add module form ─────────────────────────────────── */
.cm-mod-add {
    display: flex;
    gap: 0.6rem;
    align-items: center;
}
.cm-mod-add input {
    flex: 1;
    height: 42px;
    padding: 0 0.9rem;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 0.88rem;
    font-family: inherit;
    outline: none;
    color: var(--text-main);
    background: var(--surface);
    transition: border-color 0.15s, box-shadow 0.15s;
}
.cm-mod-add input:focus {
    border-color: var(--brand);
    box-shadow: 0 0 0 3px rgba(34,179,193,0.12);
}
.btn-add-mod {
    height: 42px;
    padding: 0 1.1rem;
    font-size: 0.88rem;
    white-space: nowrap;
    flex-shrink: 0;
}
/* ── autocomplete ───────────────────────────────────── */
.cm-autocomplete-wrap {
    position: relative;
    flex: 1;
    min-width: 0;
}
.cm-autocomplete-wrap input {
    width: 100%;
    height: 42px;
    padding: 0 2.5rem 0 0.9rem;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    background: var(--surface);
    color: var(--text-main);
    font-size: 0.92rem;
    font-family: inherit;
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
    box-sizing: border-box;
}
.cm-autocomplete-wrap input:focus {
    border-color: var(--brand);
    box-shadow: 0 0 0 3px rgba(34,179,193,0.12);
}
.cm-autocomplete-wrap .cm-ac-icon {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-subtle);
    font-size: 0.9rem;
    pointer-events: none;
}
.cm-ac-clear {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--text-subtle);
    font-size: 1rem;
    cursor: pointer;
    padding: 0;
    display: none;
    line-height: 1;
}
.cm-ac-clear:hover { color: var(--danger); }
.cm-ac-dropdown {
    display: none;
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    box-shadow: var(--shadow-sm);
    z-index: 9000;
    max-height: 240px;
    overflow-y: auto;
}
.cm-ac-dropdown.is-open { display: block; }
.cm-ac-item {
    padding: 0.6rem 0.9rem;
    font-size: 0.88rem;
    cursor: pointer;
    color: var(--text-main);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: background 0.1s;
}
.cm-ac-item:hover,
.cm-ac-item.is-focused {
    background: var(--surface-muted);
}
.cm-ac-item mark {
    background: rgba(34,179,193,0.18);
    color: var(--brand-strong);
    border-radius: 2px;
    font-weight: 600;
    padding: 0 1px;
}
.cm-ac-empty {
    padding: 0.7rem 0.9rem;
    font-size: 0.85rem;
    color: var(--text-subtle);
    font-style: italic;
}
/* ── confirm modal ───────────────────────────────────── */
.cm-modal-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,0.45);
    z-index: 99999;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(2px);
}
.cm-modal-backdrop.is-open { display: flex; }
.cm-modal {
    background: var(--surface);
    border-radius: var(--radius-md);
    box-shadow: 0 20px 60px rgba(15,23,42,0.2);
    padding: 1.75rem 1.75rem 1.4rem;
    max-width: 380px;
    width: calc(100% - 2rem);
    animation: modalIn 0.18s cubic-bezier(.34,1.4,.64,1) both;
}
@keyframes modalIn {
    from { opacity:0; transform: scale(0.93) translateY(8px); }
    to   { opacity:1; transform: none; }
}
.cm-modal-icon {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #fff0f0;
    color: var(--danger);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    margin-bottom: 1rem;
}
.cm-modal-title {
    font-weight: 700;
    font-size: 1rem;
    color: var(--text-main);
    margin: 0 0 0.35rem;
}
.cm-modal-msg {
    font-size: 0.87rem;
    color: var(--text-subtle);
    margin: 0 0 1.4rem;
    line-height: 1.55;
}
.cm-modal-actions {
    display: flex;
    gap: 0.6rem;
    justify-content: flex-end;
}
.cm-modal-cancel {
    height: 38px;
    padding: 0 1rem;
    font-size: 0.88rem;
    font-family: inherit;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    background: var(--surface);
    color: var(--text-subtle);
    cursor: pointer;
    transition: background 0.15s;
}
.cm-modal-cancel:hover { background: var(--surface-muted); }
.cm-modal-confirm {
    height: 38px;
    padding: 0 1.1rem;
    font-size: 0.88rem;
    font-family: inherit;
    border: none;
    border-radius: var(--radius-sm);
    background: var(--danger);
    color: #fff;
    cursor: pointer;
    font-weight: 600;
    transition: opacity 0.15s;
}
.cm-modal-confirm:hover { opacity: 0.88; }
</style>
@endsection

@section('content')
<header class="page-header">
    <div>
        <p class="page-header__subtitle">Pick a company from the dropdown to view, add, or remove its linked module models.</p>
        <h1 class="page-header__title">Companies & Modules</h1>
    </div>
    <span class="pill"><i class="bi bi-building"></i> Admin Panel</span>
</header>

<section class="section-stack">
<div class="surface">

    {{-- Company data for JS autocomplete --}}
    <script>
    const CM_COMPANIES = @json(collect($companies)->map(fn($c) => ['id' => $c->idCompany, 'name' => $c->CompanyName]));
    </script>

    {{-- ── Top row: autocomplete input + new company button ── --}}
    <div class="cm-selector-row">
        <div class="cm-autocomplete-wrap" id="cmAcWrap">
            <input type="text" id="cmAcInput"
                   placeholder="Type a company name..."
                   autocomplete="off"
                   aria-label="Search company"
                   value="{{ session('open_company') ? (collect($companies)->firstWhere('idCompany', session('open_company'))->CompanyName ?? '') : '' }}">
            <button type="button" class="cm-ac-clear" id="cmAcClear" title="Clear"><i class="bi bi-x"></i></button>
            <i class="bi bi-search cm-ac-icon" id="cmAcSearchIcon"></i>
            <div class="cm-ac-dropdown" id="cmAcDropdown"></div>
        </div>

        {{-- NEW COMPANY BUTTON — temporarily disabled
        <button type="button" class="btn btn-primary cm-add-company-btn"
                id="toggleNewCompanyBtn" onclick="toggleNewCompanyForm(this)">
            <i class="bi bi-plus-lg"></i> New Company
        </button>
        --}}
    </div>

    {{-- ── New company inline form — temporarily disabled ─
    <div class="cm-new-company-row" id="newCompanyRow">
        <form action="{{ route('admin.companies.store') }}" method="POST"
              style="display:flex; gap:0.6rem; flex:1; align-items:center;">
            @csrf
            <input type="text" name="CompanyName"
                   placeholder="Enter company name e.g. Peak Technologies"
                   autocomplete="off" required>
            <button type="submit" class="btn btn-primary" style="height:42px; white-space:nowrap;">
                <i class="bi bi-check-lg"></i> Save
            </button>
            <button type="button" class="btn" style="height:42px;"
                    onclick="toggleNewCompanyForm(document.getElementById('toggleNewCompanyBtn'))">
                Cancel
            </button>
        </form>
    </div>
    ── --}}

    {{-- ── Per-company panels (hidden until selected) ───── --}}
    @foreach($companies as $company)
    @php $mods = $companyModules[$company->idCompany] ?? []; @endphp
    <div class="cm-panel {{ session('open_company') == $company->idCompany ? 'is-active' : '' }}"
         id="panel-{{ $company->idCompany }}">

        <div class="cm-panel-header">
            <span class="cm-panel-title">
                <i class="bi bi-building" style="color:var(--brand);"></i>
                {{ $company->CompanyName }}
                <span class="cm-badge">
                    {{ count($mods) }} {{ count($mods) === 1 ? 'module' : 'modules' }}
                </span>
            </span>
            <form action="{{ route('admin.companies.delete', $company->idCompany) }}" method="POST"
                  id="del-company-form-{{ $company->idCompany }}">
                @csrf
                <button type="button" class="cm-del-company"
                        onclick="openConfirm(
                            'del-company-form-{{ $company->idCompany }}',
                            'Delete Company',
                            'This will permanently delete <strong>{{ addslashes($company->CompanyName) }}</strong> and all its linked modules. This cannot be undone.'
                        )">
                    <i class="bi bi-trash"></i> Delete Company
                </button>
            </form>
        </div>

        {{-- Module tags --}}
        <div class="cm-tags">
            @forelse($mods as $mod)
            <span class="cm-tag">
                {{ $mod->ModuleName }}
                <form action="{{ route('admin.companies.deleteModule', [$company->idCompany, $mod->idModule]) }}"
                      method="POST" style="display:inline;"
                      id="del-mod-form-{{ $company->idCompany }}-{{ $mod->idModule }}">
                    @csrf
                    <button type="button" class="cm-tag-del" title="Remove"
                            onclick="openConfirm(
                                'del-mod-form-{{ $company->idCompany }}-{{ $mod->idModule }}',
                                'Remove Module',
                                'Remove <strong>{{ addslashes($mod->ModuleName) }}</strong> from this company?'
                            )">
                        <i class="bi bi-x"></i>
                    </button>
                </form>
            </span>
            @empty
            <span class="cm-empty-msg">No modules yet — add one below.</span>
            @endforelse
        </div>

        {{-- Add module --}}
        <form action="{{ route('admin.companies.addModule', $company->idCompany) }}" method="POST"
              class="cm-mod-add">
            @csrf
            <input type="text" name="ModuleName"
                   placeholder="New module name e.g. ALUVISION P2.5"
                   autocomplete="off" required>
            <button type="submit" class="btn btn-primary btn-add-mod">
                <i class="bi bi-plus-lg"></i> Add
            </button>
        </form>

    </div>
    @endforeach

    {{-- Placeholder when nothing selected --}}
    <div id="cm-placeholder"
         style="{{ session('open_company') ? 'display:none;' : '' }} margin-top:1.5rem; border-top:1px solid var(--border); padding-top:1.25rem; text-align:center; color:var(--text-subtle); font-size:0.88rem; padding-bottom:0.5rem;">
        <i class="bi bi-arrow-up" style="font-size:1rem;"></i>&nbsp;
        Select a company above to manage its modules
    </div>

</div>
</section>

{{-- ── Confirm Modal ──────────────────────────────────── --}}
<div class="cm-modal-backdrop" id="cmModalBackdrop">
    <div class="cm-modal" role="dialog" aria-modal="true">
        <div class="cm-modal-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
        <p class="cm-modal-title" id="cmModalTitle">Are you sure?</p>
        <p class="cm-modal-msg" id="cmModalMsg"></p>
        <div class="cm-modal-actions">
            <button class="cm-modal-cancel" id="cmModalCancelBtn">Cancel</button>
            <button class="cm-modal-confirm" id="cmModalConfirmBtn">Yes, Delete</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let _pendingFormId = null;

function openConfirm(formId, title, msg) {
    _pendingFormId = formId;
    document.getElementById('cmModalTitle').textContent = title;
    document.getElementById('cmModalMsg').innerHTML = msg;
    document.getElementById('cmModalBackdrop').classList.add('is-open');
}

function closeConfirm() {
    document.getElementById('cmModalBackdrop').classList.remove('is-open');
    _pendingFormId = null;
}

function switchCompany(id) {
    document.querySelectorAll('.cm-panel').forEach(p => p.classList.remove('is-active'));
    const placeholder = document.getElementById('cm-placeholder');
    if (!id) { placeholder.style.display = ''; return; }
    placeholder.style.display = 'none';
    const panel = document.getElementById('panel-' + id);
    if (panel) panel.classList.add('is-active');
}

// ── Autocomplete ────────────────────────────────────────
(function () {
    let focusIdx = -1;
    const input    = document.getElementById('cmAcInput');
    const dropdown = document.getElementById('cmAcDropdown');
    const clearBtn = document.getElementById('cmAcClear');
    const icon     = document.getElementById('cmAcSearchIcon');
    if (!input) return;

    function highlight(text, query) {
        if (!query) return text;
        const re = new RegExp('(' + query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
        return text.replace(re, '<mark>$1</mark>');
    }

    function renderDropdown(query) {
        const q = query.trim().toLowerCase();
        const matches = q
            ? CM_COMPANIES.filter(c => c.name.toLowerCase().includes(q))
            : CM_COMPANIES;
        focusIdx = -1;
        if (matches.length === 0) {
            dropdown.innerHTML = '<div class="cm-ac-empty">No companies found</div>';
        } else {
            dropdown.innerHTML = matches.map(c =>
                `<div class="cm-ac-item" data-id="${c.id}" data-name="${c.name}">
                    <i class="bi bi-building" style="color:var(--brand);font-size:0.8rem;"></i>
                    ${highlight(c.name, query.trim())}
                </div>`
            ).join('');
            dropdown.querySelectorAll('.cm-ac-item').forEach(item => {
                item.addEventListener('mousedown', function (e) {
                    e.preventDefault();
                    selectCompany(this.dataset.id, this.dataset.name);
                });
            });
        }
        dropdown.classList.add('is-open');
    }

    function selectCompany(id, name) {
        input.value = name;
        dropdown.classList.remove('is-open');
        clearBtn.style.display = 'block';
        icon.style.display = 'none';
        switchCompany(id);
    }

    function clearSelection() {
        input.value = '';
        clearBtn.style.display = 'none';
        icon.style.display = '';
        dropdown.classList.remove('is-open');
        switchCompany('');
        input.focus();
    }

    input.addEventListener('focus', function () {
        renderDropdown(this.value);
    });

    input.addEventListener('input', function () {
        clearBtn.style.display = this.value ? 'block' : 'none';
        icon.style.display = this.value ? 'none' : '';
        renderDropdown(this.value);
    });

    input.addEventListener('keydown', function (e) {
        const items = dropdown.querySelectorAll('.cm-ac-item');
        if (!items.length) return;
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            focusIdx = Math.min(focusIdx + 1, items.length - 1);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            focusIdx = Math.max(focusIdx - 1, 0);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (focusIdx >= 0 && items[focusIdx]) items[focusIdx].dispatchEvent(new Event('mousedown'));
            return;
        } else if (e.key === 'Escape') {
            dropdown.classList.remove('is-open');
            return;
        }
        items.forEach((it, i) => it.classList.toggle('is-focused', i === focusIdx));
        if (items[focusIdx]) items[focusIdx].scrollIntoView({ block: 'nearest' });
    });

    document.addEventListener('click', function (e) {
        if (!document.getElementById('cmAcWrap').contains(e.target)) {
            dropdown.classList.remove('is-open');
        }
    });

    clearBtn.addEventListener('click', clearSelection);

    // Pre-select if session had a company open
    if (input.value) {
        clearBtn.style.display = 'block';
        icon.style.display = 'none';
    }
})();

function toggleNewCompanyForm(btn) {
    const row = document.getElementById('newCompanyRow');
    const isOpen = row.classList.toggle('is-open');
    if (btn) {
        btn.innerHTML = isOpen
            ? '<i class="bi bi-x-lg"></i> Cancel'
            : '<i class="bi bi-plus-lg"></i> New Company';
    }
    if (isOpen) { const inp = row.querySelector('input'); if(inp) inp.focus(); }
}

document.addEventListener('DOMContentLoaded', function () {
    // Modal buttons
    document.getElementById('cmModalConfirmBtn').addEventListener('click', function () {
        if (_pendingFormId) document.getElementById(_pendingFormId).submit();
        closeConfirm();
    });
    document.getElementById('cmModalCancelBtn').addEventListener('click', closeConfirm);
    document.getElementById('cmModalBackdrop').addEventListener('click', function (e) {
        if (e.target === this) closeConfirm();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeConfirm();
    });

    // Pre-select company from session
    @if(session('open_company'))
    switchCompany({{ session('open_company') }});
    @endif
});
</script>
@endsection
