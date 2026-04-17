<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portle - El Pionero de Valparaíso</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* --- ESTILOS DEL TABLERO --- */
        .board-row {
            display: grid;
            gap: 4px;
            margin-bottom: 4px;
            width: 100%;
            max-width: 500px;
            justify-content: center;
        }

        .tile {
            aspect-ratio: 1 / 1;
            border: 2px solid #d1d5db;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 800;
            text-transform: uppercase;
            user-select: none;
            transition: all 0.2s ease;
        }

        .tile.filled {
            border-color: #6b7280;
            animation: pop 0.1s;
        }

        /* Colores de estado */
        .tile.correct {
            background-color: #22c55e; border-color: #22c55e; color: white;
            animation: flip 0.6s ease;
        }
        .tile.present {
            background-color: #eab308; border-color: #eab308; color: white;
            animation: flip 0.6s ease;
        }
        .tile.absent {
            background-color: #6b7280; border-color: #6b7280; color: white;
            animation: flip 0.6s ease;
        }

        /* --- ESTILOS DEL TECLADO --- */
        .key {
            height: 54px;
            border-radius: 4px;
            border: none;
            background-color: #d1d5db;
            color: #1f2937;
            font-weight: bold;
            cursor: pointer;
            user-select: none;
            transition: all 0.1s;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            touch-action: manipulation;
        }

        .key:active { transform: scale(0.95); background-color: #9ca3af; }
        @media (hover: hover) {
            .key:hover { background-color: #9ca3af; }
        }

        .key.correct { background-color: #22c55e; color: white; border-color: #22c55e; }
        .key.present { background-color: #eab308; color: white; border-color: #eab308; }
        .key.absent { background-color: #6b7280; color: white; border-color: #6b7280; } /* Corregido color gris */

        .key.large { flex: 1.5; font-size: 0.75rem; }

        .key[data-key="ENTER"] {
            background-color: #22c55e;
            color: white;
        }
        .key[data-key="ENTER"]:hover {
            background-color: #16a34a;
        }

        /* --- ANIMACIONES --- */
        @keyframes pop { 50% { transform: scale(1.1); } }
        @keyframes flip { 
            0% { transform: rotateX(0); } 
            50% { transform: rotateX(90deg); } 
            100% { transform: rotateX(0); } 
        }
        @keyframes shake {
            10%, 90% { transform: translateX(-1px); }
            20%, 80% { transform: translateX(2px); }
            30%, 50%, 70% { transform: translateX(-4px); }
            40%, 60% { transform: translateX(4px); }
        }
        .shake { animation: shake 0.4s linear; }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }
        .bounce { animation: bounce 0.5s ease-in-out; }

        /* --- ESTILOS UI --- */
        .length-selector {
            padding: 0.375rem 0.875rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 9999px;
            border: 2px solid #e5e7eb;
            background-color: white;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }
        .length-selector:hover {
            border-color: #3b82f6;
            color: #3b82f6;
            transform: translateY(-1px);
        }
        .length-selector.active {
            background-color: #3b82f6;
            border-color: #3b82f6;
            color: white;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
        }
        .length-selector:active {
            transform: scale(0.95);
        }

        /* Botón de Pista */
        .hint-btn {
            background-color: #fffbeb;
            color: #b45309;
            border: 1px solid #fcd34d;
        }
        .hint-btn:hover {
            background-color: #fef3c7;
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

<body class="bg-slate-50 min-h-screen flex flex-col font-sans text-gray-800">
    
    <div class="w-full flex-grow flex flex-col">
        <x-header />
        <x-navbar />

        <main class="flex-grow w-full max-w-lg mx-auto p-2 md:p-4 flex flex-col items-center justify-start sm:justify-center">
            
            <div class="text-center mb-4 md:mb-6 w-full mt-2 md:mt-0">
                <h1 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tighter mb-1">
                    PORTLE
                </h1>
                <h3 class="text-sm text-gray-500 font-bold uppercase tracking-widest mb-3">Edición Valparaíso</h3>
                
                <div class="mt-2 flex justify-center items-center gap-2">
                    <button onclick="showInstructions()" class="text-xs bg-gray-200 hover:bg-gray-300 px-3 py-1.5 rounded-full transition flex items-center gap-1 font-bold text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Ayuda
                    </button>
                    
                    <button onclick="showHint()" class="text-xs hint-btn px-3 py-1.5 rounded-full transition flex items-center gap-1 font-bold shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                        Pista
                    </button>

                    <span id="word-length-badge" class="ml-2 text-xs bg-blue-100 text-blue-800 px-3 py-1.5 rounded-full font-bold border border-blue-200">
                        Todas
                    </span>
                </div>

                <div class="mt-4 w-full px-4">
                    <p class="text-[10px] text-gray-400 text-center mb-2 font-bold uppercase tracking-wider">Filtrar por letras</p>
                    <div class="flex justify-center gap-1.5 flex-wrap">
                        <button onclick="selectWordLength(null)" class="length-selector active" data-length="all">Todas</button>
                        <button onclick="selectWordLength(5)" class="length-selector" data-length="5">5</button>
                        <button onclick="selectWordLength(6)" class="length-selector" data-length="6">6</button>
                        <button onclick="selectWordLength(7)" class="length-selector" data-length="7">7</button>
                        <button onclick="selectWordLength(8)" class="length-selector" data-length="8">8</button>
                        <button onclick="selectWordLength(9)" class="length-selector" data-length="9">9</button>
                        <button onclick="selectWordLength(10)" class="length-selector" data-length="10">10+</button>
                    </div>
                </div>
            </div>

            <div id="message-container" class="fixed top-24 left-0 right-0 flex justify-center pointer-events-none z-50 px-4">
            </div>

            <div id="board-container" class="w-full flex flex-col items-center mb-4 md:mb-8 flex-grow justify-center max-h-[420px]">
            </div>

            <div class="w-full max-w-md bg-white p-1.5 md:p-2 rounded-xl shadow-sm border border-gray-200 select-none select-none mb-4 md:mb-0">
                <div id="keyboard" class="flex flex-col gap-1.5">
                    <div class="flex gap-1">
                        <button class="key" data-key="Q">Q</button><button class="key" data-key="W">W</button><button class="key" data-key="E">E</button><button class="key" data-key="R">R</button><button class="key" data-key="T">T</button><button class="key" data-key="Y">Y</button><button class="key" data-key="U">U</button><button class="key" data-key="I">I</button><button class="key" data-key="O">O</button><button class="key" data-key="P">P</button>
                    </div>
                    <div class="flex gap-1 px-2 md:px-4">
                        <button class="key" data-key="A">A</button><button class="key" data-key="S">S</button><button class="key" data-key="D">D</button><button class="key" data-key="F">F</button><button class="key" data-key="G">G</button><button class="key" data-key="H">H</button><button class="key" data-key="J">J</button><button class="key" data-key="K">K</button><button class="key" data-key="L">L</button><button class="key" data-key="Ñ">Ñ</button>
                    </div>
                    <div class="flex gap-1">
                        <button class="key large font-black text-xs" data-key="ENTER">ENVIAR</button>
                        <button class="key" data-key="Z">Z</button><button class="key" data-key="X">X</button><button class="key" data-key="C">C</button><button class="key" data-key="V">V</button><button class="key" data-key="B">B</button><button class="key" data-key="N">N</button><button class="key" data-key="M">M</button>
                        <button class="key large" data-key="BACKSPACE">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z" /></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div id="new-game-container" class="hidden w-full flex justify-center mt-4 pb-4 md:pb-0">
                <button onclick="newGame()" class="bg-slate-900 text-white font-bold py-3 px-10 rounded-full hover:scale-105 transition-all shadow-xl animate-bounce flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    Jugar otra vez
                </button>
            </div>

        </main>
        
        <x-footer />
    </div>

    <div id="instructionsModal" class="fixed inset-0 bg-slate-900/50 hidden items-start md:items-center justify-center z-50 backdrop-blur-sm p-4 overflow-y-auto" onclick="hideInstructions()">
        <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl mt-8 md:mt-0 relative animate-popIn" onclick="event.stopPropagation()">
            <button onclick="hideInstructions()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <h2 class="text-2xl font-black mb-2 uppercase text-blue-900 text-center">Reglas Porteñas</h2>
            <p class="mb-6 text-center text-gray-600">Adivina la palabra oculta en 6 intentos.</p>
            
            <div class="space-y-4 mb-8">
                <div class="flex items-center gap-4 p-2 rounded-lg bg-green-50 border border-green-100">
                    <div class="w-10 h-10 bg-green-500 text-white font-bold flex items-center justify-center rounded text-xl shadow-sm">C</div>
                    <p class="text-sm text-green-800 font-medium leading-tight">Letra correcta en la posición correcta.</p>
                </div>
                <div class="flex items-center gap-4 p-2 rounded-lg bg-yellow-50 border border-yellow-100">
                    <div class="w-10 h-10 bg-yellow-500 text-white font-bold flex items-center justify-center rounded text-xl shadow-sm">E</div>
                    <p class="text-sm text-yellow-800 font-medium leading-tight">Letra correcta pero en otra posición.</p>
                </div>
                <div class="flex items-center gap-4 p-2 rounded-lg bg-gray-100 border border-gray-200">
                    <div class="w-10 h-10 bg-gray-500 text-white font-bold flex items-center justify-center rounded text-xl shadow-sm">R</div>
                    <p class="text-sm text-gray-600 font-medium leading-tight">La letra no está en la palabra.</p>
                </div>
            </div>
            
            <button onclick="hideInstructions()" class="w-full bg-blue-600 text-white font-bold py-3.5 rounded-xl hover:bg-blue-700 transition shadow-md text-lg">
                ¡VAMOS A JUGAR!
            </button>
        </div>
    </div>

    <script>
        // DATA ESTRUCTURADA: Objeto { word, hint }
        const PALABRAS_VALPARAISO = [
            // Cerros
            { word: 'ALEGRE', hint: 'Nombre de Cerro' },
            { word: 'CONCEPCION', hint: 'Nombre de Cerro' },
            { word: 'ARTILLERIA', hint: 'Nombre de Cerro / Ascensor' },
            { word: 'PLAYANCHA', hint: 'Nombre de Cerro (Coloquial)' },
            { word: 'BARON', hint: 'Nombre de Cerro / Muelle / Ascensor' },
            { word: 'POLANCO', hint: 'Nombre de Cerro / Ascensor' },
            { word: 'CORDILLERA', hint: 'Nombre de Cerro' },
            { word: 'PANTEON', hint: 'Nombre de Cerro' },
            { word: 'CARCEL', hint: 'Nombre de Cerro' },
            { word: 'MARIPOSAS', hint: 'Nombre de Cerro' },
            { word: 'MONJAS', hint: 'Nombre de Cerro / Ascensor' },
            { word: 'LECHEROS', hint: 'Nombre de Cerro' },
            { word: 'LARRAIN', hint: 'Nombre de Cerro' },
            { word: 'RECREO', hint: 'Barrio / Cerro limítrofe' },
            { word: 'ESPERANZA', hint: 'Nombre de Cerro' },
            { word: 'PLACERES', hint: 'Nombre de Cerro' },
            { word: 'OHIGGINS', hint: 'Nombre de Cerro' },
            { word: 'ROCUANT', hint: 'Nombre de Cerro' },
            { word: 'YUNGAY', hint: 'Nombre de Cerro' },
            { word: 'BELLAVISTA', hint: 'Nombre de Cerro' },
            { word: 'PERAL', hint: 'Nombre de Ascensor' },
            { word: 'VILLASECA', hint: 'Nombre de Ascensor' },
            { word: 'FLORIDA', hint: 'Nombre de Cerro / Ascensor' },
            { word: 'MARIPOSA', hint: 'Nombre de Ascensor' },
            
            // Transporte
            { word: 'TROLE', hint: 'Transporte típico' },
            { word: 'TROLEBUS', hint: 'Transporte típico' },
            { word: 'MICRO', hint: 'Transporte público' },
            { word: 'COLECTIVO', hint: 'Transporte público' },
            { word: 'METRO', hint: 'Transporte (Merval)' },
            { word: 'MERVAL', hint: 'Tren de la región' },
            { word: 'ESTACION', hint: 'Lugar de trenes' },
            { word: 'RIEL', hint: 'Parte de la vía del tren' },
            { word: 'CABINA', hint: 'Parte del ascensor' },
            { word: 'FUNICULAR', hint: 'Tipo de ascensor' },
            { word: 'PLANO', hint: 'Zona baja de la ciudad' },
            { word: 'ESPIRITU', hint: 'Nombre de Ascensor (Espíritu Santo)' },
            { word: 'REINA', hint: 'Nombre de Ascensor (Reina Victoria)' },
            
            // Puerto y Mar
            { word: 'PUERTO', hint: 'Lugar de barcos' },
            { word: 'MUELLE', hint: 'Estructura en el mar' },
            { word: 'DIQUE', hint: 'Para reparar barcos' },
            { word: 'GRUA', hint: 'Maquinaria portuaria' },
            { word: 'ESTIBADOR', hint: 'Trabajador portuario' },
            { word: 'CHORERO', hint: 'Relacionado al puerto (coloquial)' },
            { word: 'LANCHA', hint: 'Embarcación pequeña' },
            { word: 'BOTE', hint: 'Embarcación pequeña' },
            { word: 'REMOLCADOR', hint: 'Barco que tira otros barcos' },
            { word: 'TRANSATLANTICO', hint: 'Barco gigante' },
            { word: 'CONTAINER', hint: 'Caja metálica de carga' },
            { word: 'ADUANA', hint: 'Edificio de control' },
            { word: 'FARO', hint: 'Luz guía en la costa' },
            { word: 'ROMPEOLAS', hint: 'Protección contra el mar' },
            { word: 'BAHIA', hint: 'Entrada de mar' },
            { word: 'COSTANERA', hint: 'Calle junto al mar' },
            { word: 'PESCADOR', 'hint': 'Oficio de mar' },
            { word: 'JAIVA', hint: 'Crustáceo típico' },
            { word: 'MERLUZA', hint: 'Pescado típico' },
            { word: 'GAVIOTA', hint: 'Ave costera' },
            
            // Cultura y Personajes
            { word: 'WANDERERS', hint: 'El equipo de fútbol' },
            { word: 'CATURRO', hint: 'Apodo del equipo local' },
            { word: 'LUKAS', hint: 'Dibujante famoso (Renzo Pecchenino)' },
            { word: 'NERUDA', hint: 'Poeta Nobel' },
            { word: 'SEBASTIANA', hint: 'Casa de Neruda' },
            { word: 'GITANO', hint: 'Personaje (Gitano Rodríguez)' },
            { word: 'PAYASO', hint: 'Artista callejero' },
            { word: 'MIMOS', hint: 'Artista callejero' },
            { word: 'CARNAVAL', hint: 'Fiesta callejera (Mil Tambores)' },
            { word: 'TAMBORES', hint: 'Instrumento de carnaval' },
            { word: 'MILCHAS', hint: 'Pan tradicional (Milcao - sureño pero común)' },
            { word: 'CHORRILLANA', hint: 'Plato típico porteño' },
            { word: 'CUECA', hint: 'Baile nacional' },
            { word: 'BOHEMIA', hint: 'Vida nocturna y cultural' },
            { word: 'CINZANO', hint: 'Bar tradicional' },
            { word: 'PAJARITO', hint: 'Dulce chileno' },
            { word: 'JOYA', hint: 'Apodo de la ciudad (Joya del Pacífico)' },
            { word: 'PATRIMONIO', hint: 'Título de la UNESCO' },
            { word: 'UNESCO', hint: 'Organización mundial' },
            { word: 'GRAFFITI', hint: 'Arte en los muros' },
            
            // Calles y Lugares
            { word: 'PRAT', hint: 'Calle financiera' },
            { word: 'SOTOMAYOR', hint: 'Plaza principal' },
            { word: 'ANCHA', hint: 'Playa...' },
            { word: 'SERRANO', hint: 'Calle comercial' },
            { word: 'ESMERALDA', hint: 'Calle céntrica' },
            { word: 'CONDELL', hint: 'Calle comercial' },
            { word: 'PEDRO', hint: 'Calle (Pedro Montt)' },
            { word: 'MONTT', hint: 'Apellido de Calle Principal' },
            { word: 'VICTORIA', hint: 'Plaza o Calle' },
            { word: 'FRANCIA', hint: 'Avenida principal' },
            { word: 'BRASIL', hint: 'Avenida universitaria' },
            { word: 'ITALIA', hint: 'Paseo o Parque' },
            { word: 'ECUADOR', hint: 'Calle de bohemia' },
            { word: 'CUMMING', hint: 'Calle de subida' },
            { word: 'DINAMARCA', hint: 'Calle en cerro' },
            { word: 'URRIOLA', hint: 'Calle de subida' },
            { word: 'ALMIRANTE', hint: 'Grado naval (Montt/Simpsom)' },
            { word: 'SUBIDA', hint: 'Calle empinada' },
            { word: 'ESCALERA', hint: 'Para subir a pie' },
            { word: 'MIRADOR', hint: 'Lugar con vista' }
        ];

        const ROWS = 6;
        let currentWordCols = 5;
        let selectedLength = null; // null = todas

        let targetWord = '';
        let displayWord = '';
        let currentHint = ''; // Variable para guardar la pista actual
        let currentRow = 0;
        let currentCol = 0;
        let gameOver = false;
        let boardState = [];
        let keyboardState = {};

        function init() {
            attachEventListeners();
            newGame();
        }

        function normalizeWord(word) {
            return word.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toUpperCase();
        }

        function selectWordLength(length) {
            selectedLength = length;

            // Actualizar UI de los botones
            document.querySelectorAll('.length-selector').forEach(btn => {
                btn.classList.remove('active');
            });
            const activeBtn = document.querySelector(`.length-selector[data-length="${length === null ? 'all' : length}"]`);
            if (activeBtn) activeBtn.classList.add('active');

            // Iniciar nuevo juego con la longitud seleccionada
            newGame();
        }

        function getFilteredWords() {
            if (selectedLength === null) {
                return PALABRAS_VALPARAISO;
            }

            return PALABRAS_VALPARAISO.filter(item => {
                const len = item.word.length;
                if (selectedLength === 10) {
                    return len >= 10; 
                }
                return len === selectedLength;
            });
        }

        function newGame() {
            // Obtener palabras filtradas
            const availableWords = getFilteredWords();

            if (availableWords.length === 0) {
                showMessage('No hay palabras de ese largo', 'red');
                return;
            }

            // Selección de palabra aleatoria
            let selection;
            do {
                selection = availableWords[Math.floor(Math.random() * availableWords.length)];
            } while (selection.word.toUpperCase() === displayWord && availableWords.length > 1);

            displayWord = selection.word.toUpperCase();
            targetWord = normalizeWord(selection.word);
            currentHint = selection.hint; // Guardar la pista
            currentWordCols = targetWord.length;

            currentRow = 0;
            currentCol = 0;
            gameOver = false;
            boardState = Array(ROWS).fill().map(() => Array(currentWordCols).fill(''));
            keyboardState = {};

            document.getElementById('new-game-container').classList.add('hidden');
            document.getElementById('message-container').innerHTML = '';

            // Actualizar badge
            document.getElementById('word-length-badge').textContent = `${currentWordCols} Letras`;
            
            // Resetear teclado visual
            document.querySelectorAll('.key').forEach(key => {
                key.classList.remove('correct', 'present', 'absent');
            });

            createBoardDOM();
            console.log('Dev Hint:', targetWord); 
        }

        function createBoardDOM() {
            const boardContainer = document.getElementById('board-container');
            boardContainer.innerHTML = '';

            // Ajustar tamaño de fuente según longitud
            let fontSizeClass = 'text-2xl';
            if (currentWordCols > 6) fontSizeClass = 'text-xl';
            if (currentWordCols > 8) fontSizeClass = 'text-lg';
            if (currentWordCols >= 10) fontSizeClass = 'text-base';

            for (let i = 0; i < ROWS; i++) {
                const row = document.createElement('div');
                row.className = 'board-row';
                row.id = `row-${i}`;
                
                // Grid dinámico
                row.style.gridTemplateColumns = `repeat(${currentWordCols}, minmax(auto, 54px))`;

                for (let j = 0; j < currentWordCols; j++) {
                    const tile = document.createElement('div');
                    tile.className = `tile ${fontSizeClass}`;
                    tile.id = `tile-${i}-${j}`;
                    row.appendChild(tile);
                }
                boardContainer.appendChild(row);
            }
        }

        function attachEventListeners() {
            document.getElementById('keyboard').addEventListener('click', (e) => {
                const target = e.target.closest('.key');
                if (!target) return;
                target.style.transform = 'scale(0.95)';
                setTimeout(() => target.style.transform = '', 100);
                
                handleInput(target.dataset.key);
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') handleInput('ENTER');
                else if (e.key === 'Backspace') handleInput('BACKSPACE');
                else if (e.key.length === 1 && e.key.match(/[a-zñ]/i)) handleInput(e.key.toUpperCase());
            });
        }

        function handleInput(key) {
            if (gameOver) return;
            if (key === 'ENTER') submitGuess();
            else if (key === 'BACKSPACE') deleteLetter();
            else addLetter(key);
        }

        function addLetter(letter) {
            if (currentCol < currentWordCols) {
                boardState[currentRow][currentCol] = letter;
                const tile = document.getElementById(`tile-${currentRow}-${currentCol}`);
                tile.textContent = letter;
                tile.classList.add('filled');
                tile.animate([
                    { transform: 'scale(1)' },
                    { transform: 'scale(1.15)' },
                    { transform: 'scale(1)' }
                ], { duration: 150, easing: 'ease-out' });
                currentCol++;
            }
        }

        function deleteLetter() {
            if (currentCol > 0) {
                currentCol--;
                boardState[currentRow][currentCol] = '';
                const tile = document.getElementById(`tile-${currentRow}-${currentCol}`);
                tile.textContent = '';
                tile.classList.remove('filled');
            }
        }

        function submitGuess() {
            if (currentCol !== currentWordCols) {
                showMessage('Palabra incompleta', 'red');
                shakeRow(currentRow);
                return;
            }

            const guessArr = boardState[currentRow];
            const guessString = guessArr.join('');
            
            // Deshabilitar input mientras anima
            gameOver = true; 
            checkGuess(guessArr, guessString);
        }

        function checkGuess(guessArr, guessString) {
            const targetArray = targetWord.split('');
            const result = Array(currentWordCols).fill('absent');
            const letterCounts = {};
            for (const char of targetArray) letterCounts[char] = (letterCounts[char] || 0) + 1;

            // Paso 1: Correctas
            for (let i = 0; i < currentWordCols; i++) {
                if (guessArr[i] === targetArray[i]) {
                    result[i] = 'correct';
                    letterCounts[guessArr[i]]--;
                }
            }
            // Paso 2: Presentes
            for (let i = 0; i < currentWordCols; i++) {
                if (result[i] !== 'correct' && letterCounts[guessArr[i]] > 0) {
                    result[i] = 'present';
                    letterCounts[guessArr[i]]--;
                }
            }
            animateResults(result, guessArr, guessString);
        }

        function animateResults(result, guessArr, guessString) {
            for (let i = 0; i < currentWordCols; i++) {
                const tile = document.getElementById(`tile-${currentRow}-${i}`);
                setTimeout(() => {
                    tile.classList.add(result[i]);
                    updateKeyboardKey(guessArr[i], result[i]);
                    if (i === currentWordCols - 1) {
                        setTimeout(() => checkWinCondition(guessString), 300);
                    }
                }, i * 250);
            }
        }

        function checkWinCondition(guessString) {
            if (guessString === targetWord) {
                showMessage('¡EXCELENTE! 🎉', 'green');
                celebrateRow(currentRow);
                endGame();
            } else if (currentRow === ROWS - 1) {
                showMessage(`La palabra era: ${displayWord}`, 'black', true);
                endGame();
            } else {
                currentRow++;
                currentCol = 0;
                gameOver = false; // Habilitar input de nuevo
            }
        }

        function endGame() {
            gameOver = true;
            document.getElementById('new-game-container').classList.remove('hidden');
        }

        function updateKeyboardKey(letter, state) {
            const keyBtn = document.querySelector(`.key[data-key="${letter}"]`);
            if (!keyBtn) return;
            const oldState = keyboardState[letter];
            if (oldState === 'correct') return;
            if (oldState === 'present' && state === 'absent') return;

            keyboardState[letter] = state;
            keyBtn.classList.remove('correct', 'present', 'absent');
            keyBtn.classList.add(state);
        }

        // Nueva función para mostrar la pista
        function showHint() {
            if (!gameOver) {
                showMessage(`PISTA: ${currentHint}`, 'blue');
            }
        }

        function showMessage(text, type, permanent = false) {
            const container = document.getElementById('message-container');
            const msg = document.createElement('div');
            let classes = 'px-6 py-3 rounded-full font-bold text-sm shadow-xl animate-popIn pointer-events-auto flex items-center gap-2 backdrop-blur-sm';
            
            if (type === 'green') classes += ' bg-green-500/95 text-white';
            else if (type === 'red') classes += ' bg-white text-red-600 border-2 border-red-100';
            else if (type === 'blue') classes += ' bg-blue-600/95 text-white'; // Nuevo estilo para pista
            else classes += ' bg-slate-800/95 text-white';

            msg.className = classes;
            msg.innerHTML = text;
            container.innerHTML = '';
            container.appendChild(msg);

            // Solo auto-ocultar mensajes rojos y azules (si no es permanente)
            if ((type === 'red' || type === 'blue') && !permanent) {
                setTimeout(() => {
                    if (container.contains(msg)) {
                        msg.classList.remove('animate-popIn');
                        msg.classList.add('animate-popOut');
                        setTimeout(() => container.removeChild(msg), 300);
                    }
                }, 3000); // 3 segundos para leer la pista
            }
        }

        function shakeRow(rowIdx) {
            const row = document.getElementById(`row-${rowIdx}`);
            row.classList.add('shake');
            setTimeout(() => row.classList.remove('shake'), 400);
        }

        function celebrateRow(rowIdx) {
            for (let i = 0; i < currentWordCols; i++) {
                const tile = document.getElementById(`tile-${rowIdx}-${i}`);
                setTimeout(() => tile.classList.add('bounce'), i * 100);
            }
        }

        function showInstructions() {
            const modal = document.getElementById('instructionsModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.querySelector('div').scrollTop = 0;
        }

        function hideInstructions() {
            const modal = document.getElementById('instructionsModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        init();
    </script>
    <style>
        @keyframes popIn {
            0% { opacity: 0; transform: scale(0.8) translateY(-20px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        @keyframes popOut {
            0% { opacity: 1; transform: scale(1); }
            100% { opacity: 0; transform: scale(0.8); }
        }
        .animate-popIn { animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
        .animate-popOut { animation: popOut 0.3s ease-in forwards; }
    </style>
</body>
</html>