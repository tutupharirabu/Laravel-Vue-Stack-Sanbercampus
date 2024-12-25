/** @type {import('tailwindcss').Config} */
import daisyui from 'daisyui';

export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Plus Jakarta Sans', 'sans-serif'],
      },
    },
  },
  plugins: [
    daisyui,
  ],
  daisyui: {
    themes: [
      {
        mytheme: {
          "primary": "#5E59FF",           // Main Primary color
          "primary-hover": "#7975FF",    // Hover state
          "primary-pressed": "#001940",  // Pressed state
          "primary-surface": "#E0E6F2",  // Surface color
          "primary-border": "#002B63",   // Border color

          "neutral": "#FFFFFF",          // Base neutral color (10 in Neutral palette)
          "neutral-20": "#F4F5F6",       // Slightly darker neutral
          "neutral-30": "#E5E7EB",
          "neutral-40": "#D3D6DA",
          "neutral-50": "#A0A8B0",
          "neutral-60": "#707784",
          "neutral-70": "#515966",
          "neutral-80": "#3B4453",
          "neutral-90": "#232B39",
          "neutral-100": "#101623",      // Darkest neutral

          "error": "#FF5630",            // Main Error color
          "error-hover": "#D44828",      // Hover state
          "error-pressed": "#AA3920",    // Pressed state
          "error-surface": "#FFDDD6",    // Surface color
          "error-border": "#FF7252",     // Border color

          "warning": "#FFAB00",          // Main Warning color
          "warning-hover": "#D48E00",    // Hover state
          "warning-pressed": "#AA7200",  // Pressed state
          "warning-surface": "#FFEECC",  // Surface color
          "warning-border": "#FFB92A",   // Border color

          "success": "#36B37E",          // Main Success color
          "success-hover": "#2D9569",    // Hover state
          "success-pressed": "#247754",  // Pressed state
          "success-surface": "#D7F0E5",  // Surface color
          "success-border": "#57C093",   // Border color

          "info": "#0065FF",             // Main Info color
          "info-hover": "#0054D4",       // Hover state
          "info-pressed": "#0043AA",     // Pressed state
          "info-surface": "#CCE0FF",     // Surface color
          "info-border": "#2A7FFF",      // Border color

          "base-100": "#FFFFFF",         // Background base
          "base-200": "#F4F5F6",         // Slightly darker background
          "base-300": "#E5E7EB",         // Even darker background
          "base-content": "#101623",     // Text/content color
        },
      },
    ],
  }
}

