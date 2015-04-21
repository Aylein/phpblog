module.exports = function(grunt) {
    "use strict";
    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),
        uglify: {
            options:{
                banner: "/*!<%=pkg.author%>(<%=grunt.template.today('dd-mm-yyyy')%>)*/"
            },
            main:{
                options:{
                    mangle: false
                },
                files:[{
                    expand: true,
                    cwd: "source/scripts",
                    src: "*.js",
                    dest: "release/scripts",
                    ext: ".min.js"
                }]
            }
        },
        cssmin: {
            options:{
                keepSpecialComments: 0
            },
            main:{
                files:[{
                    expand: true,
                    cwd: "source/styles",
                    src: "*.css",
                    dest: "release/styles",
                    ext: ".min.css"
                }]
            }
        },
        copy: {
            main: {
                files:[
                    {src: "source/bower_components/angular/angular.min.js", dest: "release/scripts/angular.min.js"},
                    {src: "source/bower_components/angular/angular.min.js.map", dest: "release/scripts/angular.min.js.map"},
                    {src: "source/bower_components/angular-route/angular-route.min.js", dest: "release/scripts/angular-route.min.js"},
                    {src: "source/bower_components/angular-route/angular-route.min.js.map", dest: "release/scripts/angular-route.min.js.map"}
                ]
            }
        },
        watch: {
            script: {
                files: ["source/scripts/*.js"],
                tasks: ["uglify:main"]
            },
            css: {
                files: ["source/styles/*.css"],
                tasks: ["cssmin:main"]
            }
        }
    });
    grunt.loadNpmTasks("grunt-contrib-uglify");
    grunt.loadNpmTasks("grunt-contrib-cssmin");
    grunt.loadNpmTasks("grunt-contrib-copy");
    grunt.loadNpmTasks("grunt-contrib-watch");
    grunt.registerTask("default", ["uglify:main", "cssmin:main", "copy:main"]);
};