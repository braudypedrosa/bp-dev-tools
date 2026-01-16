/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./src/vue/**/*.{vue,js,ts,jsx,tsx}",
    "./includes/**/*.php",
  ],
  theme: {
    extend: {
      colors: {
        // Brand colors - Purple/Indigo gradient theme
        primary: {
          start: '#667eea',
          end: '#764ba2',
          DEFAULT: '#667eea',
        },
        accent: '#4f46e5',
      },
      fontFamily: {
        sans: ['-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Oxygen-Sans', 'Ubuntu', 'Cantarell', 'Helvetica Neue', 'sans-serif'],
      },
      animation: {
        'slide-in-right': 'slideInRight 0.3s ease-out',
        'slide-out-right': 'slideOutRight 0.3s ease-in forwards',
        'fade-in': 'fadeIn 0.3s ease-in-out',
      },
      keyframes: {
        slideInRight: {
          'from': { transform: 'translateX(100%)', opacity: '0' },
          'to': { transform: 'translateX(0)', opacity: '1' },
        },
        slideOutRight: {
          'from': { transform: 'translateX(0)', opacity: '1' },
          'to': { transform: 'translateX(100%)', opacity: '0' },
        },
        fadeIn: {
          'from': { opacity: '0' },
          'to': { opacity: '1' },
        },
      },
    },
  },
  plugins: [],
}
