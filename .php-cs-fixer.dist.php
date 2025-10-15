<?php

if (!file_exists(__DIR__.'/src') || !file_exists(__DIR__.'/tests')) {
    exit(0);
}

$finder = (new \PhpCsFixer\Finder())
    ->in([__DIR__.'/src', __DIR__.'/tests'])
;

return (new \PhpCsFixer\Config())
    ->setRules(array(
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'phpdoc_to_comment' => false,
        'header_comment' => [
            'header' => <<<EOF
This file is part of the Enabel UX package.
Copyright (c) Enabel <https://enabel.be/>
For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF
        ]
    ))
    ->setRiskyAllowed(true)
    ->setParallelConfig(new PhpCsFixer\Runner\Parallel\ParallelConfig(4, 20))
    ->setFinder($finder)
    ;