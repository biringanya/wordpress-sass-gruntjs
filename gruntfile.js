module.exports = function(grunt) {
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-compass');
	// grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.initConfig({
		uglify: {
			my_target: {
				files: {
					'_/js/main.js':['_/js/*.js']
				}//myfiles
			}//my target
		},//uglify
		compass:{
			dev: {
				options: {
					config: 'config.rb'
				}//options
			}//dev
		},//compass
		watch: {
			options: { livereload: true},
			scripts: {
				files: ['js/*.js'],
				tasks: ['uglify']
			},//script
			sass: {
				files: ['sass/*.scss'],
				tasks: ['compass:dev']
			},//sass
			html: {
				files: ['*.html']
			}	
		}//watch
		makepot: {										// https://github.com/cedaro/grunt-wp-i18n/blob/develop/docs/makepot.md
	        target: {
	            options: {
	                cwd: '/templates',                // Directory of files to internationalize.
	                domainPath: '/languages',         // Where to save the POT file.
	                exclude: [],                      // List of files or directories to ignore.
	                include: ['single.php', 'page.php', 'archive.php'],                      // List of files or directories to include.
	                mainFile: 'index.php',                     // Main project file.
	                potComments: '',                  // The copyright at the beginning of the POT file.
	                potFilename: 'base.pot',                  // Name of the POT file.
	                potHeaders: {
	                    poedit: true,                 // Includes common Poedit headers.
	                    'x-poedit-keywordslist': true // Include a list of all possible gettext functions.
	                },                                // Headers to add to the generated POT file.
	                processPot: null,                 // A callback function for manipulating the POT file.
	                type: 'wp-theme',                // Type of project (wp-plugin or wp-theme).
	                updateTimestamp: true             // Whether the POT-Creation-Date should be updated without other changes.
	                updatePoFiles: false              // Whether to update PO files in the same directory as the POT file.
	            }
	        }
    	}
	})//initconfig
	grunt.registerTask('default', 'watch');
}//exports	
