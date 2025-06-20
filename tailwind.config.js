/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/**/*.jsx",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
        colors: {
            primary: '#235789',
            white: '#fdfffc',
            dark: '#020100',
            success: '#3cde59',
            error: '#ed1c24',
        },
        fontFamily:{
            'noto': ["Noto Sans", 'sans-serif']
        }
    },
  },
  plugins: [],
}
