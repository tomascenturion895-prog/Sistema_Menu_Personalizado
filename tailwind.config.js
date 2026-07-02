import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            // ============================================================
            // TOKENS DE COLOR — tema Capa8Burger
            // Hamburgueseria + programacion: colores calidos de parrilla
            // combinados con una paleta oscura estilo terminal de codigo.
            // Usar SIEMPRE estos tokens en vez de colores sueltos de Tailwind
            // (ej: bg-brand-500, NO bg-orange-500) para mantener consistencia.
            // ============================================================
            colors: {
                // Color principal de la marca: naranja "a la parrilla".
                // Escala completa 50 (mas claro) a 950 (mas oscuro), igual que las de Tailwind.
                brand: {
                    50: '#fff8ed',
                    100: '#ffefd4',
                    200: '#ffdba8',
                    300: '#ffc170',
                    400: '#ff9c37',
                    500: '#ff7f11', // <- tono base de la marca (botones, links, tabs activas)
                    600: '#f06305',
                    700: '#c74a08',
                    800: '#9e3b0f',
                    900: '#7f3210',
                    950: '#451706',
                },

                // Paleta oscura "terminal": para navbar, footer, panel admin y fondos oscuros.
                // Inspirada en editores de codigo (VS Code dark).
                terminal: {
                    50: '#f4f6f7',
                    100: '#e3e7ea',
                    200: '#cad2d7',
                    300: '#a5b2bb',
                    400: '#798a97',
                    500: '#5e6f7c',
                    600: '#515e69',
                    700: '#464f58',
                    800: '#3e454c',
                    900: '#1e2227', // <- fondo principal oscuro (como un editor)
                    950: '#14171b',
                },

                // Verde "codigo compilado": exitos, confirmaciones y el acento hacker del tema.
                // Tambien es el color de "lechuga" — doble proposito burger/codigo.
                exito: {
                    100: '#dcfce9',
                    500: '#22c55e',
                    700: '#15803d',
                },

                // Colores semanticos para los tipos de dieta (badges y filtros del menu).
                // Nombrados por significado, no por color, para poder cambiarlos sin tocar las vistas.
                dieta: {
                    vegetariano: '#4ade80', // verde claro
                    vegano: '#16a34a',      // verde intenso
                    celiaco: '#facc15',     // amarillo trigo (sin gluten)
                    normal: '#fb923c',      // naranja suave
                },

                // Amarillo cheddar: destacados, precios y promociones.
                cheddar: {
                    100: '#fef9c3',
                    400: '#facc15',
                    500: '#eab308',
                },

                // Rojo tomate: errores, validaciones y acciones destructivas (eliminar).
                tomate: {
                    100: '#fee2e2',
                    500: '#ef4444',
                    700: '#b91c1c',
                },
            },

            // ============================================================
            // TOKENS DE TIPOGRAFIA
            // sans  -> texto general (Figtree, ya venia con Breeze)
            // mono  -> acento "de programador": precios, badges, codigos de pedido
            // ============================================================
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },

            // ============================================================
            // OTROS TOKENS REUTILIZABLES
            // ============================================================

            // Radio de borde estandar para tarjetas y modales del sistema
            borderRadius: {
                card: '0.75rem',
            },

            // Sombra suave unica para todas las tarjetas (productos, tablas, formularios)
            boxShadow: {
                card: '0 1px 3px 0 rgb(0 0 0 / 0.08), 0 1px 2px -1px rgb(0 0 0 / 0.08)',
            },
        },
    },

    plugins: [forms],
};
