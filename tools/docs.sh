#!/usr/bin/env bash
set -euo pipefail

rm -rf build/site build/cache

vendor/bin/phpdoc run --config=phpdoc.dist.xml

# Inject extra ToC sections into index.html
sed -i.bak '/<h4 id="packages">/i\
<h4 id="guides">Guides</h4>\
<dl class="phpdocumentor-table-of-contents">\
    <dt class="phpdocumentor-table-of-contents__entry -namespace"><a href="docs/getting-started.html">Getting started</a></dt>\
    <dt class="phpdocumentor-table-of-contents__entry -namespace"><a href="docs/webhook.html">Webhook</a></dt>\
    <dt class="phpdocumentor-table-of-contents__entry -namespace"><a href="docs/error-handling.html">Error handling</a></dt>\
    <dt class="phpdocumentor-table-of-contents__entry -namespace"><a href="docs/laravel.html">Laravel integration</a></dt>\
</dl>\

' build/site/index.html
rm -f build/site/index.html.bak

sed -i.bak '
/phpdocumentor-table-of-contents__entry -namespace.*namespaces\/line\.html/,/<\/dl>/ {
    /<\/dl>/a\
\
<h4 id="reports">Reports</h4>\
<dl class="phpdocumentor-table-of-contents">\
    <dt class="phpdocumentor-table-of-contents__entry -namespace"><a href="reports/deprecated.html">Deprecated</a></dt>\
    <dt class="phpdocumentor-table-of-contents__entry -namespace"><a href="reports/errors.html">Errors</a></dt>\
    <dt class="phpdocumentor-table-of-contents__entry -namespace"><a href="reports/markers.html">Markers</a></dt>\
</dl>\
\
<h4 id="indices">Indices</h4>\
<dl class="phpdocumentor-table-of-contents">\
    <dt class="phpdocumentor-table-of-contents__entry -namespace"><a href="indices/files.html">Files</a></dt>\
</dl>\
\
<h4 id="links">Links</h4>\
<dl class="phpdocumentor-table-of-contents">\
    <dt class="phpdocumentor-table-of-contents__entry -namespace"><a href="https://github.com/line/line-bot-sdk-php">GitHub</a></dt>\
</dl>
}
' build/site/index.html
rm -f build/site/index.html.bak
