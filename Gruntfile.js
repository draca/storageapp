var path        = require( 'path' ),
    lrSnippet   = require( 'grunt-contrib-livereload/lib/utils' ).livereloadSnippet,
    mountFolder = function (connect, dir) {
        return connect.static(require('path').resolve(dir));
    };

module.exports = function( grunt ){

    'use strict';

	// Project configuration
	grunt.initConfig({

        /*
         * Get the project metadata.
         */
		pkg: grunt.file.readJSON( 'package.json' ),

        /*
         * CLean temporary and deployment folders when building.
         */
        clean: {
            dist: {
                files: [{
                    dot: true,
                    src: [
                        'dist/*'
                    ]
                }]
            },
            server: '.tmp'
        },

        /*
         * Run predefined tasks whenever watched file patterns are added, changed or deleted.
         */
		watch: {
			compass: {
				files: ['app/styles/sass/*.{scss,sass}'],
				tasks: ['compass']
			},
			livereload: {
				files: [
					'app/data/*.json',
                    'app/*.html',
                    'app/partials/*.html',
					'app/scripts/*.js',
					'app/styles/css/*.css'
				],
				tasks: ['livereload']
			}
		},

        /*
         * Start a static web server.
         * DEV URL http://localhost:9001/.
         * To view the local site on another device on the same LAN, use your master machine's IP address instead, for example http://157.125.83.183:9001/.
         */
		connect: {
            options: {
                port: 9000,
                // change this to '0.0.0.0' to access the server from outside
                hostname: 'localhost'
            },
            livereload: {
                options: {
                    middleware: function (connect) {
                        return [
                            lrSnippet,
                            mountFolder(connect, 'app')
                        ];
                    }
                }
            },
            dist: {
                options: {
                    middleware: function (connect) {
                        return [
                            mountFolder(connect, 'dist')
                        ];
                    }
                }
            }
        },

        /*
         * Launch the browser to the specified location.
         */
        open: {
            server: {
                path: 'http://localhost:<%= connect.options.port %>'
            }
        },

        /*
         * Compile Sass to CSS using Compass.
         */
        compass: {
            options: {
                sassDir: 'app/styles/sass',
                cssDir: 'app/styles/css',
                imagesDir: 'app/images',
                javascriptsDir: 'app/scripts',
                //fontsDir: 'app/styles/fonts',
                //importPath: 'app/components',
                relativeAssets: true,
                noLineComments: false,
                force: true
            },
            dist:{}
		},

        /*
         * Lint CSS files.
         */
        csslint: {
            options: {
                //csslintrc: '.csslintrc' // Get CSSLint options from external file.
            },
            strict: {
                options: {},
                src: ['app/styles/css/*.css']
            },
            lax: {
                options: {}
                // src: ['www/css/common/*.css']
            }
        }
	});

	grunt.loadNpmTasks('grunt-open');
	grunt.loadNpmTasks('grunt-regarde');
    grunt.loadNpmTasks('grunt-contrib-csslint');
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-compass');
	grunt.loadNpmTasks('grunt-contrib-connect');
    grunt.loadNpmTasks('grunt-contrib-mincss');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-livereload');
	grunt.loadNpmTasks('grunt-contrib-watch');

	grunt.renameTask('regarde', 'watch');

	grunt.registerTask('server', function (target) {
		//dist functionality commented out for now
		/*
        if (target === 'dist') {
            return grunt.task.run(['build', 'open', 'connect:dist:keepalive']);
        }
		*/
        grunt.task.run([
            'clean:server',
            'livereload-start',
            'connect:livereload',
            'open',
            'watch'
        ]);
    });

	grunt.registerTask('build', [
        'compass',
        'cssmin',
        'concat'
    ]);

	// Default task(s)
	grunt.registerTask( 'default', [ 'server' ]);
};