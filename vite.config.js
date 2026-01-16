import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

/**
 * Vite Configuration for WordPress Plugin
 * 
 * This configures Vite to build Vue components for a WordPress plugin.
 * The output goes to dist/ folder which WordPress will load.
 */
export default defineConfig({
  plugins: [vue()],
  
  // Define path aliases for cleaner imports
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src/vue'),
      '@components': path.resolve(__dirname, './src/vue/components'),
      '@views': path.resolve(__dirname, './src/vue/views'),
      '@store': path.resolve(__dirname, './src/vue/store'),
    },
  },

  // Build configuration
  build: {
    // Output directory
    outDir: 'dist',
    
    // Empty the output directory before building
    emptyOutDir: true,
    
    // Generate sourcemaps for debugging
    sourcemap: true,
    
    // Rollup options for WordPress integration
    rollupOptions: {
      input: {
        // Main admin app entry point
        admin: path.resolve(__dirname, 'src/js/admin-app.js'),
      },
      output: {
        // Output structure
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/[name]-[hash].js',
        assetFileNames: (assetInfo) => {
          // Organize assets by type
          if (assetInfo.name.endsWith('.css')) {
            return 'css/[name][extname]'
          }
          return 'assets/[name]-[hash][extname]'
        },
      },
    },
    
    // Minification
    minify: 'terser',
    terserOptions: {
      compress: {
        drop_console: true, // Remove console.logs in production
      },
    },
  },

  // Dev server configuration
  server: {
    port: 3000,
    strictPort: false,
    // Enable CORS for WordPress integration during development
    cors: true,
    hmr: {
      host: 'localhost',
    },
  },

  // CSS configuration
  css: {
    postcss: './postcss.config.js',
  },
})
