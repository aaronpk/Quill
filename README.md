Quill
=====
[Micropub](https://micropub.net/draft/) client written in PHP.

A hosted version is available to try at:

https://quill.p3k.io/


## Dependencies
- PHP
- MySQL or SQLite
- Composer for further dependency installation


## Setup
- Follow the "Web Server Configuration" section
- Run `composer install`
- Copy `lib/config.template.php` to `lib/config.php` and adjust it
- Import `schema/mysql.sql` (or `schema/sqlite.sql`)
- Open the Quill URL in your Browser


### Web Server Configuration

Set the document root to the "public" folder of this repo, and ensure all requests are routed through `public/index.php` if they don't match a file.

#### nginx

```
server {
  listen       80;
  server_name  quill.dev;

  root /path/to/Quill/public;

  error_log  logs/quill.error.log  notice;

  try_files $uri /index.php?$args;

  location /index.php {
    fastcgi_pass    php-pool;
    fastcgi_index   index.php;
    include fastcgi_params;
    fastcgi_param   SCRIPT_FILENAME $document_root$fastcgi_script_name;
  }
}
```

#### Apache .htaccess
An `.htaccess` file is already located in the `public/` folder.


### Contributing

By submitting code to this project, you agree to irrevocably release it under the same license as this project.


## Credits

Quill icon designed by [Juan Pablo Bravo from the Noun Project](http://thenounproject.com/term/quill/17013/).


## License

Copyright 2013-2016 by Aaron Parecki and contributors

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
