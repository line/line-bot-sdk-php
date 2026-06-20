package com.linecorp.linebot.sdk.php.generator.pebble;

import io.pebbletemplates.pebble.PebbleEngine;
import io.pebbletemplates.pebble.loader.ClasspathLoader;
import org.openapitools.codegen.api.AbstractTemplatingEngineAdapter;
import org.openapitools.codegen.api.TemplatingExecutor;

import java.io.IOException;
import java.io.StringWriter;
import java.util.Map;

public class PebbleTemplateAdapter extends AbstractTemplatingEngineAdapter {
    private static final String[] EXTENSIONS = new String[]{"pebble"};

    private final PebbleEngine engine = new PebbleEngine.Builder()
        .cacheActive(false)
        .newLineTrimming(false)
        .autoEscaping(false)
        .strictVariables(false)
        .loader(new ClasspathLoader())
        .extension(new PhpPebbleExtension())
        .build();

    public PebbleTemplateAdapter() {
        super();
    }

    @Override
    public String getIdentifier() {
        return "pebble";
    }

    @Override
    public String[] getFileExtensions() {
        return EXTENSIONS;
    }

    @Override
    public String compileTemplate(TemplatingExecutor executor, Map<String, Object> bundle, String templateFile) throws IOException {
        String modifiedTemplate = this.getModifiedFileLocation(templateFile)[0];

        StringWriter writer = new StringWriter();
        engine.getTemplate(modifiedTemplate).evaluate(writer, bundle);
        return writer.toString();
    }
}
