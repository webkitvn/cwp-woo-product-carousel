const esbuild = require('esbuild');
const sassPlugin = require('esbuild-plugin-sass');
const postcss = require('postcss');
const autoprefixer = require('autoprefixer');
const purgecss = require('@fullhuman/postcss-purgecss');

async function build() {
    const isWatchMode = process.argv.includes('--watch');

    const ctx = await esbuild.context({
        entryPoints: ['assets/js/*.js', 'assets/css/cwp_woo_product_carousel.scss'],
        bundle: true,
        outdir: 'assets/dist',
        minify: true,
        sourcemap: true,
        metafile: true,
        loader: {
            ".png": "file",
            ".jpg": "file",
            ".jpeg": "file",
            ".svg": "file",
            ".gif": "file",
            ".woff": "file",
            ".woff2": "file",
            ".ttf": "file",
        },
        assetNames: 'assets/img/[name]',
        plugins: [
            sassPlugin({
                async transform(source) {
                    const { css } = await postcss([
                        purgecss({
                            content: [
                                'template-parts/**/*.php',
                                'inc/**/*.php',
                                'js/**/*.js',
                                'woocommerce/**/*.php',
                            ],
                        }),
                        autoprefixer,
                    ]).process(source, {
                        from: [
                            'assets/css/custom.scss',
                            'assets/css/fancybox.scss',
                            'assets/css/accordion.scss',
                        ],
                    });
                    return css;
                },
            }),
        ],
    });

    if (isWatchMode) {
        await ctx.watch();
        console.log('watching...');
    } else {
        await ctx.build();
    }
}

build().catch(() => process.exit(1));