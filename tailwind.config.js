/** @type {import('tailwindcss').Config} */
// eslint-disable-next-line no-undef
module.exports = {
	important: true,
	corePlugins: {
		preflight: false,
	},

	// prefix: 'tw-',

	content: ['./js/src/**/*.{js,ts,jsx,tsx}', './**/*.{php, html, js, ts}'],
	theme: {
		extend: {
			colors: {
				primary: '#1677ff',
			},
			screens: {
				sm: '576px', // iphone SE
				md: '810px', // ipad 直向
				lg: '1080px', // ipad 橫向
				xl: '1280px', // mac air
				xxl: '1440px',
			},
		},
	},
	plugins: [
		function ({ addUtilities }) {
			const newUtilities = {
				'.rtl': {
					direction: 'rtl',
				},

				// classes conflicted with WordPress
				'.tw-hidden': {
					display: 'none',
				},
				'.tw-columns-1': {
					columnCount: 1,
				},
				'.tw-columns-2': {
					columnCount: 2,
				},
				'.tw-fixed': {
					position: 'fixed',
				},
				'.tw-inline': {
					display: 'inline'
				}
			}
			addUtilities(newUtilities, ['responsive', 'hover'])
		},
	],
	safelist: [],
	blocklist: ['fixed', 'columns-1', 'columns-2', 'hidden', 'inline'],
}
