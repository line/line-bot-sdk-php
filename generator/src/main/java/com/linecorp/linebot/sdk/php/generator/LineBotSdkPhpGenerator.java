package com.linecorp.linebot.sdk.php.generator;

import org.openapitools.codegen.CodegenConfig;
import org.openapitools.codegen.CodegenModel;
import org.openapitools.codegen.CodegenOperation;
import org.openapitools.codegen.CodegenProperty;
import org.openapitools.codegen.SupportingFile;
import org.openapitools.codegen.languages.PhpClientCodegen;
import org.openapitools.codegen.model.ModelMap;
import org.openapitools.codegen.model.ModelsMap;
import org.openapitools.codegen.model.OperationMap;
import org.openapitools.codegen.model.OperationsMap;

import java.io.File;
import java.util.List;
import java.util.Map;

/**
 * Custom OpenAPI Generator codegen for the line-bot-sdk-php repository.
 *
 * <p>This generator extends the stock {@link PhpClientCodegen} but replaces every
 * Mustache template registration with explicit Pebble counterparts so that
 * nothing falls back to the bundled OpenAPI Generator PHP templates. The
 * effective template set is therefore fully owned by this repository.</p>
 */
public class LineBotSdkPhpGenerator extends PhpClientCodegen implements CodegenConfig {

    public static final String COPYRIGHT_YEAR = "copyrightYear";

    private static final String TEMPLATE_DIR_NAME = "line-bot-sdk-php-generator";
    private static final String CHANNEL_ACCESS_TOKEN_API_CLASS = "ChannelAccessTokenApi";
    private static final String STATELESS_OPERATION_ID = "issueStatelessChannelToken";
    private static final String STATELESS_WRAPPERS_VENDOR_EXT = "x-line-stateless-channel-token-wrappers";
    private static final String STATELESS_DEPRECATED_VENDOR_EXT = "x-line-stateless-channel-token-deprecated";

    public LineBotSdkPhpGenerator() {
        super();

        embeddedTemplateDir = templateDir = TEMPLATE_DIR_NAME;

        // Wipe inherited Mustache template registrations so nothing falls back
        // to the bundled OpenAPI Generator PHP templates.
        modelTemplateFiles.clear();
        apiTemplateFiles.clear();
        modelDocTemplateFiles.clear();
        apiDocTemplateFiles.clear();
        modelTestTemplateFiles.clear();
        apiTestTemplateFiles.clear();

        modelTemplateFiles.put(TEMPLATE_DIR_NAME + "/model.pebble", ".php");
        apiTemplateFiles.put(TEMPLATE_DIR_NAME + "/api.pebble", ".php");
    }

    @Override
    public String getName() {
        return "line-bot-sdk-php-generator";
    }

    @Override
    public String getHelp() {
        return "Generates LINE Messaging SDK PHP clients using the in-repo Pebble templates.";
    }

    @Override
    public void processOpts() {
        super.processOpts();

        // The parent registers a long list of supporting files using bundled
        // PHP Mustache templates. Snapshot the folders we need before wiping
        // the registrations so we can rebuild them with our Pebble templates.
        String invokerFolder = findSupportingFileFolder("ApiException.mustache");
        String configurationFolder = findSupportingFileFolder("Configuration.mustache");
        String formDataProcessorFolder = findSupportingFileFolder("FormDataProcessor.mustache");
        String headerSelectorFolder = findSupportingFileFolder("HeaderSelector.mustache");
        String objectSerializerFolder = findSupportingFileFolder("ObjectSerializer.mustache");
        String modelInterfaceFolder = findSupportingFileFolder("ModelInterface.mustache");

        supportingFiles.clear();

        supportingFiles.add(new SupportingFile(TEMPLATE_DIR_NAME + "/ApiException.pebble", invokerFolder, "ApiException.php"));
        supportingFiles.add(new SupportingFile(TEMPLATE_DIR_NAME + "/Configuration.pebble", configurationFolder, "Configuration.php"));
        supportingFiles.add(new SupportingFile(TEMPLATE_DIR_NAME + "/FormDataProcessor.pebble", formDataProcessorFolder, "FormDataProcessor.php"));
        supportingFiles.add(new SupportingFile(TEMPLATE_DIR_NAME + "/HeaderSelector.pebble", headerSelectorFolder, "HeaderSelector.php"));
        supportingFiles.add(new SupportingFile(TEMPLATE_DIR_NAME + "/ObjectSerializer.pebble", objectSerializerFolder, "ObjectSerializer.php"));
        supportingFiles.add(new SupportingFile(TEMPLATE_DIR_NAME + "/ModelInterface.pebble", modelInterfaceFolder, "ModelInterface.php"));

        if (!additionalProperties.containsKey(COPYRIGHT_YEAR)) {
            additionalProperties.put(COPYRIGHT_YEAR, "2026");
        }
    }

    private String findSupportingFileFolder(String templateName) {
        for (SupportingFile sf : supportingFiles) {
            if (templateName.equals(sf.getTemplateFile())) {
                return sf.getFolder();
            }
        }
        throw new IllegalStateException(
            "Required supporting file " + templateName + " not registered by parent codegen. "
            + "Maybe the OpenAPI Generator version changed?");
    }

    @Override
    public Map<String, ModelsMap> postProcessAllModels(Map<String, ModelsMap> objs) {
        Map<String, ModelsMap> result = super.postProcessAllModels(objs);
        for (ModelsMap entry : result.values()) {
            for (ModelMap modelMap : entry.getModels()) {
                CodegenModel model = modelMap.getModel();
                // The legacy patch removed the discriminator initialiser block
                // from every generated model. We surface that as a vendor
                // extension so the Pebble template can simply skip the block
                // without resorting to a regex post-processing pass.
                model.getVendorExtensions().put("x-line-skip-discriminator-init", Boolean.TRUE);

                // Pebble can't easily ask "does any var have an inline enum?"
                // mid-template, so precompute it once. This drives the blank
                // line that appears between getModelName() and the container
                // doc block when there are no inline enum constants to emit.
                boolean hasInlineEnumVars = false;
                if (model.vars != null) {
                    for (CodegenProperty v : model.vars) {
                        if (v.isEnum) {
                            hasInlineEnumVars = true;
                            break;
                        }
                    }
                }
                model.getVendorExtensions().put("x-line-has-inline-enum-vars", hasInlineEnumVars);

                if (model.vars != null) {
                    for (CodegenProperty v : model.vars) {
                        if (v.hasValidation) {
                            v.vendorExtensions.put("x-line-maxlength-newline", v.maxLength == null);
                            v.vendorExtensions.put("x-line-maxitems-newline", v.maxItems == null);
                        }
                    }
                }
            }
        }
        return result;
    }

    @Override
    public OperationsMap postProcessOperationsWithModels(OperationsMap objs, List<ModelMap> allModels) {
        OperationsMap result = super.postProcessOperationsWithModels(objs, allModels);
        OperationMap operations = result.getOperations();
        if (operations == null) {
            return result;
        }
        String classname = operations.getClassname();
        for (CodegenOperation op : operations.getOperation()) {
            if (CHANNEL_ACCESS_TOKEN_API_CLASS.equals(classname)
                && STATELESS_OPERATION_ID.equals(op.operationId)) {
                op.vendorExtensions.put(STATELESS_WRAPPERS_VENDOR_EXT, Boolean.TRUE);
                op.vendorExtensions.put(STATELESS_DEPRECATED_VENDOR_EXT, Boolean.TRUE);
            }
        }
        return result;
    }
}
