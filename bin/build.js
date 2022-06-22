const esbuild = require('esbuild')
const shouldWatch = process.argv.includes('--watch')

esbuild.build({
    define: {
        'process.env.NODE_ENV': shouldWatch ? `'production'` : `'development'`,
    },
    entryPoints: ['packages/forms/resources/js/index.js'],
    outfile: 'packages/forms/dist/module.esm.js',
    bundle: true,
    platform: 'neutral',
    mainFields: ['module', 'main'],
    watch: shouldWatch,
}).catch(() => process.exit(1))

esbuild.build({
    define: {
        'process.env.NODE_ENV': shouldWatch ? `'production'` : `'development'`,
    },
    entryPoints: ['packages/support/resources/js/index.js'],
    outfile: 'packages/tables/dist/module.esm.js',
    bundle: true,
    platform: 'neutral',
    mainFields: ['module', 'main'],
    watch: shouldWatch,
}).catch(() => process.exit(1))
