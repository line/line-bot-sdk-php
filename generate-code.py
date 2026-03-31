import os
import subprocess
import sys

TEMPLATE_DIR = "tools/custom-template"
COMPONENTS = [
    {"sourceYaml": "channel-access-token.yml", "invokerPackage": "LINE\\Clients\\ChannelAccessToken"},
    {"sourceYaml": "insight.yml", "invokerPackage": "LINE\\Clients\\Insight"},
    {"sourceYaml": "manage-audience.yml", "invokerPackage": "LINE\\Clients\\ManageAudience"},
    {"sourceYaml": "messaging-api.yml", "invokerPackage": "LINE\\Clients\\MessagingApi"},
    {"sourceYaml": "liff.yml", "invokerPackage": "LINE\\Clients\\Liff"},
]


def run_command(command):
    print(command)
    proc = subprocess.run(command, shell=True, text=True, capture_output=True)
    if len(proc.stdout) != 0:
        print("\n\nSTDOUT:\n\n")
        print(proc.stdout)
    if len(proc.stderr) != 0:
        print("\n\nSTDERR:\n\n")
        print(proc.stderr)
        print("\n\n")
    if proc.returncode != 0:
        print(f"\n\nCommand '{command}' returned non-zero exit status {proc.returncode}.")
        sys.exit(1)
    return proc.stdout.strip()


def generate_clients(jar_path):
    for component in COMPONENTS:
        source_yaml = component["sourceYaml"]
        schema = source_yaml.replace(".yml", "")
        output_path = f"src/clients/{schema}"

        run_command(f"rm -rf {output_path}")
        run_command(f"mkdir {output_path}")
        run_command(f"cp tools/.openapi-generator-ignore {output_path}/")

        command = f"""java -jar {jar_path} generate \
            -i line-openapi/{source_yaml} \
            -g php \
            -o {output_path} \
            --template-dir {TEMPLATE_DIR} \
            --http-user-agent LINE-BotSDK-PHP/11 \
            --additional-properties="invokerPackage={component['invokerPackage']}" \
            --additional-properties="variableNamingConvention=camelCase"
        """
        run_command(command)


def generate_webhook(jar_path):
    output_path = "src/webhook"

    run_command(f"rm -rf {output_path}")

    command = f"""java -jar {jar_path} generate \
        -i line-openapi/webhook.yml \
        -g php \
        -o {output_path} \
        --template-dir {TEMPLATE_DIR} \
        --additional-properties="invokerPackage=LINE\\Webhook" \
        --additional-properties="variableNamingConvention=camelCase"
    """
    run_command(command)


def post_process():
    run_command("php tools/patch-gen-oas-client.php")


def main():
    os.chdir(os.path.dirname(os.path.abspath(__file__)))

    os.chdir("generator")
    run_command('mvn package -DskipTests=true')
    os.chdir("..")

    jar_path = "generator/target/openapi-generator-cli.jar"
    generate_clients(jar_path)
    generate_webhook(jar_path)
    post_process()


if __name__ == "__main__":
    main()
