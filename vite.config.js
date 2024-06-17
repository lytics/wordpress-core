import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import path, { resolve } from "path";
import typescript from "@rollup/plugin-typescript";
import { viteStaticCopy } from "vite-plugin-static-copy";
// import sass from "sass";
// import fs from "fs";

const rootDir = resolve(__dirname);
const outDir = resolve(rootDir, "dist");

// function compileSCSS() {
//   const scssPath = path.resolve(__dirname, "styles/main.scss");
//   const cssPath = path.resolve(__dirname, `${outDir}/css/core.css`);

//   const compile = () => {
//     sass.render({ file: scssPath }, (err, result) => {
//       if (err) {
//         console.error(err);
//         return;
//       }
//       fs.mkdirSync(path.dirname(cssPath), { recursive: true });
//       fs.writeFileSync(cssPath, result.css);
//     });
//   };

//   return {
//     name: "compile-scss",
//     buildStart() {
//       compile();
//     },
//     handleHotUpdate({ file, server }) {
//       if (file.endsWith(".scss")) {
//         compile();
//         server.ws.send({
//           type: "full-reload",
//         });
//       }
//     },
//   };
// }

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
    // compileSCSS(),
    typescript({
      tsconfig: "./tsconfig.json",
    }),
    viteStaticCopy({
      targets: [
        // root files
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
        // admin/css
        // admin/img
        // admin/js

        // public files

        // backup files

        // include files
      ],
    }),
  ],
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./src"),
    },
  },
  build: {
    minify: false,
    outDir: outDir,
    rollupOptions: {
      input: {
        "lytics-admin": path.resolve(
          __dirname,
          "./src/admin/js/lytics-admin/index.ts"
        ),
        "lytics-recommendations-block": path.resolve(
          __dirname,
          "./src/admin/js/lytics-recommendations-block/index.js"
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
          } else if (chunkInfo.name === "lytics-pathfora-helper") {
            return "admin/js/pathforaHelper.js";
          } else if (chunkInfo.name === "lytics-pathfora-interface") {
            return "admin/js/pathforaInterface.js";
          } else if (chunkInfo.name === "lytics-widget-wizard") {
            return "admin/js/lytics-widget-wizard.js";
          }
        },
      },
    },
  },
});
