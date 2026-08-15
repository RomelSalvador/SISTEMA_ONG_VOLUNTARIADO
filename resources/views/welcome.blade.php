<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'ONG') }} — Voluntariado y Ayuda Social</title>
    <meta name="description" content="Únete a campañas de voluntariado, súmate a nuestra comunidad y sigue el impacto en tiempo real.">

    {{-- ============================================================
         CONTRATO DE DATOS QUE ESPERA ESTA VISTA (pásalos desde el controlador)
         ------------------------------------------------------------
         $totalUsuarios  int                 -> Usuario::count()
         $categorias     Collection          -> CategoriaCampana::withCount('campanas')->where('activo', true)->get()
         $campanas       Collection          -> Campana::withCount('inscripciones')->with(['categoria','organizador.usuario'])
                                                  ->where('estado','activa')->latest('fecha_creacion')->take(6)->get()
                                                  (el withCount agrega $campana->inscripciones_count, usado en el medidor de avance)
         Todos tienen valores por defecto abajo para que la vista no rompa si faltan.
    ============================================================ --}}
    @php
        $totalUsuarios = $totalUsuarios ?? 0;
        $categorias = $categorias ?? collect();
        $campanas = $campanas ?? collect();
    @endphp

    <!-- Fuentes: Fraunces (display, con carácter editorial/botánico), Work Sans (texto), Space Mono (cifras y sellos) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=fraunces:500,600,600i,700i|work-sans:400,500,600,700|space-mono:400,700&display=swap" rel="stylesheet" />

    <!-- Tailwind autocontenido (no depende de tu build/Vite), con TU paleta inyectada -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Work Sans', 'sans-serif'],
                        display: ['Fraunces', 'serif'],
                        mono: ['Space Mono', 'monospace'],
                    },
                    colors: {
                        primary: '#1E73BE',
                        secondary: '#7CB342',
                        dark: '#222222',
                        gray: '#666666',
                        light: '#F5F5F5',
                        white: '#FFFFFF',
                        'primary-light': '#E8F3FF',
                        'green-light': '#EAF5D5',
                    },
                    keyframes: {
                        'fade-up': { '0%':{opacity:'0',transform:'translateY(20px)'}, '100%':{opacity:'1',transform:'translateY(0)'} },
                        drift: { '0%,100%':{transform:'translateX(0)'}, '50%':{transform:'translateX(-14px)'} },
                    },
                    animation: {
                        'fade-up': 'fade-up 0.7s ease-out both',
                        drift: 'drift 9s ease-in-out infinite',
                    },
                },
            },
        }
    </script>

    <style>
        .no-scrollbar::-webkit-scrollbar{ display:none; }
        .no-scrollbar{ -ms-overflow-style:none; scrollbar-width:none; }
        .reveal{ opacity:0; transform:translateY(16px); transition:opacity .6s ease, transform .6s ease; }
        .reveal.is-visible{ opacity:1; transform:translateY(0); }
        @media (prefers-reduced-motion: reduce){
            .reveal{ opacity:1; transform:none; transition:none; }
            .animate-drift{ animation:none; }
        }
        /* Sello: la insignia circular punteada que marca cada dato "verificado" del recorrido */
        .sello{
            border: 1.5px dashed currentColor;
            border-radius: 9999px;
        }
        /* Línea de ruta punteada que conecta los pasos de "Cómo funciona" */
        .ruta-linea{
            background-image: linear-gradient(to right, currentColor 45%, transparent 0%);
            background-position: bottom;
            background-size: 14px 2px;
            background-repeat: repeat-x;
        }
        .ficha-foto{
            box-shadow: 0 1px 0 #fff, 0 2px 0 #fff, 0 14px 30px -12px rgba(34,34,34,0.35);
        }
    </style>
</head>
<body class="antialiased font-sans bg-light text-dark">

    <!-- ============================================================
         NAVBAR — sobre papel, no sobre cristal oscuro
    ============================================================= -->
    <header class="sticky top-0 inset-x-0 z-50 bg-light/95 backdrop-blur-sm border-b border-dark/10">
        <nav class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                <span class="sello flex h-9 w-9 items-center justify-center text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4.5 w-4.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-4.5-2.5-8-6-8-10.5A5.5 5.5 0 0112 5a5.5 5.5 0 018 5.5c0 4.5-3.5 8-8 10.5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 12v5" />
                    </svg>
                </span>
                <span class="font-display font-semibold text-dark text-lg tracking-tight">ONG</span>
            </a>

            <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray">
                <a href="#campanas" class="hover:text-dark transition-colors">Campañas</a>
                <a href="#categorias" class="hover:text-dark transition-colors">Categorías</a>
                <a href="#impacto" class="hover:text-dark transition-colors">Impacto</a>
            </div>

            <div class="flex items-center gap-3 text-sm font-semibold">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/home') }}" class="border border-dark/25 text-dark hover:border-dark hover:bg-dark/5 transition-all px-4 py-2 rounded-full">Mi panel</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray hover:text-dark transition-colors px-2">Iniciar sesión</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-secondary hover:brightness-95 text-white transition-colors px-4 py-2 rounded-full">Quiero ayudar</a>
                        @endif
                    @endauth
                @endif
            </div>
        </nav>
    </header>

    <!-- ============================================================
         HERO — bitácora de campo: titular editorial + sello de conteo en vivo
    ============================================================= -->
    <section class="relative overflow-hidden pt-16 pb-24 px-6">
        <!-- Textura de curvas de nivel (mapa topográfico), muy tenue, propia del trabajo de campo -->
        <svg class="absolute inset-x-0 top-0 w-full h-[520px] text-secondary/[0.08] pointer-events-none" viewBox="0 0 1400 520" preserveAspectRatio="none" fill="none">
            <path d="M-50 80 Q 300 20 600 90 T 1450 60" stroke="currentColor" stroke-width="2"/>
            <path d="M-50 160 Q 320 90 650 170 T 1450 150" stroke="currentColor" stroke-width="2"/>
            <path d="M-50 240 Q 340 170 700 250 T 1450 230" stroke="currentColor" stroke-width="2"/>
            <path d="M-50 320 Q 360 250 720 320 T 1450 310" stroke="currentColor" stroke-width="2"/>
            <path d="M-50 400 Q 380 330 740 400 T 1450 390" stroke="currentColor" stroke-width="2"/>
        </svg>

        <div class="max-w-7xl mx-auto grid lg:grid-cols-[1.1fr_0.9fr] gap-16 items-center relative">

            <div class="reveal animate-fade-up" data-reveal>
                <p class="font-mono text-xs tracking-[0.15em] text-secondary uppercase mb-5"></p>

                <h1 class="font-display text-dark text-5xl sm:text-6xl font-semibold leading-[1.05] tracking-tight">
                    <span id="phrase-rotator">Cada hora que das, <em class="italic text-primary">cambia</em> una comunidad.</span>
                </h1>

                <p class="mt-6 text-gray text-lg leading-relaxed max-w-lg">
                    Únete a campañas activas, elige la causa que te mueve y súmate a una red de voluntarios que ya está transformando su entorno.
                </p>

                <div class="mt-9 flex flex-wrap items-center gap-4">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="bg-secondary hover:brightness-95 transition-colors text-white px-6 py-3.5 rounded-full font-semibold">Regístrate como voluntario</a>
                    @endif
                    <a href="#campanas" class="text-dark font-semibold px-2 py-3.5 border-b-2 border-dark/20 hover:border-secondary transition-colors">Ver campañas activas →</a>
                </div>

                <!-- Sello de conteo: el elemento distintivo de la página, como el timbre de un registro de campo -->
                <div class="mt-12 flex items-center gap-5">
                    <div class="sello -rotate-6 h-20 w-20 shrink-0 flex flex-col items-center justify-center text-primary">
                        <span id="hero-user-count" class="font-mono font-bold text-xl leading-none" data-count="{{ $totalUsuarios }}">0</span>
                        <span class="font-mono text-[8px] tracking-wide uppercase mt-1">voluntarios</span>
                    </div>
                    <p class="text-sm text-gray max-w-[15rem]">Ya son parte de la comunidad y suman horas certificadas cada semana.</p>
                </div>
            </div>

            <!-- Fotografía única, tipo ficha de campo pegada al margen -->
            <div class="relative hidden lg:block reveal" data-reveal>
                <div class="ficha-foto bg-white p-3 pb-8 -rotate-2 max-w-md ml-auto">
                    <img src="https://picsum.photos/seed/mv-hero/640/520" alt="Voluntarios trabajando en campaña" class="w-full h-[380px] object-cover">
                    <p class="font-mono text-xs text-gray mt-3 text-center">Jornada de reforestación · registro fotográfico</p>
                </div>
                <div class="sello absolute -top-6 -left-6 h-24 w-24 bg-light flex flex-col items-center justify-center text-secondary rotate-6 animate-drift">
                    <span class="font-mono font-bold text-xl leading-none" id="hero-chip-count" data-count="{{ $campanas->count() }}">0</span>
                    <span class="font-mono text-[8px] tracking-wide uppercase mt-1 text-center leading-tight">campañas<br>activas</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
         FRANJA DE ESTADÍSTICAS — libro de registro, no tarjetas de vidrio
    ============================================================= -->
    <section id="impacto" class="px-6 border-y border-dark/10 bg-white">
        <div class="max-w-7xl mx-auto grid sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-dark/10 reveal" data-reveal>

            <div class="py-10 sm:px-10 first:pl-0">
                <div class="flex items-center gap-2 mb-3">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                    </span>
                    <p class="font-mono text-xs tracking-wide text-gray uppercase">Voluntarios registrados</p>
                </div>
                <p class="font-display text-5xl font-semibold text-dark" id="stat-usuarios" data-count="{{ $totalUsuarios }}">0</p>
            </div>

            <div class="py-10 sm:px-10">
                <div class="flex items-center gap-2 mb-3">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-secondary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-secondary"></span>
                    </span>
                    <p class="font-mono text-xs tracking-wide text-gray uppercase">Campañas activas</p>
                </div>
                <p class="font-display text-5xl font-semibold text-secondary" id="stat-campanas" data-count="{{ $campanas->count() }}">0</p>
            </div>

            <div class="py-10 sm:px-10 sm:pr-0">
                <div class="flex items-center gap-2 mb-3">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                    </span>
                    <p class="font-mono text-xs tracking-wide text-gray uppercase">Categorías de ayuda</p>
                </div>
                <p class="font-display text-5xl font-semibold text-dark" id="stat-categorias" data-count="{{ $categorias->count() }}">0</p>
            </div>

        </div>
    </section>

    <!-- ============================================================
         CÓMO FUNCIONA — la ruta: tres paradas sobre una línea de sendero
    ============================================================= -->
    <section class="px-6 py-24">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16 reveal" data-reveal>
                <p class="font-mono text-xs tracking-[0.15em] text-primary uppercase">La ruta</p>
                <h2 class="font-display text-4xl font-semibold mt-2 text-dark">Cómo funciona</h2>
                <p class="text-gray mt-3 max-w-lg mx-auto">Tres paradas simples entre inscribirte y dejar huella real en tu comunidad.</p>
            </div>

            <div class="relative grid sm:grid-cols-3 gap-10 reveal" data-reveal>
                <div class="hidden sm:block absolute top-7 left-[16.5%] right-[16.5%] h-0 ruta-linea text-dark/25"></div>

                <div class="relative text-center">
                    <span class="sello relative z-10 mx-auto h-14 w-14 flex items-center justify-center bg-light font-mono font-semibold text-primary mb-5">01</span>
                    <h3 class="font-display font-semibold text-lg text-dark mb-2">Explora causas</h3>
                    <p class="text-sm text-gray leading-relaxed max-w-xs mx-auto">Revisa las campañas activas y elige la categoría que más se alinee con lo que quieres aportar.</p>
                </div>
                <div class="relative text-center">
                    <span class="sello relative z-10 mx-auto h-14 w-14 flex items-center justify-center bg-light font-mono font-semibold text-secondary mb-5">02</span>
                    <h3 class="font-display font-semibold text-lg text-dark mb-2">Inscríbete</h3>
                    <p class="text-sm text-gray leading-relaxed max-w-xs mx-auto">Confirma tu disponibilidad y recibe la información del punto de encuentro.</p>
                </div>
                <div class="relative text-center">
                    <span class="sello relative z-10 mx-auto h-14 w-14 flex items-center justify-center bg-light font-mono font-semibold text-primary mb-5">03</span>
                    <h3 class="font-display font-semibold text-lg text-dark mb-2">Genera impacto</h3>
                    <p class="text-sm text-gray leading-relaxed max-w-xs mx-auto">Participa, acumula horas certificadas y sigue tu contribución en tu perfil.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
         CATEGORÍAS — etiquetas tipo ficha, sin degradados
    ============================================================= -->
    <section id="categorias" class="px-6 py-16 bg-white border-y border-dark/10">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-end justify-between mb-8 reveal" data-reveal>
                <div>
                    <p class="font-mono text-xs tracking-[0.15em] text-secondary uppercase">Causas</p>
                    <h2 class="font-display text-3xl font-semibold mt-1 text-dark">Categorías de la ONG</h2>
                </div>
            </div>

            <div id="categorias-list" class="flex gap-3 overflow-x-auto no-scrollbar pb-2 reveal" data-reveal>
                @forelse($categorias as $categoria)
                    <button
                        class="shrink-0 flex items-center gap-2 px-4 py-2.5 rounded-full text-sm font-medium border border-dashed border-secondary/50 text-secondary hover:bg-green-light transition-all"
                        data-categoria-id="{{ $categoria->id_categoria }}"
                    >
                        <span>{{ $categoria->icono ?? '🌱' }}</span>
                        <span>{{ $categoria->nombre }}</span>
                        <span class="font-mono text-xs opacity-70">{{ $categoria->campanas_count ?? 0 }}</span>
                    </button>
                @empty
                    <p class="text-gray text-sm">Aún no hay categorías registradas.</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- ============================================================
         CAMPAÑAS RECIENTES — fichas de campo con sello de avance
    ============================================================= -->
    <section id="campanas" class="px-6 py-20">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-end justify-between mb-8 reveal" data-reveal>
                <div>
                    <p class="font-mono text-xs tracking-[0.15em] text-primary uppercase">Actualidad</p>
                    <h2 class="font-display text-3xl font-semibold mt-1 text-dark">Campañas recientes</h2>
                </div>
                <a href="{{ url('/campanas') }}" class="text-sm font-semibold text-primary hover:underline">Ver todas →</a>
            </div>

            <div id="campanas-grid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-7">
                @forelse($campanas as $campana)
                    <a href="{{ url('/campanas/'.$campana->id_campana) }}" class="group bg-white border border-dark/10 overflow-hidden transition-all hover:-translate-y-1 hover:shadow-xl reveal" data-reveal>
                        <div class="h-44 w-full relative">
                            <img src="{{ $campana->imagen_banner ?? 'https://picsum.photos/seed/mv-camp'.$campana->id_campana.'/500/300' }}" alt="{{ $campana->nombre }}" class="w-full h-full object-cover">
                            @if($campana->categoria)
                                <span class="absolute top-3 left-3 text-xs font-mono font-semibold px-2.5 py-1 text-white bg-dark/80">
                                    {{ $campana->categoria->nombre }}
                                </span>
                            @endif

                            @php
                                $meta = $campana->meta_voluntarios ?? 0;
                                $inscritos = $campana->inscripciones_count ?? 0;
                                $porcentaje = $meta > 0 ? min(100, round(($inscritos / $meta) * 100)) : 0;
                            @endphp
                            @if($meta > 0)
                                <span class="sello absolute -bottom-5 right-4 h-14 w-14 bg-white border-secondary flex items-center justify-center font-mono text-xs font-bold text-secondary shadow-md">
                                    {{ $porcentaje }}%
                                </span>
                            @endif
                        </div>
                        <div class="p-5 pt-7">
                            <h3 class="font-display font-semibold text-lg leading-snug text-dark">{{ $campana->nombre }}</h3>
                            <p class="text-sm text-gray mt-1.5 line-clamp-2">{{ $campana->descripcion }}</p>

                            @if($meta > 0)
                                <p class="text-xs text-gray mt-3 font-mono">{{ $inscritos }} de {{ $meta }} voluntarios inscritos</p>
                            @endif

                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-dark/10 text-xs text-gray">
                                <span>📍 {{ $campana->lugar }}</span>
                                <span class="font-mono">{{ \Carbon\Carbon::parse($campana->fecha_inicio)->format('d M') }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="text-gray text-sm col-span-full">Todavía no hay campañas publicadas. ¡Vuelve pronto!</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- ============================================================
         TESTIMONIO — voz humana sobre papel, sin vidrio
    ============================================================= -->
    <section class="px-6 pb-20">
        <div class="max-w-4xl mx-auto reveal" data-reveal>
            <div class="bg-white border border-dark/10 p-8 sm:p-10 flex flex-col sm:flex-row items-center gap-8">
                <div class="sello h-24 w-24 shrink-0 p-1 text-secondary">
                    <img src="https://picsum.photos/seed/mv-testimonial/160" alt="Voluntaria de la ONG" class="h-full w-full rounded-full object-cover">
                </div>
                <div>
                    <p class="font-display italic text-6xl leading-none text-secondary/40 mb-1">&ldquo;</p>
                    <p class="text-dark text-lg leading-relaxed font-display -mt-4">
                        Empecé apoyando una sola jornada de reforestación y hoy ya llevo más de 40 horas certificadas. Ver el contador de impacto crecer en tiempo real me motiva a seguir sumando.
                    </p>
                    <p class="text-sm text-gray mt-4 font-mono">
                        Valeria Ríos · voluntaria desde 2024
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
         CTA FINAL — banda verde a todo lo ancho, sin degradado
    ============================================================= -->
    <section class="px-6 pb-24">
        <div class="relative overflow-hidden max-w-7xl mx-auto px-8 py-16 text-center reveal bg-secondary" data-reveal>
            <svg class="absolute inset-0 w-full h-full text-white/10 pointer-events-none" viewBox="0 0 1400 300" preserveAspectRatio="none" fill="none">
                <path d="M-50 40 Q 300 -10 600 50 T 1450 30" stroke="currentColor" stroke-width="2"/>
                <path d="M-50 120 Q 320 70 650 130 T 1450 110" stroke="currentColor" stroke-width="2"/>
                <path d="M-50 200 Q 340 150 700 210 T 1450 190" stroke="currentColor" stroke-width="2"/>
            </svg>
            <div class="relative">
                <h2 class="font-display text-white text-4xl sm:text-5xl font-semibold max-w-2xl mx-auto leading-tight">
                    Tu tiempo puede ser el próximo dato en vivo de esta página.
                </h2>
                <p class="text-white/85 mt-4 max-w-xl mx-auto">Regístrate en menos de dos minutos y elige la campaña que más te represente.</p>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="inline-block mt-8 px-7 py-3.5 rounded-full font-semibold bg-white text-secondary hover:brightness-95 transition-colors">Unirme ahora</a>
                @endif
            </div>
        </div>
    </section>

    <!-- ============================================================
         FOOTER
    ============================================================= -->
    <footer class="px-6 pt-16 pb-10 bg-dark">
        <div class="max-w-7xl mx-auto">
            <div class="grid sm:grid-cols-3 gap-8 text-white/70 text-sm pb-12 border-b border-white/10">
                <div>
                    <p class="font-display text-white font-semibold mb-3 text-lg">ONG</p>
                    <p class="leading-relaxed">Conectamos voluntarios con campañas de ayuda social y ambiental, y mostramos el impacto en tiempo real.</p>
                </div>
                <div>
                    <p class="text-white font-medium mb-3">Explorar</p>
                    <ul class="space-y-2">
                        <li><a href="#campanas" class="hover:text-white">Campañas</a></li>
                        <li><a href="#categorias" class="hover:text-white">Categorías</a></li>
                        <li><a href="#impacto" class="hover:text-white">Impacto en vivo</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-white font-medium mb-3">Cuenta</p>
                    <ul class="space-y-2">
                        @if (Route::has('login'))
                            <li><a href="{{ route('login') }}" class="hover:text-white">Iniciar sesión</a></li>
                        @endif
                        @if (Route::has('register'))
                            <li><a href="{{ route('register') }}" class="hover:text-white">Registrarme</a></li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Sello grande, parte inferior -->
            <div class="flex flex-col items-center pt-10">
                <span class="sello flex h-14 w-14 items-center justify-center mb-3 text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="h-7 w-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-4.5-2.5-8-6-8-10.5A5.5 5.5 0 0112 5a5.5 5.5 0 018 5.5c0 4.5-3.5 8-8 10.5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 12v5" />
                    </svg>
                </span>
                <p class="font-display text-white text-xl font-semibold tracking-tight">ONG</p>
                <p class="text-white/50 text-sm mt-2 max-w-sm text-center">Organización sin fines de lucro. Todas las horas y campañas mostradas son verificables dentro de la plataforma.</p>

                <div class="flex items-center gap-3 mt-5">
                    <a href="#" aria-label="Facebook" class="h-9 w-9 rounded-full border border-white/15 flex items-center justify-center text-white/60 hover:text-white hover:border-white/30 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06c0 5 3.66 9.15 8.44 9.94v-7.03H7.9v-2.91h2.54V9.85c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56v1.88h2.78l-.45 2.91h-2.33V22c4.78-.79 8.44-4.94 8.44-9.94z"/></svg>
                    </a>
                    <a href="#" aria-label="Instagram" class="h-9 w-9 rounded-full border border-white/15 flex items-center justify-center text-white/60 hover:text-white hover:border-white/30 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="h-4 w-4"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="3.7"/><circle cx="17.2" cy="6.8" r="1"/></svg>
                    </a>
                    <a href="#" aria-label="LinkedIn" class="h-9 w-9 rounded-full border border-white/15 flex items-center justify-center text-white/60 hover:text-white hover:border-white/30 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M4.98 3.5C4.98 4.88 3.87 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1s2.48 1.12 2.48 2.5zM.5 8h4V23h-4V8zm7.5 0h3.8v2.05h.05c.53-1 1.83-2.05 3.77-2.05C19.6 8 21 10.3 21 14.2V23h-4v-7.9c0-1.9-.03-4.3-2.6-4.3-2.6 0-3 2-3 4.1V23H7.9V8z"/></svg>
                    </a>
                </div>

                <p class="text-white/40 text-xs mt-6 font-mono">© {{ date('Y') }} · v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})</p>
            </div>
        </div>
    </footer>

    <!-- ============================================================
         JS — frases rotativas, conteo animado y actualización en vivo
         (sin cambios de comportamiento respecto a la versión anterior)
    ============================================================= -->
    <script>
        // ---- 1. Frases rotativas del hero ----
        const frases = [
            "Cada hora que das, cambia una comunidad.",
            "Ayudar también se mide: y hoy suma más que ayer.",
            "Voluntarios reales, impacto que puedes ver en vivo.",
            "Elige tu causa. La comunidad hace el resto.",
        ];
        let fraseIdx = 0;
        const fraseEl = document.getElementById('phrase-rotator');
        if (fraseEl && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            fraseEl.style.transition = 'opacity .3s ease';
            setInterval(() => {
                fraseIdx = (fraseIdx + 1) % frases.length;
                fraseEl.style.opacity = 0;
                setTimeout(() => {
                    fraseEl.textContent = frases[fraseIdx];
                    fraseEl.style.opacity = 1;
                }, 300);
            }, 5000);
        }

        // ---- 2. Conteo animado (cuenta hacia arriba al cargar) ----
        function animateCount(el, to, duration = 1200) {
            if (!el) return;
            const from = 0;
            const start = performance.now();
            function step(now) {
                const progress = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = Math.round(from + (to - from) * eased).toLocaleString('es-PE');
                if (progress < 1) requestAnimationFrame(step);
            }
            requestAnimationFrame(step);
        }

        function animateAllCounters() {
            document.querySelectorAll('[data-count]').forEach(el => {
                animateCount(el, parseInt(el.dataset.count || '0', 10));
            });
        }
        document.addEventListener('DOMContentLoaded', animateAllCounters);

        // ---- 3. Reveal on scroll ----
        const revealEls = document.querySelectorAll('[data-reveal]');
        const io = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });
        revealEls.forEach(el => io.observe(el));

        // ---- 4. Actualización "en tiempo real" por polling ----
        // Requiere una ruta tipo: Route::get('/api/live-stats', [StatsController::class,'live'])->name('api.live-stats');
        // que devuelva JSON: { total_usuarios, campanas_activas, categorias_activas }
        const LIVE_STATS_URL = "{{ Route::has('api.live-stats') ? route('api.live-stats') : '' }}";
        const POLL_INTERVAL_MS = 20000;

        function refreshLiveStats() {
            if (!LIVE_STATS_URL) return; // la ruta aún no existe: no falla, simplemente no actualiza
            fetch(LIVE_STATS_URL, { headers: { 'Accept': 'application/json' } })
                .then(res => res.ok ? res.json() : Promise.reject(res.status))
                .then(data => {
                    const map = {
                        'stat-usuarios': data.total_usuarios,
                        'hero-user-count': data.total_usuarios,
                        'stat-campanas': data.campanas_activas,
                        'hero-chip-count': data.campanas_activas,
                        'stat-categorias': data.categorias_activas,
                    };
                    Object.entries(map).forEach(([id, value]) => {
                        if (value === undefined) return;
                        const el = document.getElementById(id);
                        if (!el) return;
                        const current = parseInt(el.dataset.count || '0', 10);
                        if (current !== value) {
                            el.dataset.count = value;
                            animateCount(el, value, 800);
                        }
                    });
                })
                .catch(() => { /* silencioso: se reintenta en el siguiente ciclo */ });
        }
        setInterval(refreshLiveStats, POLL_INTERVAL_MS);
    </script>
</body>
</html>