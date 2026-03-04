<?php
// Optional: load tinytorch bridge if it exists
if (file_exists('tinytorch/tinytorch.php')) {
    require_once 'tinytorch/tinytorch.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TinyTorch Hub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Syne:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:       #0a0e1a;
            --surface:  #111827;
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
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Animated grid background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(0,212,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,212,255,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
            z-index: 0;
        }

        .page-wrap {
            position: relative;
            z-index: 1;
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 24px 80px;
        }

        /* ── Header ── */
        header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 56px;
            padding-bottom: 32px;
            border-bottom: 1px solid var(--border);
        }

        .logo-block h1 {
            font-size: 2.6rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            line-height: 1;
        }

        .logo-block h1 span.accent { color: var(--accent); }

        .logo-block p {
            margin-top: 8px;
            color: var(--muted);
            font-family: var(--font-mono);
            font-size: 0.8rem;
            letter-spacing: 0.05em;
        }

        .status-cluster {
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: flex-end;
        }

        .status-dot {
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: var(--font-mono);
            font-size: 0.75rem;
            color: var(--muted);
        }

        .dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--muted);
            animation: pulse 2s infinite;
        }
        .dot.checking { background: var(--yellow); }
        .dot.online   { background: var(--green); }
        .dot.offline  { background: var(--red); animation: none; }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.4; }
        }

        /* ── Section titles ── */
        .section-label {
            font-family: var(--font-mono);
            font-size: 0.7rem;
            letter-spacing: 0.15em;
            color: var(--accent);
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        /* ── Grid layouts ── */
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
        }

        /* ── Cards ── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            transition: border-color 0.2s, transform 0.2s;
        }

        .card:hover {
            border-color: var(--accent);
            transform: translateY(-2px);
        }

        .card h3 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card p {
            font-size: 0.85rem;
            color: var(--muted);
            line-height: 1.6;
            margin-bottom: 16px;
        }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 6px;
            font-family: var(--font-mono);
            font-size: 0.78rem;
            font-weight: 700;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            letter-spacing: 0.02em;
        }

        .btn-primary {
            background: var(--accent);
            color: #000;
        }
        .btn-primary:hover { background: #33dcff; }

        .btn-ghost {
            background: transparent;
            color: var(--accent);
            border: 1px solid var(--accent);
        }
        .btn-ghost:hover { background: rgba(0,212,255,0.08); }

        .btn-purple {
            background: var(--accent2);
            color: #fff;
        }
        .btn-purple:hover { background: #6d28d9; }

        .btn-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        /* ── Section spacing ── */
        .section { margin-bottom: 52px; }

        /* ── Example list ── */
        #example-list-wrap {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
        }

        #example-list-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
        }

        #example-list-header span {
            font-family: var(--font-mono);
            font-size: 0.78rem;
            color: var(--muted);
        }

        #example-list {
            padding: 8px 0;
            max-height: 340px;
            overflow-y: auto;
        }

        #example-list::-webkit-scrollbar { width: 4px; }
        #example-list::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

        .example-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            border-bottom: 1px solid rgba(30,45,69,0.5);
            transition: background 0.15s;
        }

        .example-item:last-child { border-bottom: none; }
        .example-item:hover { background: rgba(0,212,255,0.04); }

        .example-item .name {
            font-family: var(--font-mono);
            font-size: 0.82rem;
            color: var(--text);
        }

        .example-item .tag {
            font-family: var(--font-mono);
            font-size: 0.68rem;
            padding: 2px 8px;
            border-radius: 3px;
            background: rgba(0,212,255,0.1);
            color: var(--accent);
        }

        .list-placeholder {
            padding: 32px 20px;
            text-align: center;
            font-family: var(--font-mono);
            font-size: 0.8rem;
            color: var(--muted);
        }

        .spinner {
            display: inline-block;
            width: 14px; height: 14px;
            border: 2px solid var(--border);
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            vertical-align: middle;
            margin-right: 8px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Quick links row ── */
        .link-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 52px;
        }

        .link-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 999px;
            text-decoration: none;
            color: var(--text);
            font-size: 0.82rem;
            font-family: var(--font-mono);
            transition: all 0.2s;
        }
        .link-pill:hover {
            border-color: var(--accent);
            color: var(--accent);
            transform: translateY(-1px);
        }
        .link-pill .icon { font-size: 1rem; }

        /* ── Editor CTA ── */
        .editor-cta {
            background: linear-gradient(135deg, rgba(124,58,237,0.15), rgba(0,212,255,0.08));
            border: 1px solid var(--accent2);
            border-radius: 12px;
            padding: 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .editor-cta h3 {
            font-size: 1.2rem;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .editor-cta p {
            font-size: 0.85rem;
            color: var(--muted);
            max-width: 480px;
        }

        /* ── API output preview ── */
        .api-raw {
            font-family: var(--font-mono);
            font-size: 0.72rem;
            color: var(--green);
            background: #050810;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 14px 16px;
            margin-top: 16px;
            max-height: 120px;
            overflow-y: auto;
            white-space: pre-wrap;
            display: none;
        }
        .api-raw.show { display: block; }

        footer {
            text-align: center;
            padding-top: 40px;
            border-top: 1px solid var(--border);
            font-family: var(--font-mono);
            font-size: 0.72rem;
            color: var(--muted);
        }
    </style>
</head>
<body>
<div class="page-wrap">

    <!-- ── Header ── -->
    <header>
        <div class="logo-block">
            <h1>Tiny<span class="accent">Torch</span> Hub</h1>
            <p>// PHP portal · tito API · Jupyter integration</p>
        </div>
        <div class="status-cluster">
            <div class="status-dot">
                <div class="dot checking" id="dot-api"></div>
                <span id="label-api">checking tito API…</span>
            </div>
            <div class="status-dot">
                <div class="dot checking" id="dot-jupyter"></div>
                <span id="label-jupyter">checking Jupyter…</span>
            </div>
        </div>
    </header>

    <!-- ── Quick links ── -->
    <div class="link-row">
        <a class="link-pill" href="https://github.com/hpssjellis/my-examples-of-tito" target="_blank">
            <span class="icon">⚙️</span> tito API · GitHub
        </a>
        <a class="link-pill" href="https://github.com/hpssjellis/my-example-jupyter-tinyTorch" target="_blank">
            <span class="icon">📓</span> Jupyter · GitHub
        </a>
        <a class="link-pill" href="https://github.com/mlsysbook/TinyTorch" target="_blank">
            <span class="icon">🔦</span> TinyTorch · GitHub
        </a>
        <a class="link-pill" href="https://mlsysbook.ai/tinytorch/" target="_blank">
            <span class="icon">📖</span> mlsysbook.ai
        </a>
        <a class="link-pill" href="https://tinytorch.ai/quickstart-guide.html" target="_blank">
            <span class="icon">🚀</span> Quickstart Guide
        </a>
    </div>

    <!-- ── Live Services ── -->
    <div class="section">
        <div class="section-label">// live services</div>
        <div class="grid-2">

            <div class="card">
                <h3>⚙️ tito API Service</h3>
                <p>Flask backend exposing TinyTorch CLI commands via REST. Supports notebook convert, execute, grade, and tito system health checks.</p>
                <div class="btn-row">
                    <a class="btn btn-primary" href="https://my-examples-of-tito.onrender.com/" target="_blank">Open API Tester ↗</a>
                    <button class="btn btn-ghost" onclick="testAPI()">Health Check</button>
                </div>
                <div class="api-raw" id="api-output"></div>
            </div>

            <div class="card">
                <h3>📓 Jupyter Lab</h3>
                <p>Full JupyterLab environment with TinyTorch pre-installed. Browse and run <code style="font-family:var(--font-mono);font-size:0.85em;color:var(--accent)">.ipynb</code> notebooks directly in the browser.</p>
                <div class="btn-row">
                    <a class="btn btn-purple" href="https://my-example-jupyter-tinytorch.onrender.com/" target="_blank">Open JupyterLab ↗</a>
                </div>
            </div>

        </div>
    </div>

    <!-- ── Module List ── -->
    <div class="section">
        <div class="section-label">// available modules</div>
        <div id="example-list-wrap">
            <div id="example-list-header">
                <strong style="font-size:0.9rem;">TinyTorch Modules</strong>
                <span id="module-count">loading…</span>
            </div>
            <div id="example-list">
                <div class="list-placeholder">
                    <span class="spinner"></span>fetching modules from tito API…
                </div>
            </div>
        </div>
    </div>

    <!-- ── Editor CTA ── -->
    <div class="section">
        <div class="section-label">// notebook editor</div>
        <div class="editor-cta">
            <div>
                <h3>📝 Notebook Editor</h3>
                <p>Load a TinyTorch module, edit cells, execute with the tito API backend, and grade your work — all from the browser.</p>
            </div>
            <a class="btn btn-purple" href="editor.php" style="font-size:0.9rem; padding:12px 28px;">
                Open Editor →
            </a>
        </div>
    </div>

    <footer>
        TinyTorch Hub · PHP <?php echo PHP_VERSION; ?> · 
        Built on <a href="https://mlsysbook.ai/tinytorch/" style="color:var(--accent);text-decoration:none;">mlsysbook.ai/tinytorch</a>
    </footer>

</div>

<script>
    const TITO_API  = 'https://my-examples-of-tito.onrender.com';
    const JUPYTER   = 'https://my-example-jupyter-tinytorch.onrender.com';

    // ── Status checks ──
    async function checkStatus(url, dotId, labelId, label) {
        try {
            const r = await fetch(url + '/api/v1/health', { signal: AbortSignal.timeout(8000) });
            const data = await r.json();
            document.getElementById(dotId).className    = 'dot online';
            document.getElementById(labelId).textContent = label + ' · online';
        } catch {
            document.getElementById(dotId).className    = 'dot offline';
            document.getElementById(labelId).textContent = label + ' · offline / sleeping';
        }
    }

    // Jupyter doesn't have /api/v1/health, just ping root
    async function checkJupyter() {
        try {
            await fetch(JUPYTER, { mode: 'no-cors', signal: AbortSignal.timeout(8000) });
            document.getElementById('dot-jupyter').className     = 'dot online';
            document.getElementById('label-jupyter').textContent = 'Jupyter · reachable';
        } catch {
            document.getElementById('dot-jupyter').className     = 'dot offline';
            document.getElementById('label-jupyter').textContent = 'Jupyter · offline / sleeping';
        }
    }

    // ── Explicit health check button ──
    async function testAPI() {
        const out = document.getElementById('api-output');
        out.classList.add('show');
        out.textContent = 'calling /api/v1/health …';
        try {
            const r    = await fetch(TITO_API + '/api/v1/health');
            const data = await r.json();
            out.textContent = JSON.stringify(data, null, 2);
        } catch (e) {
            out.textContent = 'ERROR: ' + e.message;
            out.style.color = 'var(--red)';
        }
    }

    // ── Load module list from tito API ──
    async function loadModules() {
        const list  = document.getElementById('example-list');
        const count = document.getElementById('module-count');

        try {
            const r = await fetch(TITO_API + '/api/v1/module?operation=list', {
                signal: AbortSignal.timeout(10000)
            });
            const data = await r.json();

            // Support various response shapes the API might return
            let modules = [];
            if (Array.isArray(data))              modules = data;
            else if (Array.isArray(data.modules)) modules = data.modules;
            else if (Array.isArray(data.data))    modules = data.data;
            else if (data.output) {
                // Parse text output like "01_tensor\n02_autograd\n..."
                modules = data.output.split('\n').map(s => s.trim()).filter(Boolean);
            }

            if (modules.length === 0) throw new Error('No modules returned');

            count.textContent = modules.length + ' modules';
            list.innerHTML = modules.map(m => {
                const name    = typeof m === 'string' ? m : (m.name || JSON.stringify(m));
                const isNb    = name.endsWith('.ipynb');
                const isPy    = name.endsWith('.py');
                const tagText = isNb ? '.ipynb' : isPy ? '.py' : 'module';
                return `
                    <div class="example-item">
                        <span class="name">${name}</span>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <span class="tag">${tagText}</span>
                            <a class="btn btn-ghost" style="padding:3px 10px;font-size:0.7rem;"
                               href="editor.php?module=${encodeURIComponent(name)}">Edit →</a>
                        </div>
                    </div>`;
            }).join('');

        } catch (e) {
            count.textContent = 'unavailable';
            list.innerHTML = `
                <div class="list-placeholder" style="color:var(--yellow);">
                    ⚠ Could not load module list — API may be sleeping.<br>
                    <span style="font-size:0.7rem;color:var(--muted);margin-top:6px;display:block;">${e.message}</span>
                    <button class="btn btn-ghost" style="margin-top:12px;" onclick="loadModules()">Retry</button>
                </div>`;
        }
    }

    // ── Init ──
    checkStatus(TITO_API, 'dot-api', 'label-api', 'tito API');
    checkJupyter();
    loadModules();
</script>
</body>
</html>
