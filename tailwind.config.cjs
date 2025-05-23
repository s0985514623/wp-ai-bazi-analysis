/** @type {import('tailwindcss').Config} */
// eslint-disable-next-line no-undef
module.exports = {
	important: true,
	corePlugins: {
		preflight: false,
	},
	content: ['./js/src/**/*.{js,ts,jsx,tsx}', './inc/**/*.php'],
	theme: {
		container: {
			center: true,
			screens: {
				sm: '576px',
				md: '810px',
				lg: '1080px',
				xl: '1280px',
				xxl: '1536px',
				'2xl': '1920px',
			},
		},
		extend: {
			colors: {
				primary: '#1677ff',
			},
			screens: {
				sm: '576px', // iphone SE
				md: '810px', // ipad Portrait
				lg: '1080px', // ipad Landscape
				xl: '1280px', // mac air
				xxl: '1536px',
				'2xl': '1920px',
			},
		},
	},
	plugins: [
		function ({ addUtilities }) {
			const newUtilities = {
				'.rtl': {
					direction: 'rtl',
				},

				// 與 WordPress 衝突的 class
				'.tw-hidden': {
					display: 'none',
				},
				'.tw-column-1': {
					columnCount: 1,
				},
				'.tw-column-2': {
					columnCount: 2,
				},
				'.tw-fixed': {
					position: 'fixed',
				},
			}
			addUtilities(newUtilities, ['responsive', 'hover'])
		},
	],
	safelist: [],
	blocklist: ['fixed', 'columns-1', 'columns-2'],
}
