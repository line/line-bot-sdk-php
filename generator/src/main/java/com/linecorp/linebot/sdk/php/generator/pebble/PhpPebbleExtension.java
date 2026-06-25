package com.linecorp.linebot.sdk.php.generator.pebble;

import io.pebbletemplates.pebble.extension.AbstractExtension;
import io.pebbletemplates.pebble.extension.Filter;

import java.util.HashMap;
import java.util.Map;

public class PhpPebbleExtension extends AbstractExtension {
    @Override
    public Map<String, Filter> getFilters() {
        Map<String, Filter> filters = new HashMap<>();
        filters.put("mustache_escape", new PhpStringFilter());
        return filters;
    }
}
