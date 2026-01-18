import { defineConfig } from "vite";
import fs from "fs";
import path from "path";
import compression from "vite-plugin-compression";

const inputEntries = {
  appcss: "./public/css/app.css",
  dashboardcss: "./public/css/dashboard.css",
  clientcss: "./public/css/client.css",
  icons: "./public/css/icons.css",
  appjs: "./public/js/app.js",
  dashboardjs: "./public/js/dashboard.js",
  clientjs: "./public/js/client.js",
  swiper: "./public/js/swiper.js",
  report: "./public/js/report.js",

  notfoundimg: "./public/img/svg/404.svg",
  loginimg: "./public/img/svg/login.svg",
  forgetimg: "./public/img/svg/forget.svg",
  resetimg: "./public/img/svg/reset.svg",
  twofactorimg: "./public/img/svg/twofactor.svg",
  lazyimg: "./public/img/svg/lazy.svg",
  waveimg: "./public/img/svg/wave.svg",
  wave1img: "./public/img/svg/wave1.svg",
  wave2img: "./public/img/svg/wave2.svg",
  sunimg: "./public/img/svg/sun.svg",
  moonimg: "./public/img/svg/moon.svg",
  devlogo: "./public/img/developer/dev_logo.png",
  devcvfile: "./public/img/developer/dev_cv.pdf",
  devbusinesscardfile: "./public/img/developer/dev_businesscard.pdf",

  kurdishimg: "./public/img/kurdish.png",
  arabicimg: "./public/img/arabic.png",
  englishimg: "./public/img/english.png",

  // pwa
  "pwa-apple-touch-icon": "./server/pwa/apple-touch-icon.png",
  "pwa-icon-72x72": "./server/pwa/icon-72x72.png",
  "pwa-icon-96x96": "./server/pwa/icon-96x96.png",
  "pwa-icon-128x128": "./server/pwa/icon-128x128.png",
  "pwa-icon-144x144": "./server/pwa/icon-144x144.png",
  "pwa-icon-152x152": "./server/pwa/icon-152x152.png",
  "pwa-icon-192x192": "./server/pwa/icon-192x192.png",
  "pwa-icon-384x384": "./server/pwa/icon-384x384.png",
  "pwa-icon-512x512": "./server/pwa/icon-512x512.png",
  "pwa-splash-640x1136": "./server/pwa/splash-640x1136.png",
  "pwa-splash-750x1334": "./server/pwa/splash-750x1334.png",
  "pwa-splash-1242x2208": "./server/pwa/splash-1242x2208.png",
  "pwa-splash-1125x2436": "./server/pwa/splash-1125x2436.png",
  "pwa-splash-828x1792": "./server/pwa/splash-828x1792.png",
  "pwa-splash-1242x2688": "./server/pwa/splash-1242x2688.png",
  "pwa-splash-1536x2048": "./server/pwa/splash-1536x2048.png",
  "pwa-splash-1668x2224": "./server/pwa/splash-1668x2224.png",
  "pwa-splash-1668x2388": "./server/pwa/splash-1668x2388.png",
  "pwa-splash-2048x2732": "./server/pwa/splash-2048x2732.png",
  "pwa-mobile-1": "./server/pwa/mobile-1.png",
  "pwa-mobile-2": "./server/pwa/mobile-2.png",
  "pwa-desktop-1": "./server/pwa/desktop-1.png",
  "pwa-desktop-2": "./server/pwa/desktop-2.png",
};

function normalizePath(p) {
  return p.replace(/^\.?\/+/, "").replace(/\\/g, "/");
}

const noHashList = [
  "apple-touch-icon.png",
  "icon-72x72.png",
  "icon-96x96.png",
  "icon-128x128.png",
  "icon-144x144.png",
  "icon-152x152.png",
  "icon-192x192.png",
  "icon-384x384.png",
  "icon-512x512.png",
  "splash-640x1136.png",
  "splash-750x1334.png",
  "splash-1242x2208.png",
  "splash-1125x2436.png",
  "splash-828x1792.png",
  "splash-1242x2688.png",
  "splash-1536x2048.png",
  "splash-1668x2224.png",
  "splash-1668x2388.png",
  "splash-2048x2732.png",
  "mobile-1.png",
  "mobile-2.png",
  "desktop-1.png",
  "desktop-2.png",
];

export default defineConfig({
  css: {
    postcss: "./postcss.config.js",
  },
  root: ".",
  base: "./",
  publicDir: false,
  build: {
    outDir: "dist",
    emptyOutDir: true,
    assetsDir: ".",
    manifest: "manifest.json",
    cssCodeSplit: true,
    minify: "terser", // switch from default 'esbuild'
    terserOptions: {
      compress: {
        pure_funcs: ["console.log", "debug"], // remove these if unused
        drop_console: true, // strips all console.* calls
        drop_debugger: true, // strips all debugger statements
        passes: 2, // deeper optimization
        ecma: 2015,
      },
      format: {
        comments: false,
      },
    },
    assetsInlineLimit: 0,
    rollupOptions: {
      input: inputEntries,
      output: {
        entryFileNames: (chunkInfo) => {
          return noHashList.includes(`${chunkInfo.name}.js`)
            ? "[name].js"
            : "[name].[hash].js";
        },
        assetFileNames: (assetInfo) => {
          const name = path.basename(assetInfo.name || "");
          return noHashList.includes(name)
            ? "[name][extname]"
            : "[name].[hash][extname]";
        },
      },
    },
  },
  plugins: [
    compression({ algorithm: "gzip" }),
    compression({ algorithm: "brotliCompress" }),
    {
      name: "rewrite-manifest-alias-keys",
      closeBundle() {
        const manifestPath = path.resolve("dist/manifest.json");
        if (!fs.existsSync(manifestPath)) {
          console.warn("Manifest not found!");
          return;
        }

        const manifest = JSON.parse(fs.readFileSync(manifestPath, "utf-8"));
        const pathToAlias = Object.entries(inputEntries).reduce(
          (acc, [alias, filePath]) => {
            acc[normalizePath(filePath)] = alias;
            return acc;
          },
          {}
        );
        const newManifest = {};

        for (const key in manifest) {
          const alias = pathToAlias[normalizePath(key)] || key;
          newManifest[alias] = manifest[key];
        }
        newManifest["dashboardpwamanifest"] = {
          file: "dashboard-pwa-manifest.json",
          name: "dashboardpwamanifest",
          src: "server/pwa/dashboard-pwa-manifest.json",
          isEntry: true,
        };
        newManifest["clientpwamanifest"] = {
          file: "client-pwa-manifest.json",
          name: "clientpwamanifest",
          src: "server/pwa/client-pwa-manifest.json",
          isEntry: true,
        };
        newManifest["dashboardpwamain"] = {
          file: "dashboard-pwa-main.js",
          name: "dashboardpwamain",
          src: "server/pwa/dashboard-pwa-main.js",
          isEntry: true,
        };
        newManifest["clientpwamain"] = {
          file: "client-pwa-main.js",
          name: "clientpwamain",
          src: "server/pwa/client-pwa-main.js",
          isEntry: true,
        };
        newManifest["clientpwasw"] = {
          file: "dashboard-pwa-sw.js",
          name: "clientpwasw",
          src: "server/pwa/dashboard-pwa-sw.js",
          isEntry: true,
        };
        newManifest["dashboardpwasw"] = {
          file: "dashboard-pwa-sw.js",
          name: "dashboardpwasw",
          src: "server/pwa/dashboard-pwa-sw.js",
          isEntry: true,
        };
        fs.writeFileSync(manifestPath, JSON.stringify(newManifest, null, 2));
        console.log("[vite-plugin] Manifest rewritten with alias keys.");
      },
    },
    {
      name: "copy-dashboard-pwa-manifest",
      apply: "build",
      generateBundle(_, bundle) {
        const dashboardJsonPath = path.resolve(
          __dirname,
          "server/pwa/dashboard-pwa-manifest.json"
        );
        const dashboardJsonContent = fs.readFileSync(
          dashboardJsonPath,
          "utf-8"
        );
        this.emitFile({
          type: "asset",
          fileName: "dashboard-pwa-manifest.json",
          source: dashboardJsonContent,
        });
      },
    },
    {
      name: "copy-client-pwa-manifest",
      apply: "build",
      generateBundle(_, bundle) {
        const clientJsonPath = path.resolve(
          __dirname,
          "server/pwa/client-pwa-manifest.json"
        );
        const clientJsonContent = fs.readFileSync(clientJsonPath, "utf-8");
        this.emitFile({
          type: "asset",
          fileName: "client-pwa-manifest.json",
          source: clientJsonContent,
        });
      },
    },
    {
      name: "copy-dashboard-pwa-main",
      apply: "build",
      generateBundle() {
        const filePath = path.resolve(
          __dirname,
          "server/pwa/dashboard-pwa-main.js"
        );
        const fileContent = fs.readFileSync(filePath, "utf-8");
        this.emitFile({
          type: "asset",
          fileName: "dashboard-pwa-main.js",
          source: fileContent,
        });
      },
    },
    {
      name: "copy-client-pwa-main",
      apply: "build",
      generateBundle() {
        const filePath = path.resolve(
          __dirname,
          "server/pwa/client-pwa-main.js"
        );
        const fileContent = fs.readFileSync(filePath, "utf-8");
        this.emitFile({
          type: "asset",
          fileName: "client-pwa-main.js",
          source: fileContent,
        });
      },
    },
    {
      name: "copy-dashboard-pwa-sw",
      apply: "build",
      generateBundle() {
        const filePath = path.resolve(
          __dirname,
          "server/pwa/dashboard-pwa-sw.js"
        );
        const fileContent = fs.readFileSync(filePath, "utf-8");
        this.emitFile({
          type: "asset",
          fileName: "dashboard-pwa-sw.js",
          source: fileContent,
        });
      },
    },
    {
      name: "copy-client-pwa-sw",
      apply: "build",
      generateBundle() {
        const filePath = path.resolve(__dirname, "server/pwa/client-pwa-sw.js");
        const fileContent = fs.readFileSync(filePath, "utf-8");
        this.emitFile({
          type: "asset",
          fileName: "client-pwa-sw.js",
          source: fileContent,
        });
      },
    },
  ],
});
