import { ColorModeScript } from '@chakra-ui/react'
import { Html, Head, Main, NextScript } from 'next/document'
import theme from '../libs/theme'

export default function Document() {
	return (
		<Html lang="en">
			<Head >
				<link rel="preconnect" href="https://fonts.googleapis.com" />
					<link rel="preconnect" href="https://fonts.gstatic.com" />
						<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai&display=swap" rel="stylesheet" />
						</Head>
						<body>
							<ColorModeScript initialColorMode={theme.config.initialColorMode} />
							<Main />
							<NextScript />
						</body>
					</Html>
					)
}
