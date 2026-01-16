const basePath = process.env.NEXT_PUBLIC_BASE_PATH || '';

/** @type {import('next').NextConfig} */
const nextConfig = {
    output: 'export',
    basePath: basePath,
    assetPrefix: basePath + '/',
    publicRuntimeConfig: {
        basePath: basePath,
    }
}

module.exports = nextConfig
