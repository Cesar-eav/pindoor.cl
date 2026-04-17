<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sopa de Letras - El Pionero de Valparaíso</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .word-search-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 2px;
            background-color: #1e3a8a;
            padding: 4px;
            max-width: 100%;
            width: 88%;
            margin: 0 auto;
            border-radius: 4px;
            user-select: none;
            touch-action: none;
        }

        @media (min-width: 640px) {
            .word-search-grid { max-width: 600px; }
        }

        .letter-cell {
            aspect-ratio: 1/1;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(12px, 3.5vw, 22px);
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.1s;
            color: #1f2937;
            text-transform: uppercase;
        }

        .letter-cell.selected {
            background-color: #fde047;
            color: #000;
            transform: scale(0.95);
        }

        .letter-cell.found {
            background-color: #22c55e;
            color: white;
            animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .letter-cell.revealed {
            background-color: #93c5fd;
            color: #1e3a8a;
        }

        @keyframes popIn {
            0% { transform: scale(0.5); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        .difficulty-btn {
            transition: all 0.2s;
            border-width: 2px;
        }
        .difficulty-btn:hover { transform: translateY(-1px); }
        .difficulty-btn.active {
            border-color: #1e3a8a;
            background-color: #eff6ff;
            color: #1e3a8a;
            font-weight: bold;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .difficulty-btn.active-chorizo {
            border-color: #dc2626;
            background-color: #fef2f2;
            color: #991b1b;
        }

        .pionero-header {
            border-bottom: 4px double #1f2937;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
        }

        /* Estilo para el selector de temática */
        .theme-select {
            appearance: none;
            background: transparent;
            border: none;
            border-bottom: 2px solid #1e3a8a;
            font-weight: 800;
            color: #1e3a8a;
            cursor: pointer;
            padding-right: 2rem;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%231e3a8a'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.25rem center;
            background-size: 1.5rem;
        }
        .theme-select:focus {
            outline: none;
            border-bottom-color: #fc5648;
        }
        .theme-select:hover {
            border-bottom-color: #fc5648;
        }
    </style>

        @if (app()->environment('production'))
        <script type="text/javascript">
            (function(c, l, a, r, i, t, y) {
                c[a] = c[a] || function() {
                    (c[a].q = c[a].q || []).push(arguments)
                };
                t = l.createElement(r);
                t.async = 1;
                t.src = "https://www.clarity.ms/tag/" + i;
                y = l.getElementsByTagName(r)[0];
                y.parentNode.insertBefore(t, y);
            })(window, document, "clarity", "script", "rsqwi6wyvd");
        </script>
    @endif
</head>

<body class="bg-gray-100 font-[Instrument Sans] text-gray-800 flex flex-col min-h-screen">
    <div class="w-full mx-auto md:p-4">
        <x-header />
        <x-navbar />

        <main class="flex-grow max-w-6xl mx-auto py-10 px-4 w-full">
            <div class="pionero-header text-center">
                <h1 class="text-4xl md:text-6xl font-extrabold uppercase tracking-tighter text-gray-900 leading-none">El Pionero</h1>
                <p class="text-xs md:text-sm font-bold text-gray-500 uppercase tracking-[0.3em] mt-2">
                    Sopa de letras
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <div class="lg:col-span-4 space-y-6 order-1 lg:order-1">
                    <div class="bg-white p-5 shadow-lg rounded-lg border border-gray-200">
                        <h3 class="text-xs font-bold uppercase text-gray-400 mb-4 tracking-wider">Dificultad</h3>
                        <div class="space-y-2">
                            <button onclick="setDifficulty('normal')" id="btn-normal" class="difficulty-btn w-full py-3 px-4 rounded border-transparent bg-gray-50 text-left flex justify-between items-center group">
                                <span class="text-sm">🙂 Normal</span>
                                <span class="text-[10px] text-gray-400 uppercase">Horiz/Vert</span>
                            </button>
                            <button onclick="setDifficulty('hard')" id="btn-hard" class="difficulty-btn w-full py-3 px-4 rounded border-transparent bg-gray-50 text-left flex justify-between items-center group">
                                <span class="text-sm">🤔 Difícil</span>
                                <span class="text-[10px] text-gray-400 uppercase">Mixto</span>
                            </button>
                            <button onclick="setDifficulty('chorizo')" id="btn-chorizo" class="difficulty-btn w-full py-3 px-4 rounded border-transparent bg-red-50 text-red-900 text-left flex justify-between items-center hover:bg-red-100">
                                <span class="text-sm font-bold">🌶️ Nivel Chorizo</span>
                                <span class="text-[10px] text-red-400 uppercase font-bold">¡Caos Total!</span>
                            </button>
                        </div>
                    </div>

                    <div class="hidden lg:block bg-white p-5 shadow-lg rounded-lg border border-gray-200">
                        <div class="flex justify-between items-center mb-4 border-b pb-2 border-gray-100">
                            <h3 class="text-xs font-bold uppercase text-gray-400 tracking-wider">Palabras a buscar MOVL</h3>
                            <span id="counter" class="text-xs font-bold bg-gray-100 px-2 py-0.5 rounded text-gray-600">0/10</span>
                        </div>
                        <ul id="word-list" class="grid grid-cols-2 gap-2 text-sm font-medium text-gray-600"></ul>
                        <div class="mt-6">
                            <button id="btn-solution" class="w-full text-red-500 hover:text-red-700 font-bold py-2 text-xs transition uppercase border border-transparent hover:border-red-100 rounded">
                                Mostrar Solución
                            </button>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-8 bg-white p-4 md:p-8 shadow-2xl rounded-lg border border-gray-200 order-2 lg:order-2">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <div class="relative w-full md:w-auto">
                            <label class="block text-[15px] font-bold text-blue-900 uppercase mb-1">Elige la temática</label>
                            <select id="theme-selector" onchange="changeTheme(this.value)" class="theme-select text-sm md:text-xl w-auto">
                                <option value="GENERAL">La Joya del Pacífico</option>
                                <option value="ASCENSORES">Nombres de Ascensores</option>
                                <option value="CERROS">Cerros Porteños</option>
                                <option value="PLACAS_CALLES">Plazas y Calles</option>
                                <option value="JUGADORES">Históricos Wanderers</option>
                            </select>
                            <p id="current-mode-text" class="text-md text-red-600 mt-1">Modo: Normal</p>
                        </div>
                        <span id="game-status" class="hidden text-xs font-bold uppercase bg-blue-100 text-blue-800 px-4 py-2 rounded-full animate-pulse">
                        </span>
                    </div>
                    
                    <div id="grid" class="word-search-grid"></div>

                    <div class="lg:hidden mt-3 pt-3 border-t border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-sm font-bold uppercase text-gray-700 tracking-wider">Palabras a buscar</h3>
                            <span id="counter-mobile" class="text-xs font-bold bg-gray-100 px-3 py-1 rounded text-gray-600">0/10</span>
                        </div>
                        <ul id="word-list-mobile" class="grid grid-cols-2 gap-1 text-xs font-medium text-gray-600 justify-items-left"></ul>
                        <div class="mt-6">
                            <button id="btn-solution-mobile" class="w-full text-red-500 hover:text-red-700 font-bold py-2 text-xs transition uppercase border border-transparent hover:border-red-100 rounded">
                                Mostrar Solución
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        @if (View::exists('components.footer'))
            <x-footer />
        @else
            <footer class="bg-gray-900 text-white py-8 mt-10">
                <div class="max-w-6xl mx-auto px-4 text-center">
                    <p class="font-bold uppercase tracking-widest mb-2">El Pionero de Valparaíso</p>
                    <p class="text-xs text-gray-500">&copy; {{ date('Y') }} Todos los derechos reservados.</p>
                </div>
            </footer>
        @endif
    </div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- TEMÁTICAS ---
        const THEMES = {
            GENERAL: [

    'VALPARAISO',   // "Tu Valparaiso puerto principal"
    'ARCOIRIS',     // "Eres un arcoiris de multiples colores"
    'MARGARITAS',   // "Tus mujeres son blancas margaritas"
    'PLACERES',     // "Del cerro los placeres..."
    'CORDILLERA',   // "Me vine al cordillera..."
    'VICTORIA',     // "La plaza de la victoria..."
    'TORPEDERA',    // "Torpedera de mis sueños..."
    'VOLANTIN',     // "...jugando al volantin"
    'MARIPOSAS',    // "Como las mariposas..."
    'PACIFICO'      // "La joya del pacifico...


            ],
            ASCENSORES: ['ARTILLERIA', 'VILLASECA', 'CONCEPCION', 'LARRAIN', 'BARON', 'POLANCO', 'MONJAS', 'FLORIDA', 'MARIPOSAS', 'PERAL'],
            CERROS: ['ALEGRE', 'CONCEPCION', 'BARON', 'POLANCO', 'LARRAIN', 'CARCEL', 'PLACERES', 'CORDILLERA', 'BELLAVISTA', 'MOLINO'],
            PLACAS_CALLES: ['VICTORIA', 'SOTOMAYOR', 'PINTO', 'ECHUURREN', 'MONTT', 'CONDELL', 'ESMERALDA', 'PRAT', 'BLANCO', 'ERRAZURIZ'],
            JUGADORES: ['OLIVARES', 'FIGUEROA', 'SANCHEZ', 'VILLARROEL', 'LETELIER', 'ORMEÑO', 'DUBOST'],
        };

        const SIZE = 12;
        const VECTORS = {
            H:  { r: 0, c: 1 }, V:  { r: 1, c: 0 }, D1: { r: 1, c: 1 }, D2: { r: -1, c: 1 },
            HR: { r: 0, c: -1 }, VR: { r: -1, c: 0 }, D1R:{ r: -1, c: -1 }, D2R:{ r: 1, c: -1 }
        };

        // --- ESTADO ---
        let state = {
            grid: [],
            solutions: {},
            difficulty: 'normal',
            currentTheme: 'GENERAL',
            foundCount: 0,
            gameActive: true,
            selection: { active: false, start: null, currentIds: [] }
        };

        const ui = {
            grid: document.getElementById('grid'),
            list: document.getElementById('word-list'),
            listMobile: document.getElementById('word-list-mobile'),
            status: document.getElementById('game-status'),
            counter: document.getElementById('counter'),
            counterMobile: document.getElementById('counter-mobile'),
            modeText: document.getElementById('current-mode-text'),
            btnSolution: document.getElementById('btn-solution'),
            btnSolutionMobile: document.getElementById('btn-solution-mobile')
        };

        // --- MANEJO DE TEMAS ---
        window.changeTheme = (themeKey) => {
            state.currentTheme = themeKey;
            initGame();
        };

        window.setDifficulty = (level) => {
            state.difficulty = level;
            updateDifficultyUI(level);
            initGame();
        };

        window.initGame = () => {
            state.gameActive = true;
            state.foundCount = 0;
            state.selection = { active: false, start: null, currentIds: [] };
            state.solutions = {};

            ui.status.classList.add('hidden');

            const currentPool = THEMES[state.currentTheme];

            [ui.btnSolution, ui.btnSolutionMobile].forEach(btn => {
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = "Mostrar Solución";
                    btn.classList.remove('opacity-50', 'line-through');
                }
            });

            if (ui.counter) ui.counter.textContent = `0/${currentPool.length}`;
            if (ui.counterMobile) ui.counterMobile.textContent = `0/${currentPool.length}`;

            renderWordList();
            generateGrid();
        };

        function updateDifficultyUI(level) {
            document.querySelectorAll('.difficulty-btn').forEach(b => b.classList.remove('active', 'active-chorizo'));
            const btn = document.getElementById(`btn-${level}`);
            if(level === 'chorizo') btn.classList.add('active-chorizo');
            else btn.classList.add('active');

            const labels = { normal: 'Normal', hard: 'Difícil', chorizo: 'Nivel Chorizo 🌶️' };
            ui.modeText.textContent = `Modo: ${labels[level]}`;
        }

        function generateGrid() {
            state.grid = Array(SIZE).fill().map(() => Array(SIZE).fill(''));
            const currentPool = THEMES[state.currentTheme];
            const sortedWords = [...currentPool].sort((a, b) => b.length - a.length);

            sortedWords.forEach((word, index) => {
                let placed = false;
                let attempts = 0;
                const dirs = getAllowedVectors(state.difficulty, index);

                while (!placed && attempts < 300) {
                    const vecName = dirs[Math.floor(Math.random() * dirs.length)];
                    const vec = VECTORS[vecName];
                    const r = Math.floor(Math.random() * SIZE);
                    const c = Math.floor(Math.random() * SIZE);

                    if (canPlace(word, r, c, vec)) {
                        place(word, r, c, vec);
                        placed = true;
                    }
                    attempts++;
                }
            });
            renderGridHTML();
        }

        function getAllowedVectors(diff, idx) {
            if (diff === 'normal') return ['H', 'V'];
            if (diff === 'hard') return (idx < 5) ? ['H', 'V'] : ['D1', 'D2', 'HR', 'VR', 'D1R', 'D2R'];
            if (diff === 'chorizo') return ['D1', 'D2', 'D1R', 'D2R', 'HR', 'VR'];
            return ['H'];
        }

        function canPlace(word, startR, startC, vec) {
            for (let i = 0; i < word.length; i++) {
                const r = startR + (i * vec.r);
                const c = startC + (i * vec.c);
                if (r < 0 || r >= SIZE || c < 0 || c >= SIZE) return false;
                if (state.grid[r][c] !== '' && state.grid[r][c] !== word[i]) return false;
            }
            return true;
        }

        function place(word, startR, startC, vec) {
            const coords = [];
            for (let i = 0; i < word.length; i++) {
                const r = startR + (i * vec.r);
                const c = startC + (i * vec.c);
                state.grid[r][c] = word[i];
                coords.push(`${r}-${c}`);
            }
            state.solutions[word] = coords;
        }

        function renderWordList() {
            const currentPool = THEMES[state.currentTheme];
            const containers = [
                { el: ui.list, prefix: 'list-' },
                { el: ui.listMobile, prefix: 'list-mobile-' }
            ];

            containers.forEach(container => {
                if (container.el) {
                    container.el.innerHTML = '';
                    currentPool.forEach(word => {
                        const li = document.createElement('li');
                        li.id = `${container.prefix}${word}`;
                        li.className = 'flex items-center p-1 transition-all rounded';
                        li.innerHTML = `<span class="w-4 h-4 mr-2 border border-gray-300 rounded-sm flex items-center justify-center text-[10px] text-transparent">✓</span><span class="opacity-80">${word}</span>`;
                        container.el.appendChild(li);
                    });
                }
            });
        }

        function renderGridHTML() {
            ui.grid.innerHTML = '';
            for (let r = 0; r < SIZE; r++) {
                for (let c = 0; c < SIZE; c++) {
                    if (state.grid[r][c] === '') {
                        state.grid[r][c] = String.fromCharCode(65 + Math.floor(Math.random() * 26));
                    }
                    const cell = document.createElement('div');
                    cell.className = 'letter-cell select-none';
                    cell.textContent = state.grid[r][c];
                    cell.dataset.id = `${r}-${c}`;
                    cell.dataset.r = r;
                    cell.dataset.c = c;

                    cell.addEventListener('mousedown', (e) => startSelect(r, c, e));
                    cell.addEventListener('mouseenter', () => updateSelect(r, c));
                    cell.addEventListener('touchstart', (e) => { 
                        if(e.cancelable) e.preventDefault(); 
                        startSelect(r, c); 
                    }, { passive: false });
                    
                    ui.grid.appendChild(cell);
                }
            }
        }

        window.addEventListener('mouseup', endSelect);
        window.addEventListener('touchend', endSelect);
        
        ui.grid.addEventListener('touchmove', (e) => {
            if(!state.selection.active) return;
            e.preventDefault();
            const touch = e.touches[0];
            const el = document.elementFromPoint(touch.clientX, touch.clientY);
            if (el && el.classList.contains('letter-cell')) {
                updateSelect(parseInt(el.dataset.r), parseInt(el.dataset.c));
            }
        }, { passive: false });

        function startSelect(r, c, e) {
            if (!state.gameActive) return;
            if(e && e.button !== 0) return;
            state.selection.active = true;
            state.selection.start = { r, c };
            paintSelection([`${r}-${c}`]);
        }

        function updateSelect(r, c) {
            if (!state.selection.active) return;
            const start = state.selection.start;
            const dx = r - start.r;
            const dy = c - start.c;
            const isLine = (dx === 0) || (dy === 0) || (Math.abs(dx) === Math.abs(dy));

            if (isLine) {
                const steps = Math.max(Math.abs(dx), Math.abs(dy));
                const stepR = steps === 0 ? 0 : dx / steps;
                const stepC = steps === 0 ? 0 : dy / steps;
                const path = [];
                for (let i = 0; i <= steps; i++) {
                    path.push(`${start.r + (i * stepR)}-${start.c + (i * stepC)}`);
                }
                paintSelection(path);
            }
        }

        function paintSelection(ids) {
            document.querySelectorAll('.letter-cell.selected').forEach(el => el.classList.remove('selected'));
            state.selection.currentIds = ids;
            ids.forEach(id => {
                const el = document.querySelector(`[data-id="${id}"]`);
                if (el && !el.classList.contains('found')) el.classList.add('selected');
            });
        }

        function endSelect() {
            if (!state.selection.active) return;
            state.selection.active = false;
            
            const currentPool = THEMES[state.currentTheme];
            const wordStr = state.selection.currentIds.map(id => {
                const [r, c] = id.split('-');
                return state.grid[r][c];
            }).join('');

            const reversed = wordStr.split('').reverse().join('');
            let match = null;
            if (currentPool.includes(wordStr)) match = wordStr;
            else if (currentPool.includes(reversed)) match = reversed;

            if (match) {
                const listEl = document.getElementById(`list-${match}`);
                if (!listEl.querySelector('span:first-child').classList.contains('bg-green-500')) {
                    triggerFound(match);
                } else {
                    paintSelection([]);
                }
            } else {
                paintSelection([]);
            }
        }

        function triggerFound(word) {
            const currentPool = THEMES[state.currentTheme];
            state.selection.currentIds.forEach(id => {
                const el = document.querySelector(`[data-id="${id}"]`);
                el.classList.remove('selected');
                el.classList.add('found');
            });

            const ids = [`list-${word}`, `list-mobile-${word}`];
            ids.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.classList.add('text-green-700', 'font-bold', 'bg-green-50');
                    const icon = el.querySelector('span:first-child');
                    icon.classList.remove('text-transparent', 'border-gray-300');
                    icon.classList.add('bg-green-500', 'border-green-500', 'text-white');
                    el.querySelector('span:last-child').classList.remove('opacity-80');
                }
            });

            state.foundCount++;
            if (ui.counter) ui.counter.textContent = `${state.foundCount}/${currentPool.length}`;
            if (ui.counterMobile) ui.counterMobile.textContent = `${state.foundCount}/${currentPool.length}`;

            if (state.foundCount === currentPool.length) {
                state.gameActive = false;
                ui.status.textContent = "¡COMPLETADO!";
                ui.status.className = "text-xs font-bold uppercase bg-green-500 text-white px-6 py-2 rounded-full shadow-lg";
                ui.status.classList.remove('hidden');
                setTimeout(() => alert("¡Excelente! Has completado el recorrido por Valparaíso."), 300);
            }
        }

        function showSolution() {
            if(!confirm('¿Rendirse? Se mostrarán todas las palabras.')) return;
            state.gameActive = false;
            paintSelection([]);

            Object.entries(state.solutions).forEach(([word, coords]) => {
                coords.forEach(id => {
                    const el = document.querySelector(`[data-id="${id}"]`);
                    if (el && !el.classList.contains('found')) el.classList.add('revealed');
                });

                [`list-${word}`, `list-mobile-${word}`].forEach(id => {
                    const el = document.getElementById(id);
                    if (el && !el.querySelector('span:first-child').classList.contains('bg-green-500')) {
                        el.classList.add('text-blue-500', 'opacity-60');
                    }
                });
            });

            [ui.btnSolution, ui.btnSolutionMobile].forEach(btn => {
                if(btn) {
                    btn.textContent = "Juego Finalizado";
                    btn.disabled = true;
                }
            });
        }

        if (ui.btnSolution) ui.btnSolution.addEventListener('click', showSolution);
        if (ui.btnSolutionMobile) ui.btnSolutionMobile.addEventListener('click', showSolution);

        setDifficulty('normal'); 
    });
</script>
</body>
</html>