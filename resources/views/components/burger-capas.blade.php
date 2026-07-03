{{-- Ilustracion insignia de la marca: la hamburguesa de 8 capas dibujada en SVG plano.
     Cada capa es un <g> separado; los colores salen de la paleta de tokens.
     Se usa en la landing y puede reusarse en cualquier vista con <x-burger-capas /> --}}
<svg {{ $attributes->merge(['class' => 'w-full h-auto']) }} viewBox="0 0 320 300" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Hamburguesa de 8 capas de Capa8Burger">
    {{-- Capa 1: pan superior con semillas --}}
    <g>
        <path d="M40 90 C40 45 105 25 160 25 C215 25 280 45 280 90 L280 100 C280 106 275 110 269 110 L51 110 C45 110 40 106 40 100 Z" fill="#F2A65A"/>
        <ellipse cx="110" cy="60" rx="7" ry="4" fill="#FFF3E0" transform="rotate(-15 110 60)"/>
        <ellipse cx="160" cy="48" rx="7" ry="4" fill="#FFF3E0"/>
        <ellipse cx="210" cy="60" rx="7" ry="4" fill="#FFF3E0" transform="rotate(15 210 60)"/>
        <ellipse cx="135" cy="80" rx="7" ry="4" fill="#FFF3E0" transform="rotate(8 135 80)"/>
        <ellipse cx="188" cy="82" rx="7" ry="4" fill="#FFF3E0" transform="rotate(-10 188 82)"/>
    </g>

    {{-- Capa 2: salsa que chorrea --}}
    <path d="M48 110 L272 110 L272 118 C272 118 260 130 248 118 C236 130 224 118 224 118 C212 130 200 118 200 118 C188 130 176 118 176 118 C164 130 152 118 152 118 C140 130 128 118 128 118 C116 130 104 118 104 118 C92 130 80 118 80 118 C68 130 48 118 48 118 Z" fill="#eab308"/>

    {{-- Capa 3: tomate --}}
    <rect x="52" y="126" width="216" height="14" rx="7" fill="#ef4444"/>

    {{-- Capa 4: lechuga ondulada --}}
    <path d="M44 152 C52 142 60 162 68 152 C76 142 84 162 92 152 C100 142 108 162 116 152 C124 142 132 162 140 152 C148 142 156 162 164 152 C172 142 180 162 188 152 C196 142 204 162 212 152 C220 142 228 162 236 152 C244 142 252 162 260 152 C268 142 276 152 276 152 L276 160 L44 160 Z" fill="#4ade80"/>

    {{-- Capa 5: queso cheddar derretido --}}
    <path d="M48 166 L272 166 L272 172 L252 186 L232 172 L212 186 L192 172 L172 186 L152 172 L132 186 L112 172 L92 186 L72 172 L48 172 Z" fill="#facc15"/>

    {{-- Capa 6: medallon de carne --}}
    <rect x="44" y="178" width="232" height="26" rx="13" fill="#7f3210"/>

    {{-- Capa 7: cebolla morada (anillos) --}}
    <g stroke="#c084fc" stroke-width="4" fill="none">
        <path d="M70 216 C80 208 96 208 106 216"/>
        <path d="M130 216 C140 208 156 208 166 216"/>
        <path d="M190 216 C200 208 216 208 226 216"/>
    </g>

    {{-- Capa 8: pan inferior --}}
    <path d="M44 226 L276 226 C282 226 286 230 286 236 L286 248 C286 262 274 272 260 272 L60 272 C46 272 34 262 34 248 L34 236 C34 230 38 226 44 226 Z" fill="#F2A65A"/>
</svg>
