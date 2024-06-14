// GULP PACKAGES
// Most packages are lazy loaded
const gulp  = require('gulp'),
      gutil = require('gulp-util'),
      browserSync = require('browser-sync').create(),
      filter = require('gulp-filter'),
      sass = require('gulp-sass')(require('sass')),
      touch = require('gulp-touch-cmd'),
      plugin = require('gulp-load-plugins')();

// GULP VARIABLES
// Modify these variables to match your project needs

// Set local URL if using Browser-Sync
const LOCAL_URL = 'http://teentix-craft.local/';

// Set path to Foundation files
const FOUNDATION = 'node_modules/foundation-sites';

// Select Foundation components, remove components project will not use
const SOURCE = {
  scripts: [
    // Let's grab what-input first
    'node_modules/jquery/dist/jquery.min.js',
    'node_modules/what-input/dist/what-input.js',
    'node_modules/easy-autocomplete/dist/jquery.easy-autocomplete.min.js',
    'node_modules/slick-carousel/slick/slick.min.js',

    // Foundation core - needed if you want to use any of the components below
    FOUNDATION + '/dist/js/plugins/foundation.core.js',
    FOUNDATION + '/dist/js/plugins/foundation.util.*.js',

    // Pick the components you need in your project
    FOUNDATION + '/dist/js/plugins/foundation.abide.js',
    //FOUNDATION + '/dist/js/plugins/foundation.accordion.js',
    FOUNDATION + '/dist/js/plugins/foundation.accordionMenu.js',
    //FOUNDATION + '/dist/js/plugins/foundation.drilldown.js',
    FOUNDATION + '/dist/js/plugins/foundation.dropdown.js',
    FOUNDATION + '/dist/js/plugins/foundation.dropdownMenu.js',
    FOUNDATION + '/dist/js/plugins/foundation.equalizer.js',
    //FOUNDATION + '/dist/js/plugins/foundation.interchange.js',
    //FOUNDATION + '/dist/js/plugins/foundation.offcanvas.js',
    FOUNDATION + '/dist/js/plugins/foundation.orbit.js',
    //FOUNDATION + '/dist/js/plugins/foundation.responsiveMenu.js',
    //FOUNDATION + '/dist/js/plugins/foundation.responsiveToggle.js',
    FOUNDATION + '/dist/js/plugins/foundation.reveal.js',
    //FOUNDATION + '/dist/js/plugins/foundation.slider.js',
    //FOUNDATION + '/dist/js/plugins/foundation.smoothScroll.js',
    //FOUNDATION + '/dist/js/plugins/foundation.magellan.js',
    //FOUNDATION + '/dist/js/plugins/foundation.sticky.js',
    //FOUNDATION + '/dist/js/plugins/foundation.tabs.js',
    //FOUNDATION + '/dist/js/plugins/foundation.responsiveAccordionTabs.js',
    //FOUNDATION + '/dist/js/plugins/foundation.toggler.js',
    //FOUNDATION + '/dist/js/plugins/foundation.tooltip.js',

    // Place custom JS here, files will be concantonated, minified if ran with --production
    'assets/scripts/js/**/*.js',
  ],

  // Scss files will be concantonated, minified if ran with --production
  styles: 'assets/styles/scss/**/*.scss',

  // Images placed here will be optimized
  images: 'assets/images/src/**/*',
  
  html: 'templates/**/*.html'

};

const ASSETS = {
  styles: 'html/assets/styles/',
  scripts: 'html/assets/scripts/',
  images: 'html/assets/images/',
  all: 'assets/'
};

const JSHINT_CONFIG = {
  "node": true,
  "globals": {
    "document": true,
    "window": true,
    "jQuery": true,
    "$": true,
    "Foundation": true
  }
};

// GULP FUNCTIONS
// JSHint, concat, and minify JavaScript
gulp.task('scripts', function() {

  // Use a custom filter so we only lint custom JS
  const CUSTOMFILTER = filter('assets/scripts/app.js', {restore: true});

  return gulp.src(SOURCE.scripts)
    .pipe(plugin.plumber(function(error) {
      gutil.log(gutil.colors.red(error.message));
      this.emit('end');
    }))
    .pipe(plugin.sourcemaps.init())
    .pipe(plugin.babel({
      presets: ['es2015'],
      compact: true,
      ignore: ['what-input.js','jquery.easy-autocomplete.min.js','slick.min.js']
    }))
    .pipe(CUSTOMFILTER)
      .pipe(plugin.jshint(JSHINT_CONFIG))
      .pipe(plugin.jshint.reporter('jshint-stylish'))
      .pipe(CUSTOMFILTER.restore)
    .pipe(plugin.concat('scripts.js'))
    .pipe(plugin.uglify())
    .pipe(plugin.sourcemaps.write('.')) // Creates sourcemap for minified JS
    .pipe(gulp.dest(ASSETS.scripts))
    .pipe(touch());
});

// Compile Sass, Autoprefix and minify
gulp.task('styles', function() {
  return gulp.src(SOURCE.styles)
    .pipe(plugin.plumber(function(error) {
      gutil.log(gutil.colors.red(error.message));
      this.emit('end');
    }))
    .pipe(plugin.sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(plugin.autoprefixer({
      'overrideBrowserslist': [
        'last 2 versions',
        'ie >= 9',
        'ios >= 7'
      ],
      cascade: false
    }))
    .pipe(plugin.cssnano({safe: true, minifyFontValues: {removeQuotes: false}}))
    .pipe(plugin.sourcemaps.write('.'))
    .pipe(gulp.dest(ASSETS.styles))
    .pipe(touch());
});

// Optimize images, move into assets directory
gulp.task('images', function() {
  return gulp.src(SOURCE.images)
    .pipe(plugin.imagemin())
    .pipe(gulp.dest(ASSETS.images))
    .pipe(touch());
});

// Browser-Sync watch files and inject changes
gulp.task('browsersync', function() {

  // Watch these files
  var files = [
    SOURCE.html,
  ];

  browserSync.init(files, {
    proxy: LOCAL_URL,
  });

  gulp.watch(SOURCE.styles, gulp.parallel('styles')).on('change', browserSync.reload);
  gulp.watch(SOURCE.scripts, gulp.parallel('scripts')).on('change', browserSync.reload);
  gulp.watch(SOURCE.images, gulp.parallel('images')).on('change', browserSync.reload);

});

// Watch files for changes (without Browser-Sync)
gulp.task('watch', function() {

  // Watch .scss files
  gulp.watch(SOURCE.styles, gulp.parallel('styles'));

  // Watch scripts files
  gulp.watch(SOURCE.scripts, gulp.parallel('scripts'));

  // Watch images files
  gulp.watch(SOURCE.images, gulp.parallel('images'));

});

// Run styles, scripts and foundation-js
gulp.task('default', gulp.parallel('styles', 'scripts', 'images'));