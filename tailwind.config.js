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
            colors: {
                brand: {
                    primary: '#2563EB',
                    'primary-hover': '#1D4ED8',
                    secondary: '#F59E0B',
                    success: '#10B981',
                    warning: '#F97316',
                    danger: '#EF4444',
                    bg: '#F8FAFC',
                    surface: '#FFFFFF',
                    border: '#E5E7EB',
                    text: '#1F2937',
                    muted: '#6B7280',
                    sidebar: '#1E293B',
                    'sidebar-text': '#E5E7EB',
                },
            },
            fontFamily: {
                sans: ['Manrope', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                panel: '0 18px 40px rgba(15, 23, 42, 0.08)',
                soft: '0 10px 24px rgba(37, 99, 235, 0.08)',
            },
        },
    },

    plugins: [forms],
};