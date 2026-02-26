const purgecssModule = require("@fullhuman/postcss-purgecss");
const purgecss = purgecssModule.default || purgecssModule;

const purgecssPlugin = purgecss({
  content: [
    "./**/*.php", // all PHP files in all subdirectories
    "./dashboard/**/*.php",
    "./*.php", // all PHP files in root
  ],
  defaultExtractor: (content) => content.match(/[\w-/:]+(?<!:)/g) || [],
  safelist: [
    /^ti-logout/,
    /^ti-logout-2/,
    /^ti-menu-2/,
    /^ti-menu-3/,
    /^ti-moon/,
    /^ti-sun/,
    /float-start/,
    /float-end/,
    /text-start/,
    /text-end/,
    /top-left/,
    /top-right/,
    /bottom-left/,
    /bottom-right/,
    /top-left-fixed/,
    /top-right-fixed/,
    /bottom-left-fixed/,
    /bottom-right-fixed/,
    /circle-arrow-left/,
    /circle-arrow-right/,
    /circle-arrow-left/,
    /circle-arrow-right/,
    /^dark-/,
    /^light-/,
    /^ms-/,
    /^me-/,
    /^swal/,
    /^toast/,
    /bg-success-theme/,
    /bg-warning-theme/,
    /bg-danger-theme/,
    /bg-info-theme/,
  ],
});

module.exports = {
  plugins: [
    require("autoprefixer"),
    ...(process.env.NODE_ENV === "production" ? [purgecssPlugin] : []),
  ],
};
