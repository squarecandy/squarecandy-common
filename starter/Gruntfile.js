module.exports = function( grunt ) {
	const sass = require( 'sass' );
	const settings = grunt.file.readJSON('grunt-options.json');
	const branch = require( 'child_process' )
		.execSync( 'git branch --show-current', { encoding: 'utf8' } )
		.trim()
		.split( '/' )
		.pop();
	// calculate what files to copy in
	const defaultCopyFiles = [
		// common
		{
			expand: true,
			cwd: 'node_modules/squarecandy-common/common',
			src: '**/*',
			dest: '',
			dot: true,
			rename( dest, matchedSrcPath ) {
				// the exact file name .gitignore is reserved by npm
				// so we track it as /common/gitignore (no dot) and rename on copy
				if ( matchedSrcPath === 'gitignore' ) {
					return dest + '.gitignore';
				}
				// default for all other files
				return dest + matchedSrcPath;
			},
			// We are removing .eslintignore & .eslintrc, copy over these files if they exist
		    filter: function( filepath ) {
		    	if ( filepath.includes( '.eslintignore' ) || filepath.includes( '.eslintrc' ) ) {
		    		// get the dest path of the file
		    		const basePath = filepath.replace( this.cwd + '/', '' );
		    		// check whether the file exists, if it does, copy over it, but if not, don't re-add it
		    		const fileExists = grunt.file.exists( basePath );
		    		if ( fileExists ) {
		    			grunt.log.writeln( basePath + ' should be deleted.' );
		    		}					    		
		    		return fileExists;
		    	} else if ( filepath.includes( 'grunt-options.json' ) ) {
		    		// get the dest path of the file
		    		const basePath = filepath.replace( this.cwd + '/', '' );
		    		// check whether the file exists, if it does, don't copy over it, if it doesn't, add it
		    		const fileExists = grunt.file.exists( basePath );	    		
		    		return ! fileExists;
		    	} else {
		    		return true;
		    	}
		    },
		},
		{
			expand: true,
			cwd: 'node_modules/squarecandy-common/' + settings.copyType,
			src: '**/*',
			dest: '',
			dot: true,
		},
	];
	const copyFiles = typeof settings.additionalCopyFiles == 'undefined' ? defaultCopyFiles : defaultCopyFiles.concat( settings.additionalCopyFiles );
	const phpPaths = settings.phpFiles.join( ' ' );
	grunt.initConfig( {
		pkg: grunt.file.readJSON( 'package.json' ),
		sass: {
			// sass tasks
			dist: {
				files: settings.sassFiles,
			},
			options: {
				implementation: sass,
				compass: true,
				style: 'expanded',
				sourceMap: true,
			},
		},
		postcss: {
			options: {
				map: true, // inline sourcemaps
				processors: [
					// add vendor prefixes
					require( 'autoprefixer' )( { grid: 'autoreplace' } ),
					// minify the result
					require( 'cssnano' )( {
						preset: [ 'default', {
							colormin: false, // Disable color optimization completely. Shortening is mostly handled by stylelint already.
						} ],
					} ),
				],
			},
			dist: {
				src: 'dist/css/*.css',
			},
		},
		copy: {
			preflight: {
				files: copyFiles,
			},
		},
		modernizr: {
			dist: {
				crawl: false,
				customTests: [],
				dest: 'dist/js/vendor/modernizr.min.js',
				// lookup test names or make a custom set here: https://modernizr.com/download?setclasses
				tests: [
					'hiddenscroll',
					'input',
					'inputtypes',
					'svg',
					'webp',
					'touchevents',
					[ 'cssgrid', 'cssgridlegacy' ],
					'flexbox',
					'flexboxlegacy',
					'objectfit',
					'cssvhunit',
					'cssvwunit',
					'flexgap',
				],
				options: [ 'setClasses' ],
				uglify: true,
			},
		},
		terser: {
			options: {
				sourceMap: true,
			},
			dist: {
				files: [
					{
						expand: true,
						src: '*.js',
						dest: 'dist/js',
						cwd: 'js',
						ext: '.min.js',
					},
				],
			},
		},
		stylelint: {
			src: [ 'css/*.scss', 'css/**/*.scss', 'css/*.css' ],
		},
		eslint: {
			gruntfile: {
				src: [ 'Gruntfile.js' ],
			},
			src: {
				src: [ 'js' ],
			},
		},
		run: {
			stylelintfix: {
				cmd: 'npx',
				args: [ 'stylelint', 'css/*.scss', 'css/**/*.scss', '--fix' ],
			},
			eslintfix: {
				cmd: 'npx',
				args: [ 'eslint', 'js/*.js', 'Gruntfile.js', '--fix' ],
			},
			phpcs: {
				cmd: 'bash',
				args: [
					'-c',
					'./vendor/squizlabs/php_codesniffer/bin/phpcs ' +
					'--standard=phpcs.xml ' +
					'--runtime-set ignore_warnings_on_exit 1 ' +
					phpPaths,
				],
			},
			phpcbf: {
				cmd: 'bash',
				args: [
					'-c',
					'./vendor/squizlabs/php_codesniffer/bin/phpcbf ' +
					'--standard=phpcs.xml ' +
					phpPaths + ' || true',
				],
			},
			bump: {
				cmd: 'npm',
				args: [ 'run', 'release', '--', '--prerelease', branch, '--skip.tag', '--skip.changelog' ],
			},
			update: {
				cmd: 'npm',
				args: [ 'update' ]
			},
			ding: {
				cmd: 'tput',
				args: [ 'bel' ],
			},
		},
		watch: {
			css: {
				files: [ 'css/*.scss', 'css/**/*.scss' ],
				tasks: [ 'run:stylelintfix', 'sass', 'postcss', 'string-replace', 'run:ding' ],
			},
			js: {
				files: [ 'js/*.js' ],
				tasks: [ 'run:eslintfix', 'terser', 'run:ding' ],
			},
		},
		'string-replace': {
			dist: {
				files: [
					{
						expand: true,
						cwd: 'dist/css/',
						src: '*.min.css.map',
						dest: 'dist/css/',
					},
				],
				options: {
					replacements: [
						// place files inline example
						{
							pattern: /(file:\/\/\/([^,]*)\/wp-content)+/g,
							replacement: '/wp-content',
						},
					],
				},
			},
		},
		gitnewer: {
			checkForNewFiles: {
				options: {
					override: function( details, include ) {
						include( true );
					},
				},
			},
		},
		checkForNewFiles: {
			src: {
				src: [ '**/*.*', '*.*' ],
			},
		},
	} );

	grunt.loadNpmTasks( 'grunt-sass' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.loadNpmTasks( 'grunt-terser' );
	grunt.loadNpmTasks( 'grunt-phpcs' );
	grunt.loadNpmTasks( 'grunt-stylelint' );
	grunt.loadNpmTasks( 'grunt-eslint' );
	grunt.loadNpmTasks( '@lodder/grunt-postcss' );
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-modernizr' );
	grunt.loadNpmTasks( 'grunt-run' );
	grunt.loadNpmTasks( 'grunt-string-replace' );
	grunt.loadNpmTasks( 'grunt-gitnewer' );

	grunt.registerTask( 'default', [ 'run:stylelintfix', 'run:eslintfix', 'sass', 'postcss', 'terser', 'string-replace', 'watch' ] );
	grunt.registerTask( 'update', [ 'run:update', 'copy:preflight' ] );

	grunt.registerTask( 'compile', 'compile task with optional modernizr', function() {
	    const beforeTasks = [ 'sass', 'postcss' ];
	    const afterTasks = [ 'terser', 'string-replace' ];
	    if ( settings.doModernizr ) {
	      beforeTasks.push('modernizr');
	    }
	    tasks = beforeTasks.concat( afterTasks );
	    grunt.task.run(tasks);
	})
	grunt.registerTask( 'lint', [ 'stylelint', 'eslint', 'run:phpcs' ] );
	grunt.registerTask( 'phpfix', [ 'run:phpcbf' ] );
	grunt.registerTask( 'fix', [ 'run:stylelintfix', 'run:eslintfix', 'run:phpcbf' ] );
	grunt.registerTask( 'bump', [ 'run:bump' ] );
	grunt.registerTask( 'preflight', [ 'compile', 'lint', 'shouldBump', 'bump', 'run:ding' ] );

	grunt.registerTask( 'shouldBump', [ 'gitnewer:checkForNewFiles' ] ); // send output of git-newer to checkForNewFiles
    grunt.registerMultiTask( 'checkForNewFiles', 'Check if files changed that need to be committed before bumping.', function() {
		const allowedFiles = [
		'package-lock.json',
		'package.json',
		'functions.php',
		'plugin.php',
		'readme.txt',
		]; // files that change with bump
		this.filesSrc.forEach( function( file ) {
	        if ( ! allowedFiles.includes( file ) ) {
	        	grunt.fail.warn( file + ' should be committed before bump. ' ); // abort mission
	        }
	    } );
	} );
};
