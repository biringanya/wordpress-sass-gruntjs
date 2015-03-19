require "scut"
require 'color-schemer'
require "susy"
require "breakpoint"

project_type = :stand_alone
http_path = "/"
css_dir = "css"
sass_dir = "sass"
images_dir = "img"
javascripts_dir = "js"

line_comments = false
output_style = :expanded
# output_style = :compact
# output_style = :compressed
environment = :development
relative_assets = true


# Move style.css to the root folder
require 'fileutils'
on_stylesheet_saved do |file|
  if File.exists?(file) && File.basename(file) == "style.css"
    puts "Moving: #{file}"
    FileUtils.mv(file, File.dirname(file) + "/../" + File.basename(file))
  end
end