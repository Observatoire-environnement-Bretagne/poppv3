var Encore = require('@symfony/webpack-encore');
//const { styles } = require( '@ckeditor/ckeditor5-dev-utils' );
//var SWPrecacheWebpackPlugin = require('sw-precache-webpack-plugin');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}
Encore
    .addEntry('app', './src/app.js') // will create public/build/app.js and public/build/app.css
    .setOutputPath('public/build') // the project directory where all compiled assets will be stored
    .setPublicPath('/build') // the public path used by the web server to access the previous directory
    .enableSassLoader() // allow sass/scss files to be processed
    .enableSourceMaps(!Encore.isProduction())
    .cleanupOutputBeforeBuild() // empty the outputPath dir before each build
    .autoProvidejQuery()
    .autoProvideVariables({
        Popper: ['popper.js', 'default']
    })
   /* .copyFiles([
        {from: './node_modules/ckeditor4/', to: 'ckeditor/[path][name].[ext]', pattern: /\.(js|css)$/, includeSubdirectories: false},
        {from: './node_modules/ckeditor4/adapters', to: 'ckeditor/adapters/[path][name].[ext]'},
        {from: './node_modules/ckeditor4/lang', to: 'ckeditor/lang/[path][name].[ext]'},
        {from: './node_modules/ckeditor4/plugins', to: 'ckeditor/plugins/[path][name].[ext]'},
        {from: './node_modules/ckeditor4/skins', to: 'ckeditor/skins/[path][name].[ext]'},
        {from: './node_modules/ckeditor4/vendor', to: 'ckeditor/vendor/[path][name].[ext]'}
    ])*/
    
    /*.autoProvideVariables({
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
        Popper: ['popper.js', 'default']
    })*//*.addPlugin(
        new SWPrecacheWebpackPlugin(
        {
            cacheId: 'Symfonator',
            dontCacheBustUrlsMatching: /\.\w{8}\./,
            filename: 'service-worker.js',
            minify: true,
            navigateFallback: 'index.html',
            staticFileGlobsIgnorePatterns: [/\.map$/, /asset-manifest\.json$/],
        })
    )*/
    .enableBuildNotifications() // show OS notifications when builds finish/fail
    // create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning()
    .enableSingleRuntimeChunk()
    /*.addLoader({
        test: /\.(woff|woff2|eot|ttf|otf)$/,
        use: [
            {
                loader: 'file-loader',
                options: {
                    outputPath: 'assets/dist/fonts/'
                }
            }
        ]
    })*/
    /*.copyFiles({
        from: './src/assets/static',
    })*/
;

// export the final configuration
var config = Encore.getWebpackConfig();
config.externals = {
    fs :'fs'
  };
  
//disable amd loader
config.module.rules.unshift({
    parser: {
        amd: false,
    }
});
/*
config.module.rules.unshift({
    test: /ckeditor5-[^/\\]+[/\\]theme[/\\].+\.css$/,

    use: [
        {
            loader: 'style-loader',
            options: {
                injectType: 'singletonStyleTag',
                attributes: {
                    'data-cke': true
                }
            }
        },
        'css-loader',
        {
            loader: 'postcss-loader',
            options: {
                postcssOptions: styles.getPostCssConfig( {
                    themeImporter: {
                        themePath: require.resolve( '@ckeditor/ckeditor5-theme-lark' )
                    },
                    minify: true
                } )
            }
        }
    ]
});
config.module.rules.unshift({
    test: /ckeditor5-[^/\\]+[/\\]theme[/\\]icons[/\\][^/\\]+\.svg$/,
    use: [ 'raw-loader' ]
});*/


module.exports = config;
//module.exports = Encore.getWebpackConfig();