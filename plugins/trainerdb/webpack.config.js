/**
 * Internal Dependencies.
 */
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const glob = require( 'glob' );

/**
 * External Dependencies.
 */
const { CleanWebpackPlugin } = require( 'clean-webpack-plugin' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const IgnoreEmitPlugin = require( 'ignore-emit-webpack-plugin' );

const entry = {
	...defaultConfig.entry,
};

const styles = glob.sync( './src/**/*/style.{scss,css}' );
if ( styles.length ) {
	entry.style = styles;
}

const editorStyles = glob.sync( './src/**/*/editor.{scss,css}' );
if ( editorStyles.length ) {
	entry.editor = editorStyles;
}

const frontendScript = glob.sync( './src/frontend.js' );
if ( frontendScript.length ) {
	entry.frontend = frontendScript;
}

module.exports = {
	...defaultConfig,
	entry,
	module: {
		...defaultConfig.module,
		rules: [
			...defaultConfig.module.rules,
			{
				test: /\.s?css$/,
				use: [
					{ loader: MiniCssExtractPlugin.loader },
					{ loader: 'css-loader', options: { importLoaders: 1 } },
					{ loader: 'postcss-loader' },
					{ loader: 'sass-loader' },
				],
			},
			{
					test: /\.js/,
					exclude: /(node_modules)/,
					loader: 'babel-loader',
					options: {
							presets: ['@babel/preset-env',
												'@babel/react',{
												'plugins': ['@babel/plugin-proposal-class-properties']}]
					}
			},
		],
	},
	plugins: [
		...defaultConfig.plugins,
		new CleanWebpackPlugin(),
		new MiniCssExtractPlugin( {
			filename: '[name].css',
			chunkFilename: '[id].css',
		} ),
		new IgnoreEmitPlugin( [
			'editor.asset.php',
			'editor.js',
			'frontend.asset.php',
			'style.asset.php',
			'style.js',
		] ),
	],
};
