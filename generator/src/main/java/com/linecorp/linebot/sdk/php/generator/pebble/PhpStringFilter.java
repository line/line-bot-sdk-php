package com.linecorp.linebot.sdk.php.generator.pebble;

import io.pebbletemplates.pebble.extension.Filter;
import io.pebbletemplates.pebble.template.EvaluationContext;
import io.pebbletemplates.pebble.template.PebbleTemplate;

import java.util.Collections;
import java.util.List;
import java.util.Map;

/**
 * Mustache-compatible HTML escape: characters Mustache.java would escape via
 * {@code {{ ... }}} get the same hex-entity treatment, so descriptions and
 * other free-text fields round-trip byte-for-byte with the legacy generator.
 *
 * <p>The original OpenAPI Generator PHP templates used Mustache's default
 * {@code {{var}}} for descriptions/summaries (which HTML-escapes) and
 * {@code {{{var}}}} for paths/types (raw). Pebble templates here disable
 * auto-escaping so {@code {{var}}} matches the {@code {{{var}}}} semantics;
 * to recover the old escape behavior we apply this filter explicitly where
 * needed.</p>
 */
public class PhpStringFilter implements Filter {
    @Override
    public List<String> getArgumentNames() {
        return Collections.emptyList();
    }

    @Override
    public Object apply(Object input, Map<String, Object> args, PebbleTemplate self, EvaluationContext context, int lineNumber) {
        if (input == null) {
            return null;
        }
        String s = input.toString();
        StringBuilder sb = new StringBuilder(s.length());
        for (int i = 0; i < s.length(); i++) {
            char c = s.charAt(i);
            switch (c) {
                case '&':
                    sb.append("&amp;");
                    break;
                case '<':
                    sb.append("&lt;");
                    break;
                case '>':
                    sb.append("&gt;");
                    break;
                case '"':
                    sb.append("&quot;");
                    break;
                case '\'':
                    sb.append("&#39;");
                    break;
                case '`':
                    sb.append("&#x60;");
                    break;
                case '=':
                    sb.append("&#x3D;");
                    break;
                default:
                    sb.append(c);
                    break;
            }
        }
        return sb.toString();
    }
}
