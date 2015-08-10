## WordPress Choose Header
A WordPress plugin to allow admins to select which header file to display on a particular page

## USE:
- Install plugin
- Replace all instances of `get_header()` in template files with `get_custom_header_file()`, or to preserve maximum compatibility, replace `get_header()` with `if(function_exists(get_custom_header_file)){ get_custom_header_file(); } else { get_header(); }`

*Developed by Stephen Widom - http://stephenwidom.com*