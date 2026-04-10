/** @type {import('next').NextConfig} */
const nextConfig = {
  typescript: {
    ignoreBuildErrors: true,
  },
  images: {
    unoptimized: true,
  },
<<<<<<< HEAD
 
=======
  webpack(config) {
    if (!config.ignoreWarnings) config.ignoreWarnings = []
    config.ignoreWarnings.push((warning) => {
      if (
        warning.message &&
        typeof warning.message === "string" &&
        warning.message.includes("There are multiple modules with names that only differ in casing")
      ) {
        return true
      }
      return false
    })
    return config
  },
>>>>>>> 95064e54 (init: setup project and add authorization checks)
}

export default nextConfig
