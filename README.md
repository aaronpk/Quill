Quill
=====

Work in progress. Do not use!

https://quill.p3k.io/


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

#### Apache htaccess

```
  RewriteEngine on

  RewriteBase /

  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_URI} !=/favicon.ico
  RewriteRule ^ index.php [L]
```


### Contributing

By submitting code to this project, you agree to irrevocably release it under the same license as this project.


### Credits 

Quill icon designed by [Juan Pablo Bravo from the Noun Project](http://thenounproject.com/term/quill/17013/).


### License

Copyright 2013 by Aaron Parecki

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
