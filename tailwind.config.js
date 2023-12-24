const colors = require('tailwindcss/colors')

module.exports = {
    content: [
      "./assets/**/*.js",
      "./templates/**/*.html.twig",
    ],
    darkMode: false, // or 'media' or 'class'
    theme: {
        
        extend: {
            /* backgroundImage: theme => ({
                'footer-left': "linear-gradient(165deg, rgba(92,173,47,1) 0%, rgba(0,0,8,0.5) 100%), url('../img/footer.webp');",
                'nosoffresbg': "linear-gradient(90deg, rgba(92,173,47,0.6166841736694677) 0%, rgba(0,0,8,0.5) 100%), url('../img/nos_offre_2.webp')",
                'index_back': "url('../img/bg-image-1.webp')",
            }) */
            height: {'128': '32rem',
            '112': '28rem',
        },
            flexGrow: {
                '2': 2
            }
        },
       
        screens: {
            'xs': '320px',
            // => @media (min-width: 320px) { ... }

            'sm': '640px',
            // => @media (min-width: 640px) { ... }

            'md': '768px',
            // => @media (min-width: 768px) { ... }

            'lg': '1024px',
            // => @media (min-width: 1024px) { ... }

            'xl': '1280px',
            // => @media (min-width: 1280px) { ... }

            '2xl': '1536px',
            // => @media (min-width: 1536px) { ... }
        },
        container:  {
            width: '100%',
            center: true,
            padding: {
                default: '0rem',
                xs: '0rem',
                sm: '0rem',
                md: '6rem',
                lg: '9rem',
                xl: '13rem',
            },
        },

        colors: {
            transparent: 'transparent',
            current: 'currentColor',
            black: colors.black,
            white: colors.white,
            gray: colors.neutral,
            indigo: colors.indigo,
            red: {
                DEFAULT: '#242430'
            },
            green: {
                DEFAULT: '#5CAD2F',
                light: '#7acb4d',
                sea: '#388659',
                top: '#A8EC81'
            },
            black: {
                light: '#242430'
            },
            gray: {
                light: '#74757f'
            },
            blue: {
                lightgreensea: '#20b2aa'
            }
        },
        shadow: {
            xl: '0px 0px 35px 6px rgba(8,13,63,0.54), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
        }
    },
    variants: {
            textColor: ['responsive', 'hover', 'focus', 'group-hover'],
            textAlign: ['responsive', 'hover', 'focus', 'group-hover']
        },
    plugins: [],
}