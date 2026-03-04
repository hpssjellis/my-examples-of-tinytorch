<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TinyTorch Notebook Editor</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Syne:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:       #0a0e1a;
            --surface:  #111827;
            --surface2: #0d1424;
            --border:   #1e2d45;
            --accent:   #00d4ff;
            --accent2:  #7c3aed;
            --green:    #10b981;
            --yellow:   #f59e0b;
            --red:      #ef4444;
            --text:     #e2e8f0;
            --muted:    #64748b;
            --font-mono: 'JetBrains Mono', monospace;
            --font-ui:   'Syne', sans-serif;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font-ui);
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* ── Top bar ── */
        .topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 20px;
            height: 52px;
            display: flex;
            align-items: center;
            gap: 16px;
            flex-shrink: 0;
        }

        .topbar-logo {
            font-weight: 800;
            font-size: 1rem;
            white-space: nowrap;
        }
        .topbar-logo span { color: var(--accent); }

        .topbar-divider {
            width: 1px; height: 24px;
            background: var(--border);
            flex-shrink: 0;
        }

        .topbar-select {
            background: var(--surface2);
            border: 1px solid var(--border);
            color: var(--text);
            font-family: var(--font-mono);
            font-size: 0.78rem;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            flex: 1;
            max-width: 260px;
        }
        .topbar-select:focus { outline: none; border-color: var(--accent); }

        .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .conn-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            font-family: var(--font-mono);
            font-size: 0.72rem;
            color: var(--muted);
            padding: 4px 10px;
            border: 1px solid var(--border);
            border-radius: 999px;
        }
        .conn-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--yellow);
            animation: pulse 2s infinite;
        }
        .conn-dot.online  { background: var(--green); }
        .conn-dot.offline { background: var(--red); animation: none; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.35} }

        /* ── Toolbar ── */
        .toolbar {
            background: #0d1525;
            border-bottom: 1px solid var(--border);
            padding: 8px 20px;
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            align-items: center;
            flex-shrink: 0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: 5px;
            font-family: var(--font-mono);
            font-size: 0.74rem;
            font-weight: 700;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.15s;
            white-space: nowrap;
        }
        .btn:disabled { opacity: 0.4; cursor: not-allowed; }

        .btn-accent  { background: var(--accent); color: #000; }
        .btn-accent:hover:not(:disabled) { background: #33dcff; }

        .btn-ghost   { background: transparent; color: var(--accent); border: 1px solid var(--border); }
        .btn-ghost:hover:not(:disabled) { border-color: var(--accent); background: rgba(0,212,255,0.06); }

        .btn-green   { background: var(--green); color: #000; }
        .btn-green:hover:not(:disabled) { background: #0da271; }

        .btn-purple  { background: var(--accent2); color: #fff; }
        .btn-purple:hover:not(:disabled) { background: #6d28d9; }

        .btn-red     { background: transparent; color: var(--red); border: 1px solid var(--border); }
        .btn-red:hover:not(:disabled) { border-color: var(--red); background: rgba(239,68,68,0.06); }

        .toolbar-sep {
            width: 1px; height: 20px;
            background: var(--border);
            margin: 0 4px;
        }

        .toolbar-status {
            margin-left: auto;
            font-family: var(--font-mono);
            font-size: 0.72rem;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .spinner {
            width: 12px; height: 12px;
            border: 2px solid var(--border);
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            display: none;
        }
        .spinner.show { display: block; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Main area ── */
        .main {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        /* ── Cells panel ── */
        .cells-panel {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
        }
        .cells-panel::-webkit-scrollbar { width: 4px; }
        .cells-panel::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

        /* ── Output panel ── */
        .output-panel {
            width: 360px;
            border-left: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }

        .output-header {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            font-family: var(--font-mono);
            font-size: 0.75rem;
            color: var(--muted);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .output-body {
            flex: 1;
            overflow-y: auto;
            padding: 14px 16px;
            font-family: var(--font-mono);
            font-size: 0.76rem;
            line-height: 1.6;
            color: var(--green);
            background: #050810;
            white-space: pre-wrap;
        }
        .output-body::-webkit-scrollbar { width: 3px; }
        .output-body::-webkit-scrollbar-thumb { background: var(--border); }

        .output-placeholder {
            color: var(--muted);
            font-style: italic;
        }

        /* ── Empty state ── */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            gap: 12px;
            color: var(--muted);
            text-align: center;
        }
        .empty-state .big { font-size: 2.5rem; }
        .empty-state p { font-size: 0.85rem; font-family: var(--font-mono); max-width: 280px; }

        /* ── Cell ── */
        .cell {
            margin-bottom: 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            transition: border-color 0.15s;
        }
        .cell:hover { border-color: rgba(0,212,255,0.3); }
        .cell.active { border-color: var(--accent); }

        .cell-head {
            background: #0d1424;
            padding: 7px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border);
        }

        .cell-type-badge {
            font-family: var(--font-mono);
            font-size: 0.68rem;
            color: var(--muted);
            letter-spacing: 0.05em;
        }
        .cell-type-badge.code { color: var(--accent); }
        .cell-type-badge.markdown { color: var(--yellow); }

        .cell-actions { display: flex; gap: 4px; }
        .cell-actions .btn { padding: 3px 8px; font-size: 0.68rem; }

        textarea.cell-source {
            display: block;
            width: 100%;
            background: #0b1120;
            color: #c9d1d9;
            font-family: var(--font-mono);
            font-size: 0.82rem;
            line-height: 1.6;
            border: none;
            padding: 14px 16px;
            resize: vertical;
            min-height: 80px;
            outline: none;
            tab-size: 4;
        }
        textarea.cell-source:focus { background: #0d1525; }

        .cell-output {
            background: #050810;
            border-top: 1px solid var(--border);
            padding: 10px 14px;
            font-family: var(--font-mono);
            font-size: 0.76rem;
            color: var(--green);
            white-space: pre-wrap;
            display: none;
            max-height: 200px;
            overflow-y: auto;
        }
        .cell-output.show { display: block; }
        .cell-output.error { color: var(--red); }

        /* ── Status bar ── */
        .statusbar {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 5px 20px;
            font-family: var(--font-mono);
            font-size: 0.7rem;
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        /* ── Modal ── */
        .modal-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.7);
            z-index: 100;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.show { display: flex; }

        .modal {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 28px;
            max-width: 480px;
            width: 90%;
        }
        .modal h3 { margin-bottom: 12px; }
        .modal pre {
            background: #050810;
            color: var(--green);
            font-family: var(--font-mono);
            font-size: 0.76rem;
            padding: 14px;
            border-radius: 6px;
            max-height: 300px;
            overflow-y: auto;
            white-space: pre-wrap;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>

    <!-- ── Top bar ── -->
    <div class="topbar">
        <a href="index.php" style="text-decoration:none;color:inherit;">
            <span class="topbar-logo">Tiny<span>Torch</span> Hub</span>
        </a>
        <div class="topbar-divider"></div>
        <select class="topbar-select" id="module-select">
            <option value="">— select a module —</option>
            <option value="01_tensor">01 · Tensor Basics</option>
            <option value="02_autograd">02 · Autograd</option>
            <option value="03_nn">03 · Neural Networks</option>
            <option value="04_optim">04 · Optimizers</option>
            <option value="05_data">05 · Data Loading</option>
            <option value="06_training">06 · Training Loop</option>
        </select>
        <button class="btn btn-accent" onclick="loadModule()">Load Module</button>
        <div class="topbar-right">
            <div class="conn-badge">
                <div class="conn-dot" id="conn-dot"></div>
                <span id="conn-label">connecting…</span>
            </div>
        </div>
    </div>

    <!-- ── Toolbar ── -->
    <div class="toolbar">
        <button class="btn btn-green"  id="btn-save"     onclick="saveNotebook()"  disabled>💾 Save</button>
        <button class="btn btn-accent" id="btn-run-all"  onclick="executeAll()"    disabled>▶ Run All</button>
        <button class="btn btn-ghost"  id="btn-add-code" onclick="addCell('code')" disabled>+ Code</button>
        <button class="btn btn-ghost"  id="btn-add-md"   onclick="addCell('markdown')" disabled>+ Markdown</button>
        <div class="toolbar-sep"></div>
        <button class="btn btn-purple" id="btn-grade"    onclick="runGrade()"      disabled>✅ Grade</button>
        <button class="btn btn-red"    id="btn-clear"    onclick="clearOutput()"   disabled>✕ Clear Output</button>
        <div class="toolbar-status">
            <div class="spinner" id="spinner"></div>
            <span id="status-msg">Load a module to begin</span>
        </div>
    </div>

    <!-- ── Main ── -->
    <div class="main">

        <!-- Cells -->
        <div class="cells-panel" id="cells-panel">
            <div class="empty-state" id="empty-state">
                <div class="big">📓</div>
                <p>Select a module above and click Load Module</p>
            </div>
            <div id="cells-container"></div>
        </div>

        <!-- Output sidebar -->
        <div class="output-panel">
            <div class="output-header">
                <span>execution output</span>
                <button class="btn btn-ghost" style="padding:2px 8px;font-size:0.68rem;" onclick="clearOutput()">clear</button>
            </div>
            <div class="output-body" id="global-output">
                <span class="output-placeholder">Output will appear here after running cells…</span>
            </div>
        </div>

    </div>

    <!-- ── Status bar ── -->
    <div class="statusbar">
        <span id="cell-count">0 cells</span>
        <span>tito API · <a href="https://my-examples-of-tito.onrender.com/" target="_blank" style="color:var(--accent);text-decoration:none;">my-examples-of-tito.onrender.com</a></span>
    </div>

    <!-- ── Grade modal ── -->
    <div class="modal-overlay" id="grade-modal">
        <div class="modal">
            <h3>✅ Grade Results</h3>
            <pre id="grade-output">…</pre>
            <button class="btn btn-ghost" onclick="document.getElementById('grade-modal').classList.remove('show')">Close</button>
        </div>
    </div>

<script>
    const API = 'https://my-examples-of-tito.onrender.com';

    let currentModule = null;
    let cells = [];

    // Pre-fill module from URL param
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('module')) {
        const sel = document.getElementById('module-select');
        const val = urlParams.get('module').replace(/\.(py|ipynb)$/, '');
        // Try to select existing option or add one
        let found = false;
        for (let opt of sel.options) { if (opt.value === val) { opt.selected = true; found = true; break; } }
        if (!found) {
            const o = new Option(val, val, true, true);
            sel.appendChild(o);
        }
    }

    // ── Utilities ──
    function setStatus(msg, loading = false) {
        document.getElementById('status-msg').textContent = msg;
        document.getElementById('spinner').classList.toggle('show', loading);
    }

    function setButtonsEnabled(enabled) {
        ['btn-save','btn-run-all','btn-add-code','btn-add-md','btn-grade','btn-clear']
            .forEach(id => document.getElementById(id).disabled = !enabled);
    }

    function appendOutput(text, isError = false) {
        const el = document.getElementById('global-output');
        el.querySelector('.output-placeholder')?.remove();
        const span = document.createElement('span');
        span.style.color = isError ? 'var(--red)' : 'var(--green)';
        span.textContent = text + '\n';
        el.appendChild(span);
        el.scrollTop = el.scrollHeight;
    }

    function clearOutput() {
        document.getElementById('global-output').innerHTML =
            '<span class="output-placeholder">Output cleared.</span>';
        document.querySelectorAll('.cell-output').forEach(el => {
            el.classList.remove('show', 'error');
            el.textContent = '';
        });
    }

    // ── API helper ──
    async function api(path, opts = {}) {
        const res = await fetch(API + path, {
            ...opts,
            headers: { 'Content-Type': 'application/json', ...(opts.headers || {}) },
            signal: opts.signal || AbortSignal.timeout(30000)
        });
        return res.json();
    }

    // ── Load module ──
    async function loadModule() {
        const sel = document.getElementById('module-select');
        const mod = sel.value;
        if (!mod) { alert('Please select a module first.'); return; }

        setStatus('Converting .py → .ipynb…', true);
        setButtonsEnabled(false);

        try {
            // Step 1: convert
            const conv = await api('/api/v1/notebook/convert', {
                method: 'POST',
                body: JSON.stringify({ source_file: `${mod}.py`, target_file: `${mod}.ipynb` })
            });
            if (conv.status === 'error') throw new Error('Convert: ' + conv.message);

            // Step 2: read
            setStatus('Reading notebook…', true);
            const nb = await api(`/api/v1/notebook/read/${mod}.ipynb`);
            if (nb.status === 'error') throw new Error('Read: ' + nb.message);

            currentModule = mod;
            cells = nb.cells || [];

            renderCells();
            setButtonsEnabled(true);
            setStatus(`✓ Loaded ${mod} · ${cells.length} cells`);
            appendOutput(`[load] ${mod}.ipynb · ${cells.length} cells`);

        } catch (e) {
            setStatus('✗ ' + e.message);
            appendOutput('[error] ' + e.message, true);
        }
    }

    // ── Render cells ──
    function renderCells() {
        const empty = document.getElementById('empty-state');
        const container = document.getElementById('cells-container');
        empty.style.display = cells.length ? 'none' : 'flex';
        container.innerHTML = '';

        cells.forEach((cell, i) => {
            const isCode = cell.type === 'code';
            const div = document.createElement('div');
            div.className = 'cell';
            div.dataset.index = i;
            div.innerHTML = `
                <div class="cell-head">
                    <span class="cell-type-badge ${cell.type}">${isCode ? '🐍 code' : '📝 markdown'} · In [${i+1}]</span>
                    <div class="cell-actions">
                        ${isCode ? `<button class="btn btn-accent" onclick="runCell(${i})">▶ Run</button>` : ''}
                        <button class="btn btn-red" onclick="deleteCell(${i})">✕</button>
                    </div>
                </div>
                <textarea class="cell-source" id="cell-src-${i}" rows="${Math.max(3, (cell.source || '').split('\n').length + 1)}"
                    oninput="cells[${i}].source = this.value; autoResize(this)">${escHtml(cell.source || '')}</textarea>
                <div class="cell-output" id="cell-out-${i}"></div>`;
            container.appendChild(div);
        });

        document.getElementById('cell-count').textContent = cells.length + ' cells';
    }

    function escHtml(s) {
        return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function autoResize(ta) {
        ta.style.height = 'auto';
        ta.style.height = ta.scrollHeight + 'px';
    }

    // ── Add / delete cell ──
    function addCell(type) {
        syncCells();
        cells.push({ type, source: '' });
        renderCells();
        setStatus(`Added ${type} cell`);
        // Focus new textarea
        const last = document.getElementById(`cell-src-${cells.length - 1}`);
        if (last) last.focus();
    }

    function deleteCell(i) {
        syncCells();
        cells.splice(i, 1);
        renderCells();
        setStatus('Cell deleted');
    }

    function syncCells() {
        cells.forEach((cell, i) => {
            const ta = document.getElementById(`cell-src-${i}`);
            if (ta) cell.source = ta.value;
        });
    }

    // ── Run single cell ──
    async function runCell(i) {
        syncCells();
        const out = document.getElementById(`cell-out-${i}`);
        const cell = cells[i];
        out.textContent = '⏳ running…';
        out.classList.add('show');
        out.classList.remove('error');

        try {
            // Save notebook first so the API can run the right version
            await saveNotebook(true);

            const res = await api('/api/v1/notebook/execute', {
                method: 'POST',
                body: JSON.stringify({ filename: `${currentModule}.ipynb`, cell_index: i })
            });

            const text = res.output || res.result || JSON.stringify(res, null, 2);
            out.textContent = text;
            if (res.status === 'error') out.classList.add('error');
            appendOutput(`[cell ${i+1}] ` + text, res.status === 'error');

        } catch (e) {
            out.textContent = 'Error: ' + e.message;
            out.classList.add('error');
            appendOutput('[error cell ' + (i+1) + '] ' + e.message, true);
        }
    }

    // ── Execute all ──
    async function executeAll() {
        if (!currentModule) return;
        syncCells();
        setStatus('Saving…', true);
        await saveNotebook(true);
        setStatus('Executing all cells…', true);

        try {
            const res = await api('/api/v1/notebook/execute', {
                method: 'POST',
                body: JSON.stringify({ filename: `${currentModule}.ipynb` })
            });
            const text = res.output || JSON.stringify(res, null, 2);
            appendOutput('[run all]\n' + text, res.status === 'error');
            setStatus(res.status === 'error' ? '✗ Execution error' : '✓ All cells executed');
        } catch (e) {
            setStatus('✗ ' + e.message);
            appendOutput('[error] ' + e.message, true);
        }
    }

    // ── Save ──
    async function saveNotebook(silent = false) {
        if (!currentModule) return;
        syncCells();
        if (!silent) setStatus('Saving…', true);

        try {
            const res = await api('/api/v1/notebook/update', {
                method: 'POST',
                body: JSON.stringify({ filename: `${currentModule}.ipynb`, cells })
            });
            if (!silent) setStatus(res.status === 'error' ? '✗ Save failed: ' + res.message : '✓ Saved');
        } catch (e) {
            if (!silent) setStatus('✗ Save error: ' + e.message);
        }
    }

    // ── Grade ──
    async function runGrade() {
        if (!currentModule) return;
        syncCells();
        setStatus('Saving for grading…', true);
        await saveNotebook(true);

        setStatus('Grading…', true);
        try {
            const res = await api('/api/v1/tito/command', {
                method: 'POST',
                body: JSON.stringify({ args: ['grade', 'autograde', currentModule] })
            });
            document.getElementById('grade-output').textContent =
                res.output || res.message || JSON.stringify(res, null, 2);
            document.getElementById('grade-modal').classList.add('show');
            setStatus('✓ Grading complete');
        } catch (e) {
            setStatus('✗ Grade error: ' + e.message);
            appendOutput('[grade error] ' + e.message, true);
        }
    }

    // ── Connection check ──
    async function checkConnection() {
        try {
            const h = await api('/api/v1/health', { signal: AbortSignal.timeout(8000) });
            const dot = document.getElementById('conn-dot');
            const lbl = document.getElementById('conn-label');
            if (h.status === 'healthy' || h.status === 'ok' || h.tito) {
                dot.classList.add('online');
                lbl.textContent = 'API online';
            } else {
                dot.classList.add('offline');
                lbl.textContent = 'API error';
            }
        } catch {
            document.getElementById('conn-dot').classList.add('offline');
            document.getElementById('conn-label').textContent = 'API offline';
        }
    }

    // ── Init ──
    checkConnection();

    // Auto-load if ?module= param was set
    if (urlParams.get('module')) {
        loadModule();
    }

    // Tab key in textareas
    document.addEventListener('keydown', e => {
        if (e.key === 'Tab' && e.target.tagName === 'TEXTAREA') {
            e.preventDefault();
            const ta = e.target;
            const s = ta.selectionStart;
            ta.value = ta.value.slice(0, s) + '    ' + ta.value.slice(ta.selectionEnd);
            ta.selectionStart = ta.selectionEnd = s + 4;
        }
    });
</script>
</body>
</html>
