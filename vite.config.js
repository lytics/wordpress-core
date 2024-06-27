import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import path, { resolve } from "path";
import typescript from "@rollup/plugin-typescript";
import { viteStaticCopy } from "vite-plugin-static-copy";

const rootDir = resolve(__dirname);
const outDir = resolve(rootDir, "dist");

export default defineConfig({
  esbuild: {
    target: "esnext",
  },
  server: {
    watch: {
      include: ["src/**"],
      exclude: ["node_modules", "dist", ".git"],
    },
  },
  plugins: [
    react(),
    typescript({
      tsconfig: "./tsconfig.json",
    }),
    viteStaticCopy({
      targets: [
        // root files
        {
          src: "./dist/admin/js/lytics-recommendation-render.js",
          dest: "./public/js",
        },
        {
          src: ".gitignore",
          dest: ".",
        },
        {
          src: "./src/uninstall.php",
          dest: ".",
        },
        {
          src: "./src/lytics.php",
          dest: ".",
        },
        {
          src: "./src/index.php",
          dest: ".",
        },

        // admin files
        {
          src: "./src/admin/*.php",
          dest: "./admin",
        },
        {
          src: "./src/admin/partials/*.php",
          dest: "./admin/partials",
        },
        {
          src: "./src/admin/img",
          dest: "./admin",
        },
        {
          src: "./src/admin/css",
          dest: "./admin",
        },
        {
          src: "./src/public",
          dest: ".",
        },
        {
          src: "./src/languages",
          dest: ".",
        },
        {
          src: "./src/includes",
          dest: ".",
        },
        {
          src: "./src/backup",
          dest: ".",
        },
      ],
    }),
  ],
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./src"),
    },
  },
  css: {
    preprocessorOptions: {
      scss: {},
    },
  },
  build: {
    minify: false,
    outDir: outDir,
    rollupOptions: {
      input: {
        "lytics-admin": path.resolve(
          __dirname,
          "./src/admin/js/lytics-admin/index.js"
        ),
        "lytics-recommendations-block": path.resolve(
          __dirname,
          "./src/admin/js/lytics-recommendations-block/index.ts"
        ),
        "lytics-recommendation-render": path.resolve(
          __dirname,
          "./src/admin/js/lytics-recommendations-block/view.ts"
        ),
        "lytics-pathfora-helper": path.resolve(
          __dirname,
          "./src/admin/js/lytics-pathfora-helper/index.js"
        ),
        "lytics-pathfora-interface": path.resolve(
          __dirname,
          "./src/admin/js/lytics-pathfora-interface/index.js"
        ),
        "lytics-widget-wizard": path.resolve(
          __dirname,
          "./src/admin/js/lytics-widget-wizard/index.js"
        ),
      },
      output: {
        entryFileNames: (chunkInfo) => {
          if (chunkInfo.name === "lytics-admin") {
            return "admin/js/lytics-admin.js";
          } else if (chunkInfo.name === "lytics-recommendations-block") {
            return "admin/js/lytics-recommendations-block.js";
          } else if (chunkInfo.name === "lytics-recommendation-render") {
            return "admin/js/lytics-recommendation-render.js";
          } else if (chunkInfo.name === "lytics-pathfora-helper") {
            return "admin/js/pathforaHelper.js";
          } else if (chunkInfo.name === "lytics-pathfora-interface") {
            return "admin/js/pathforaInterface.js";
          } else if (chunkInfo.name === "lytics-widget-wizard") {
            return "admin/js/lytics-widget-wizard.js";
          }
        },
        assetFileNames: (assetInfo) => {
          if (assetInfo.name.endsWith(".css")) {
            return "assets/[name].css";
          }
          return "assets/[name].[ext]";
        },
      },
    },
  },
});
