<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Trivia Porteña - El Pionero de Valparaíso</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if (app()->environment('production'))
        <script type="text/javascript">
            (function(c, l, a, r, i, t, y) {
                c[a] = c[a] || function() {
                    (c[a].q = c[a].q || []).push(arguments)
                };
                t = l.createElement(r); t.async = 1;
                t.src = "https://www.clarity.ms/tag/" + i;
                y = l.getElementsByTagName(r)[0]; y.parentNode.insertBefore(t, y);
            })(window, document, "clarity", "script", "rsqwi6wyvd");
        </script>
    @endif

    <style>
        .pionero-border { border: 3px solid #000; }
        .bg-pionero-red { background-color: #fc5648; }
        .bg-pionero-yellow { background-color: #eba81d; }
        
        .game-layout {
            display: grid;
            grid-template-columns: 1fr 40px;
            gap: 1.5rem;
        }

        @media (max-width: 768px) {
            .game-layout {
                grid-template-columns: 1fr;
                grid-template-rows: auto 40px;
            }
        }

        .vertical-progress-container {
            width: 100%;
            height: 100%;
            background: #eee;
            border: 2px solid #000;
            position: relative;
            display: flex;
            flex-direction: column-reverse;
            overflow: hidden;
        }

        @media (max-width: 768px) {
            .vertical-progress-container {
                flex-direction: row;
                height: 40px;
                min-height: 40px;
            }

            .vertical-progress-fill {
                height: 100% !important;
                width: 0%;
                transition: width 0.5s ease;
            }
        }

        .vertical-progress-fill {
            background-color: #000;
            width: 100%;
            transition: height 0.5s ease;
        }

        .timer-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 4px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            position: relative;
            overflow: hidden;
        }

        .timer-fill {
            position: absolute;
            bottom: 0;
            width: 100%;
            background: #eba81d;
            transition: height 1s linear;
            z-index: 1;
        }

        .timer-text {
            position: relative;
            z-index: 2;
            font-weight: 900;
            font-size: 1.25rem;
        }

        .option-button {
            border: 3px solid #000;
            background: #fff;
            box-shadow: 4px 4px 0px 0px #000;
            transition: all 0.1s;
        }

        .option-button:hover:not(.disabled) {
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0px 0px #eba81d;
        }

        .option-button.correct { background: #eba81d !important; color: black !important; }
        .option-button.incorrect { background: #fc5648 !important; color: white !important; }
        .option-button.disabled { pointer-events: none; opacity: 0.8; }

        @keyframes pulse-red {
            0% { background-color: #eba81d; }
            50% { background-color: #fc5648; }
            100% { background-color: #eba81d; }
        }
        .timer-low { animation: pulse-red 0.5s infinite; }
    </style>
</head>

<body class="bg-gray-100 font-['Instrument Sans']">

    <div class="w-full mx-auto md:p-4">
        <x-header />
        <x-navbar />

        <main class="max-w-3xl mx-auto px-3 py-3 md:py-6">
            <div id="trivia-header" class="border-b-3 border-black mb-3 md:mb-4 pb-2 flex flex-col md:flex-row justify-between items-start md:items-end gap-2">
                <div>
                    <h1 class="text-xl md:text-3xl font-black italic uppercase tracking-tighter text-gray-900">EL PIONERO PREGUNTA</h1>
                    <p class="font-bold text-[10px] tracking-widest text-gray-600">TRIVIA PORTEÑA</p>
                </div>
                <div class="text-right self-end md:self-auto">
                    <div class="timer-circle" id="timer-container">
                        <div id="timer-fill" class="timer-fill" style="height: 100%"></div>
                        <span class="timer-text" id="timer-seconds">15</span>
                    </div>
                </div>
            </div>

            <div id="game-screen">
                <div class="mb-3 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                    <div class="flex items-center gap-2">
                        <div id="diff-tag" class="pionero-border px-2 py-1 font-black text-[10px] uppercase tracking-widest bg-white"></div>
                        <div class="pionero-border px-2 py-1 font-black text-[10px] uppercase tracking-widest bg-[#eba81d]">
                            <span id="question-counter">1/10</span>
                        </div>
                    </div>
                    <div class="text-left sm:text-right">
                        <span class="text-[10px] font-bold uppercase block text-gray-500">Puntaje Acumulado</span>
                        <span class="font-black text-lg md:text-xl text-[#fc5648]" id="score-val">0</span>
                    </div>
                </div>

                <div class="game-layout">
                    <div id="question-box" class="bg-white pionero-border p-3 md:p-5 shadow-[3px_3px_0px_0px_#000] md:shadow-[5px_5px_0px_0px_#000]">
                        <h2 id="q-text" class="text-base md:text-xl font-extrabold mb-3 md:mb-5 leading-tight text-gray-900"></h2>
                        <div id="options-grid" class="grid grid-cols-1 gap-2"></div>
                    </div>

                    <div class="vertical-progress-container">
                        <div id="v-progress" class="vertical-progress-fill" style="height: 0%"></div>
                    </div>
                </div>

                <div id="next-area" class="mt-3 md:mt-4 flex flex-col sm:flex-row justify-center items-center gap-2" style="visibility: hidden;">
                    <button onclick="handleNext()" class="bg-black text-white font-black py-2 px-6 md:px-8 uppercase tracking-widest hover:bg-[#fc5648] transition-colors shadow-[3px_3px_0px_0px_#eba81d] text-xs">
                        Siguiente →
                    </button>
                    <button onclick="reportarPreguntaActual()" type="button" class="bg-white text-black font-black py-2 px-6 md:px-8 uppercase tracking-widest border-2 border-black hover:bg-gray-100 transition-colors text-xs">
                        Reportar Pregunta
                    </button>
                </div>
            </div>

            <div id="results-screen" class="hidden bg-white pionero-border p-6 md:p-12 text-center shadow-[8px_8px_0px_0px_#000]">
                <h2 class="text-3xl md:text-5xl font-black mb-4 italic uppercase">¡Trivia Concluida!</h2>
                <div class="text-6xl md:text-8xl mb-6" id="res-icon"></div>
                <div class="bg-[#eba81d] inline-block px-6 md:px-10 py-3 md:py-4 pionero-border mb-6">
                    <p class="text-4xl md:text-6xl font-black text-black" id="final-val">0</p>
                    <p class="font-bold uppercase tracking-widest text-xs">Puntos Totales</p>
                </div>
                <p id="res-msg" class="text-base italic mb-8"></p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <button onclick="location.reload()" class="bg-black text-white font-black py-3 px-8 uppercase border-2 border-black">Jugar de Nuevo</button>
                    <button onclick="toggleCorrectionForm()" class="bg-white text-black font-black py-3 px-8 uppercase border-2 border-black">Reportar Error</button>
                </div>
            </div>
        </main>

 <section id="reporte-form" class="max-w-3xl mx-auto px-3 py-10 hidden">
    <div class="bg-white pionero-border p-6 shadow-[8px_8px_0px_0px_#000]">
        <div class="border-b-2 border-black mb-4 pb-2">
            <h3 class="text-xl font-black uppercase italic text-gray-900">Reportar Pregunta</h3>
            <p class="text-[10px] font-bold text-[#fc5648] uppercase tracking-widest">Ayúdanos a mejorar la trivia</p>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-[#eba81d] pionero-border p-4">
                <p class="font-black text-sm text-black">{{ session('success') }}</p>
            </div>
        @endif

        <form action="{{ route('trivia.reportar') }}" method="POST">
            @csrf
            <input type="hidden" name="pregunta_texto" id="pregunta-hidden">

            <div class="space-y-4">
                <div>
                    <p class="text-xs font-bold mb-2 text-gray-700">Pregunta reportada:</p>
                    <p id="pregunta-display" class="text-sm font-extrabold p-3 bg-gray-100 pionero-border"></p>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase mb-1">¿Cuál es el problema? (Opcional)</label>
                    <textarea name="comentario"
                              rows="3"
                              placeholder="Describe el error que encontraste..."
                              class="w-full pionero-border p-3 font-medium outline-none focus:bg-yellow-50 transition-colors"></textarea>
                </div>

                <div class="pt-2 flex gap-2">
                    <button type="submit" class="flex-1 bg-[#fc5648] text-white font-black py-3 uppercase tracking-widest shadow-[4px_4px_0px_0px_#000] hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all text-xs">
                        Enviar Reporte
                    </button>
                    <button type="button" onclick="cerrarFormularioReporte()" class="bg-white text-black font-black py-3 px-6 uppercase tracking-widest border-2 border-black hover:bg-gray-100 transition-colors text-xs">
                        Cancelar
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

        <x-footer />
    </div>

    <script>
        // Aquí cargarías las 100 preguntas desde tu backend
const allQuestions = [
            // --- TUS 10 ORIGINALES ---
            { d: 'Fácil', q: "¿Cómo se llama el equipo de fútbol más antiguo de Chile, fundado en Valparaíso?", o: ["Everton", "Santiago Wanderers", "Unión Española", "Audax Italiano"], c: 1 },
            { d: 'Fácil', q: "¿Qué cerro es famoso por sus casas de colores y el Paseo Atkinson?", o: ["Cerro Concepción", "Cerro Barón", "Cerro Esperanza", "Cerro Cárcel"], c: 0 },
            { d: 'Fácil', q: "¿Cuál es el nombre de la plaza donde está el Monumento a los Héroes de Iquique?", o: ["Plaza Victoria", "Plaza Echaurren", "Plaza Sotomayor", "Plaza Italia"], c: 2 },
            { d: 'Medio', q: "¿Qué nombre recibe el tradicional Mercado donde se encuentra la mejor gastronomía marina?", o: ["Mercado Cardonal", "Mercado Puerto", "Mercado Central", "Feria de la Avenida"], c: 0 },
            { d: 'Medio', q: "¿En qué cerro se ubica el Parque Cultural de Valparaíso (Ex-Cárcel)?", o: ["Cerro Panteón", "Cerro Cárcel", "Cerro Concepción", "Cerro San Juan de Dios"], c: 1 },
            { d: 'Difícil', q: "¿Qué famoso pirata atacó Valparaíso en el año 1578?", o: ["Henry Morgan", "Francis Drake", "Thomas Cavendish", "Richard Hawkins"], c: 1 },
            { d: 'Difícil', q: "¿Cómo se llamaba el antiguo barrio bohemio de Valparaíso, famoso por su bohemia extrema?", o: ["El Almendral", "La Cuadra", "La Matriz", "Calle Larga"], c: 1 },

            // --- 90 PREGUNTAS NUEVAS ---
            // FÁCILES (25)
            { d: 'Fácil', q: "¿Cuál es el dulce típico de la zona, muy popular en las playas?", o: ["Barquillo", "Cuchuflí", "Palmera", "Manjar blanco"], c: 1 },
            { d: 'Fácil', q: "¿En qué mes se celebra tradicionalmente el 'Año Nuevo en el Mar'?", o: ["Enero", "Diciembre", "Septiembre", "Febrero"], c: 1 },
            { d: 'Fácil', q: "¿Qué animal es el emblema de Santiago Wanderers?", o: ["Águila", "Loro", "Puma", "Zorro"], c: 1 },
            { d: 'Fácil', q: "¿Cómo se llama el lugar de Valparaíso que concentra el comercio?", o: ["El Centro", "El Puerto", "El Almendral", "La Explanada"], c: 2 },
            { d: 'Fácil', q: "¿Cuál es el color predominante de los Trolebuses?", o: ["Azul", "Rojo", "Verde", "Blanco"], c: 2 },
            { d: 'Fácil', q: "¿Qué poeta tiene una casa llamada 'La Sebastiana'?", o: ["Vicente Huidobro", "Nicanor Parra", "Pablo Neruda", "Gabriela Mistral"], c: 2 },
            { d: 'Fácil', q: "¿Cómo se le conoce popularmente a Valparaíso?", o: ["La Perla del Norte", "La Joya del Pacífico", "La Ciudad Jardín", "El Puerto Lindo"], c: 1 },
            { d: 'Fácil', q: "¿Qué transporte usa rieles para subir los cerros?", o: ["Metro", "Ascensor", "Micro", "Tren"], c: 1 },
            { d: 'Fácil', q: "¿Cómo se llama el plato de papas fritas, cebolla, carne y huevo?", o: ["Pichanga", "Chorrillana", "Chacarero", "Bistec a lo pobre"], c: 1 },
            { d: 'Fácil', q: "¿En qué país se encuentra Valparaíso?", o: ["Argentina", "Chile", "Perú", "Uruguay"], c: 1 },
            { d: 'Fácil', q: "¿Qué océano baña las costas de Valparaíso?", o: ["Atlántico", "Índico", "Pacífico", "Antártico"], c: 2 },
            { d: 'Fácil', q: "¿Qué cerro es famoso por su vida nocturna y hostales?", o: ["Alegre", "Placeres", "San Roque", "O'Higgins"], c: 0 },
            { d: 'Fácil', q: "¿Cómo se llama el festival musical más importante de la ciudad vecina?", o: ["Lollapalooza", "Viña del Mar", "Rockodromo", "Mil Tambores"], c: 2 },
            { d: 'Fácil', q: "¿Cuántos cerros se dice tradicionalmente que tiene Valparaíso?", o: ["20", "33", "45", "52"], c: 2 },
            { d: 'Fácil', q: "¿Cómo se llama la plaza central de la ciudad donde está la Intendencia?", o: ["Plaza de Armas", "Plaza Sotomayor", "Plaza Bismarck", "Plaza Italia"], c: 1 },
            { d: 'Fácil', q: "¿Qué se celebra el 21 de mayo en Valparaíso?", o: ["Fiestas Patrias", "Glorias Navales", "Fundación", "Año Nuevo"], c: 1 },
            { d: 'Fácil', q: "¿Cómo se le dice a la gente de Valparaíso?", o: ["Valparaiseños", "Porteños", "Marinos", "Costinos"], c: 1 },
            { d: 'Fácil', q: "¿Qué edificio es conocido por su arquitectura neoclásica frente al puerto?", o: ["Edificio Armada", "Congreso", "Terminal", "Bolsa de Comercio"], c: 0 },
            { d: 'Fácil', q: "¿Cómo se llama el cementerio más antiguo en el cerro?", o: ["Nº 1", "Nº 2", "Nº 3", "Santa Inés"], c: 0 },

            // MEDIO (30)
            { d: 'Medio', q: "¿Cuál es el nombre del diario de habla castellana más antiguo del mundo en funcionamiento?", o: ["La Nación", "El Mercurio", "La Estrella", "La Unión"], c: 1 },
            { d: 'Medio', q: "¿Qué cerro alberga la Universidad Técnica Federico Santa María?", o: ["Placeres", "Esperanza", "Barón", "Alegre"], c: 0 },
            { d: 'Medio', q: "¿Cuál es el nombre del ascensor que llega al Palacio Baburizza?", o: ["El Peral", "Concepción", "Reina Victoria", "Polanco"], c: 0 },
            { d: 'Medio', q: "¿Qué estilo arquitectónico predomina en el Palacio Lyon?", o: ["Barroco", "Gótico", "Neoclásico", "Art Nouveau"], c: 2 },
            { d: 'Medio', q: "¿En qué año fue declarado Valparaíso Patrimonio de la Humanidad?", o: ["2000", "2003", "2005", "2010"], c: 1 },
            { d: 'Medio', q: "¿Cuál es la iglesia más antigua de la ciudad?", o: ["St. Paul", "La Matriz", "Sagrados Corazones", "San Francisco"], c: 1 },
            { d: 'Medio', q: "¿Cuál es el nombre del pasaje con escaleras que parecen teclas de piano?", o: ["Bavestrello", "Beethoven", "Pierre Loty", "Fisher"], c: 1 },
            { d: 'Medio', q: "¿Qué edificio alberga hoy al Museo de Historia Natural?", o: ["Palacio Baburizza", "Palacio Lyon", "Palacio Pascual", "Aduana"], c: 1 },
            { d: 'Medio', q: "¿Cuál es el nombre del ascensor que sube verticalmente por una torre?", o: ["Espíritu Santo", "Polanco", "Artillería", "Villaseca"], c: 1 },
            { d: 'Medio', q: "¿Quién fue el arquitecto del Palacio Baburizza?", o: ["Smith Solar", "Barison y Schiavon", "Lucien Henault", "César da Vinci"], c: 1 },
            { d: 'Medio', q: "¿Cómo se llama la caleta más famosa cerca del plan?", o: ["Portales", "El Membrillo", "Horcón", "Quintay"], c: 0 },
            { d: 'Medio', q: "¿Cuál es el nombre del teatro más antiguo en funcionamiento?", o: ["Municipal", "Imperio", "Condell", "Mauricio"], c: 2 },
            //{ d: 'Medio', q: "¿Qué fuerte defendía la bahía en el Cerro Artillería?", o: ["Bueras", "Esmeralda", "Condell", "Barón"], c: 0 },
            { d: 'Medio', q: "¿Cuál es la plaza donde se instaló el primer mercado de la ciudad?", o: ["Victoria", "Sotomayor", "Echaurren", "Justicia"], c: 2 },
            //{ d: 'Medio', q: "¿Qué famoso bar es conocido como 'La Catedral del Tango'?", o: ["Cinzano", "Bar Inglés", "Liberty", "Pajarito"], c: 0 },
            //{ d: 'Medio', q: "¿Cuál es el nombre del arco británico donado a la ciudad?", o: ["Arco de Triunfo", "Arco Británico", "Arco Victoria", "Arco del Puerto"], c: 1 },
            //{ d: 'Medio', q: "¿Qué cerro tiene el mirador con el nombre de un presidente argentino?", o: ["Bismarck", "Mitre", "Sarmiento", "Roca"], c: 2 },
            { d: 'Medio', q: "¿Cómo se llama el pasaje que atraviesa un edificio en el Cerro Alegre?", o: ["Bavestrello", "Pelayo", "Dimalow", "Atkinson"], c: 0 },
            { d: 'Medio', q: "¿Cuál es el nombre de la biblioteca pública mas importante del plan?", o: ["Santiago Severín", "Nacional", "Porteña", "Mistral"], c: 0 },
            { d: 'Medio', q: "¿En qué cerro está el Museo de Bellas Artes?", o: ["Concepción", "Alegre", "Bellavista", "Cordillera"], c: 1 },
            { d: 'Medio', q: "¿Qué nombre recibe el carnaval de tambores callejeros?", o: ["Mil Tambores", "Carnaval del Sol", "Tamborada", "Murga Porteña"], c: 0 },
            { d: 'Medio', q: "¿Cuál es la playa más popular del sector Playa Ancha?", o: ["Las Torpederas", "San Mateo", "Carvallo", "Amarilla"], c: 0 },
            { d: 'Medio', q: "¿Qué cerro tiene el nombre de un mineral precioso?", o: ["Esmeralda", "Plata", "Diamante", "Oro"], c: 0 },
            { d: 'Medio', q: "¿Cuál es el ascensor que lleva al cerro Florida?", o: ["Florida", "Las Flores", "Florido", "Monjas"], c: 0 },
            { d: 'Medio', q: "¿Cómo se llama la calle principal de Playa Ancha?", o: ["Gran Bretaña", "Altamirano", "Avenida Playa Ancha", "Quebrada Verde"], c: 2 },
            { d: 'Medio', q: "¿Qué cerro lleva el nombre de un libertador chileno?", o: ["Prat", "O'Higgins", "Carrera", "Rodriguez"], c: 1 },
            //{ d: 'Medio', q: "¿Cuál es el nombre de la logia masónica más antigua de Chile en Valparaíso?", o: ["Unión", "L'Etoile", "Progreso", "Fraternidad"], c: 1 },
            { d: 'Medio', q: "¿Qué cerro tiene el mirador 'Marina Mercante'?", o: ["Artillería", "Playa Ancha", "Esperanza", "Barón"], c: 1 },

            // DIFÍCIL (25)
            { d: 'Difícil', q: "¿Quién descubrió Valparaíso en 1536?", o: ["Diego de Almagro", "Juan de Saavedra", "Pedro de Valdivia", "Francisco Pizarro"], c: 1 },
            { d: 'Difícil', q: "¿Qué tragedia ocurrió en 1906 que cambió el rostro de la ciudad?", o: ["Gran Incendio", "Inundación", "Terremoto", "Bombardeo"], c: 2 },
            
            { d: 'Difícil', q: "¿En qué año se inauguró el primer ascensor de Valparaíso?", o: ["1883", "1890", "1901", "1875"], c: 0 },
            { d: 'Difícil', q: "¿Qué almirante británico lideró la Escuadra Nacional desde Valparaíso?", o: ["Nelson", "Lord Cochrane", "Drake", "Cook"], c: 1 },

            //{ d: 'Difícil', q: "¿Cuál es el nombre del primer muelle mecanizado de la ciudad?", o: ["Prat", "Barón", "Muelle Fiscal", "Muelle de Pasajeros"], c: 2 },
            //{ d: 'Difícil', q: "¿Qué cerro lleva el nombre de un santo pero es conocido por su cementerio?", o: ["San Juan", "Panteón", "San Roque", "Santa Inés"], c: 1 },
            { d: 'Difícil', q: "¿En qué año ocurrió el bombardeo español a Valparaíso?", o: ["1866", "1879", "1830", "1891"], c: 0 },
            
            { d: 'Difícil', q: "¿Quién donó los fondos para construir la Universidad Santa María?", o: ["Federico Santa María", "Agustín Edwards Santa María", "Pascual Baburizza", "Pedro Montt"], c: 0 },
            //{ d: 'Difícil', q: "¿Cómo se llama el pasaje que recuerda al autor de 'Pescador de Islandia'?", o: ["Gálvez", "Pierre Loty", "Fisher", "Bavestrello"], c: 1 },
            { d: 'Difícil', q: "¿Cuál es el nombre real del edificio conicido como el Reloj Turri?", o: ["Turri Hermanos Linitada", "Juan Balmaceda Turri", "Agustín Edwards", "Jhon Smith"], c: 2 },
            { d: 'Difícil', q: "¿Cuál es el nombre del fuerte que hoy es el Museo Lord Cochrane?", o: ["Castillo Hidalgo", "Castillo San José", "Fuerte Bueras", "Fuerte Condell"], c: 1 },
            { d: 'Difícil', q: "¿En qué cerro está el ascensor 'Villaseca'?", o: ["Artillería", "Playa Ancha", "Barón", "Santo Domingo"], c: 1 },
            { d: 'Difícil', q: "¿Cuál era la función original del actual Parque Cultural?", o: ["Cuartel militar", "Cárcel pública", "Mercado", "Depósito de pólvora"], c: 3 },
            { d: 'Difícil', q: "¿Cómo se llama la calle que concentró a la colonia británica en el cerro?", o: ["Urriola", "Concepción", "Almirante Montt", "Templeman"], c: 3 },
            //{ d: 'Difícil', q: "¿Cuál es la calle mas angosta y curva del plan?", o: ["Cochrane", "Clave", "Serrano", "Blanco"], c: 1 },
            { d: 'Difícil', q: "¿Qué cerro tiene el Mirador Camogli?", o: ["Placeres", "San Juan de Dios", "Recreo", "Barón"], c: 1 },

            // LEYENDA (10)
            { d: 'Leyenda', q: "¿Cuál es el significado original de la palabra 'Alimapu' (nombre indígena)?", o: ["Tierra quemada", "Canto del mar", "Valle de luz", "Lugar de ballenas"], c: 0 },
            //{ d: 'Leyenda', q: "¿Qué nombre recibía la campana que avisaba los incendios en el siglo XIX?", o: ["La Porteña", "La Matriz", "La Gorda", "La Campana de la Merced"], c: 3 },
            //{ d: 'Leyenda', q: "¿En qué año se fundó la Bolsa de Valores de Valparaíso?", o: ["1890", "1898", "1901", "1875"], c: 1 },
            { d: 'Leyenda', q: "¿Cómo se llama el primer club de remo fundado en el puerto ?", o: ["Club de Regatas Valparaíso", "Valparaíso Rowing", "Marítimo", "Naval"], c: 1 },
            { d: 'Leyenda', q: "¿Qué cerro fue el lugar del primer cementerio protestante (Disidentes)?", o: ["Panteón", "Florida", "Concepción", "San Juan"], c: 0 },
            { d: 'Leyenda', q: "¿Quién fue el ingeniero que instaló el primer sistema de gas en 1856?", o: ["William Wheelwright", "Henry Meiggs", "John Bland", "Peter de Val"], c: 0 },
            //{ d: 'Leyenda', q: "¿Cuál es el nombre del barco hundido cuya caldera es visible en Playa San Mateo?", o: ["Caupolicán", "Don Humberto", "Lafayette", "Aconcagua"], c: 1 },
            //{ d: 'Leyenda', q: "¿Qué emperador brasileño visitó Valparaíso en 1887?", o: ["Pedro I", "Pedro II", "Juan VI", "Maximiliano"], c: 1 },
            //{ d: 'Leyenda', q: "¿Cuál era el nombre del primer muelle de pasajeros construido de madera?", o: ["Muelle Fiscal", "Muelle de la Aduana", "Muelle Prat", "Muelle de los Huevo"], c: 3 }
        ];

        // Lógica de Selección Aleatoria (10 de las 100)
        let triviaSet = allQuestions.sort(() => Math.random() - 0.5).slice(0, 10);
        
        let current = 0;
        let score = 0;
        let timer;
        let timeLeft = 15;

        function startTimer() {
            timeLeft = 15;
            const fill = document.getElementById('timer-fill');
            fill.classList.remove('timer-low');
            fill.style.height = '100%';
            document.getElementById('timer-seconds').textContent = timeLeft;
            
            clearInterval(timer);
            timer = setInterval(() => {
                timeLeft--;
                document.getElementById('timer-seconds').textContent = timeLeft;
                fill.style.height = (timeLeft / 15 * 100) + '%';
                if (timeLeft <= 3) fill.classList.add('timer-low');
                if (timeLeft <= 0) { clearInterval(timer); autoFail(); }
            }, 1000);
        }

        function load() {
            if(current >= triviaSet.length) return end();

            const q = triviaSet[current];
            document.getElementById('q-text').textContent = q.q;
            document.getElementById('diff-tag').textContent = q.d;

            // Actualizar contador de preguntas
            document.getElementById('question-counter').textContent = `${current + 1}/10`;

            // Barra de progreso adaptable
            const progressBar = document.getElementById('v-progress');
            const percent = ((current + 1) / triviaSet.length * 100) + '%';
            if (window.innerWidth <= 768) {
                progressBar.style.width = percent; progressBar.style.height = '100%';
            } else {
                progressBar.style.height = percent; progressBar.style.width = '100%';
            }

            const grid = document.getElementById('options-grid');
            grid.innerHTML = '';
            q.o.forEach((opt, i) => {
                const b = document.createElement('button');
                b.className = "option-button w-full p-3 text-left font-extrabold uppercase italic text-[11px] md:text-xs";
                b.textContent = opt;
                b.onclick = () => select(i, b);
                grid.appendChild(b);
            });

            document.getElementById('next-area').style.visibility = 'hidden';
            startTimer();
        }

        function select(idx, btn) {
            clearInterval(timer);
            const q = triviaSet[current];
            const btns = document.querySelectorAll('.option-button');
            btns.forEach(b => b.classList.add('disabled'));

            if(idx === q.c) {
                btn.classList.add('correct');
                score += (timeLeft * 5);
            } else {
                btn.classList.add('incorrect');
                btns[q.c].classList.add('correct');
            }
            document.getElementById('score-val').textContent = score;
            document.getElementById('next-area').style.visibility = 'visible';
        }

        function autoFail() {
            const btns = document.querySelectorAll('.option-button');
            btns.forEach(b => b.classList.add('disabled'));
            btns[triviaSet[current].c].classList.add('correct');
            document.getElementById('next-area').style.visibility = 'visible';
        }

        function handleNext() { current++; load(); }

        function end() {
            document.getElementById('game-screen').classList.add('hidden');
            document.getElementById('results-screen').classList.remove('hidden');
            document.getElementById('final-val').textContent = score;
            document.getElementById('res-icon').textContent = score > 150 ? '🏆' : '🚢';
            document.getElementById('res-msg').textContent = "¡Edición terminada! Gracias por participar en El Pionero.";
        }

        function toggleCorrectionForm() {
            // Pre-llenamos con la última pregunta vista
            const preguntaActual = triviaSet[current - 1]?.q || "";
            document.getElementById('pregunta-hidden').value = preguntaActual;
            document.getElementById('pregunta-display').textContent = preguntaActual;

            document.getElementById('reporte-form').classList.remove('hidden');
            document.getElementById('reporte-form').scrollIntoView({ behavior: 'smooth' });
        }

        // Esta función se llama cuando alguien pulsa "Reportar Error" durante el juego
        function reportarPreguntaActual() {
            // Tomamos la pregunta que está cargada actualmente en el juego
            const preguntaActual = triviaSet[current].q;

            // La inyectamos en los campos del formulario
            document.getElementById('pregunta-hidden').value = preguntaActual;
            document.getElementById('pregunta-display').textContent = preguntaActual;

            // Mostramos el formulario y hacemos scroll
            document.getElementById('reporte-form').classList.remove('hidden');
            document.getElementById('reporte-form').scrollIntoView({ behavior: 'smooth' });
        }

        function cerrarFormularioReporte() {
            document.getElementById('reporte-form').classList.add('hidden');
            // Volver al título de la trivia
            document.getElementById('trivia-header').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        load();
    </script>
</body>
</html>