import { src, dest, task, watch, series, parallel } from 'gulp';
import dartSass from 'sass';
import gulpSass from 'gulp-sass';
import uglify from 'gulp-uglify'

const sass = gulpSass(dartSass);

function scss(done) {
  src(['./resources/scss/*.scss'])
    .pipe(sass({
      errLogToConsole: true,
      outputStyle: 'compressed',
      includePaths: ['./node_modules']
    }))
    .on('error', console.error.bind(console))
    .pipe(dest('./public/build/css'));
  done();
}

function script(done) {
  src(['./resources/script/**/*.js'])
    .pipe(uglify({
      compress: true,
    }))
    .pipe(dest('./public/build/script'));
  done();
}

// function reactScript(done) {
//     src(['./resources/script/**/*.jsx'])
//         .pipe(babel({
//             presets: ["@babel/preset-env", "@babel/preset-react"]
//         }))
//         .pipe(dest('./publiccript'));
//     done();
// }

function watch_files() {
  watch('./resources/scss/**/*.scss', series(scss));
  watch('./resources/script/**/*.js', series(script));
  // watch('./resources/script/**/*.jsx', series(reactScript));
}

task("default", parallel(watch_files));
