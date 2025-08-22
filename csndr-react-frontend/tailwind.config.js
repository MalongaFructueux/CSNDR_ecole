module.exports = {
  content: [
    "./src/**/*.{js,jsx,ts,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        // Charte graphique du Centre Scolaire Notre Dame du Rosaire
        'primary': {
          '50': '#f0f9ff',
          '100': '#e0f2fe',
          '200': '#bae6fd',
          '300': '#7dd3fc',
          '400': '#38bdf8',
          '500': '#0ea5e9',
          '600': '#0284c7',
          '700': '#0369a1',
          '800': '#075985',
          '900': '#0c4a6e',
        },
        'secondary': {
          '50': '#f8fafc',
          '100': '#f1f5f9',
          '200': '#e2e8f0',
          '300': '#cbd5e1',
          '400': '#94a3b8',
          '500': '#64748b',
          '600': '#475569',
          '700': '#334155',
          '800': '#1e293b',
          '900': '#0f172a',
        },
        'accent': {
          'blue': '#1e40af',
          'light-blue': '#3b82f6',
          'navy': '#1e3a8a',
        },
        'role': {
          'admin': '#dc2626',      // Rouge pour admin
          'professeur': '#059669',  // Vert pour professeur
          'parent': '#2563eb',      // Bleu pour parent
          'eleve': '#7c3aed',       // Violet pour élève
        }
      },
      fontFamily: {
        'sans': ['Inter', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
