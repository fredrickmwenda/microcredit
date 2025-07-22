const mix = require('laravel-mix');
require('laravel-mix-merge-manifest');

// Set the public path to ensure output goes to public/
mix.setPublicPath('public').mergeManifest();

// JavaScript and Sass compilation
mix.js('Modules/Core/Resources/assets/js/app.js', 'js/all.js')
   .vue() // make sure to add this if you're using .vue files
   .sass('Modules/Core/Resources/assets/sass/app.scss', 'css/all.css');

// Webpack config to use Vue with template compiler
mix.webpackConfig({
    resolve: {
        alias: {
            vue$: 'vue/dist/vue.esm.js' // For Vue 2 with template compiler
        }
    }
});

// Versioning only in production for cache-busting
if (mix.inProduction()) {
    mix.version();
}

