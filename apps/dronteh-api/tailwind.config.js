module.exports = {
  mode: "jit",
  content: [
    "./src/**/*.php",
    "./templates/**/*.{html,twig}",
    "./assets/js/*.{js,ts}",
    "./node_modules/flowbite/**/*.js"
  ],
  theme: {
    extend: {
      animation: {
        'slide-in': 'slide-in .2s ease-out',
        'slide-out': 'slide-out .2s ease-in forwards',
        'fade-in': 'fade-in .2s ease-out',
        'fade-out': 'fade-out .2s ease-in forwards',
        'fade-in-scale': 'fade-in-scale .2s ease-out',
        'fade-out-scale': 'fade-out-scale .2s ease-in forwards',
        'shine': 'shine 2s ease-out infinite'
      },
      keyframes: {
        'slide-in': {
          from: {
            transform: 'translateX(100%)',
            opacity: 0
          },
          to: {
            transform: 'translateX(0)',
            opacity: '.9'
          }
        },
        'slide-out': {
          from: {
            transform: 'translateX(0)',
            opacity: '.9'
          },
          to: {
            transform: 'translateX(100%)',
            opacity: 0
          }
        },
        'fade-in': {
          from: {
            opacity: 0
          },
          to: {
            opacity: 1
          }
        },
        'fade-out': {
          from: {
            opacity: 1
          },
          to: {
            opacity: 0
          }
        },
        'fade-in-scale': {
          from: {
            transform: 'scale(.8) translateY(-100%)'
          },
          to: {
            transform: 'scale(1) translateY(0)'
          }
        },
        'fade-out-scale': {
          from: {
            transform: 'scale(1) translateY(0)'
          },
          to: {
            transform: 'scale(.8) translateY(-100%)'
          }
        },
        'shine': {
          from: {
            border: '0'
          },
          '50%': {
            border: '10px solid rgb(224 36 36)'
          },
          to: {
            border: '0'
          }
        }
      }
    },
    container: {
      center: true
    },
  },
  plugins: [
    require('flowbite/plugin'),
  ]
};
