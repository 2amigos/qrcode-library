Laravel Blade Component
----

You can publish the qrcode's config file by executing the given command:

```bash
$ php artisan vendor:publish --tag=2am-qrcode-config
```

This will create a file name 2am-qrcode.php under your config folder, where you can
set the default look of your qrcode and the component prefix.

By the next command, you can publish the component related view, enabling you to perform your
own customization to component structure.

```bash
$ php artisan vendor:publish --tag=2am-qrcode-views
```