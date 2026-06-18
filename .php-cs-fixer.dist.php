<?php

/*
 * This file is part of the 2amigos/qrcode-library project.
 *
 * (c) 2amigOS! <http://2am.tech/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

$header = <<<'EOF'
This file is part of the 2amigos/qrcode-library project.

(c) 2amigOS! <http://2am.tech/>

For the full copyright and license information, please view
the LICENSE file that was distributed with this source code.
EOF;

// Only lint the library source. The rest of the repo (tests, docs, resources,
// routes, config) does not follow the file-header convention.
$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src');

$config = new PhpCsFixer\Config();

return $config
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => true,
        'no_unused_imports' => true,
        'single_quote' => true,
        'trailing_comma_in_multiline' => true,
        'header_comment' => [
            'header' => $header,
            'comment_type' => 'comment',
            'location' => 'after_open',
            'separate' => 'both',
        ],
    ])
    ->setFinder($finder);
