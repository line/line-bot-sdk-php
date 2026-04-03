<?php

function patchFile($filename) {
    removeDiscriminatorType($filename);
    addCopyright($filename);
}

function addCopyright($filename)
{
    $content = file_get_contents($filename);
    if (str_contains($content, 'LINE Corporation licenses this file to you under the Apache License')) {
        return;
    }

    $year = date('Y');
    $copyright = <<<COPYRIGHT
/**
 * Copyright $year LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */
COPYRIGHT;
    $content = preg_replace('/<\?php/s', "<?php\n$copyright", $content);
    file_put_contents($filename, $content);
}

function removeDiscriminatorType($filename)
{
    $content = file_get_contents($filename);
    $content = preg_replace("/\n\s+\/\/ Initialize discriminator property with the model name\./s", '', $content);
    $content = preg_replace('/\n\s+\$this->container\[\'type\'\] = static::\$openAPIModelName;/s', '', $content);
    file_put_contents($filename, $content);
}

function addStatelessChannelTokenWrappers()
{
    $filename = __DIR__ . '/../src/clients/channel-access-token/lib/Api/ChannelAccessTokenApi.php';
    $content = file_get_contents($filename);
    if (str_contains($content, 'issueStatelessChannelTokenByJWTAssertion')) {
        return;
    }

    $wrappers = <<<'WRAPPERS'

    /**
     * Issue a stateless channel access token by JWT assertion.
     *
     * @param  string $clientAssertion A JSON Web Token the client needs to create and sign with the private key of the Assertion Signing Key.
     * @param  string $contentType The value for the Content-Type header.
     *
     * @throws \LINE\Clients\ChannelAccessToken\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \LINE\Clients\ChannelAccessToken\Model\IssueStatelessChannelAccessTokenResponse
     */
    public function issueStatelessChannelTokenByJWTAssertion($clientAssertion, string $contentType = self::contentTypes['issueStatelessChannelToken'][0])
    {
        return $this->issueStatelessChannelToken(
            grantType: 'client_credentials',
            clientAssertionType: 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
            clientAssertion: $clientAssertion,
            contentType: $contentType,
        );
    }

    /**
     * Issue a stateless channel access token by client secret.
     *
     * @param  string $clientId Channel ID.
     * @param  string $clientSecret Channel secret.
     * @param  string $contentType The value for the Content-Type header.
     *
     * @throws \LINE\Clients\ChannelAccessToken\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \LINE\Clients\ChannelAccessToken\Model\IssueStatelessChannelAccessTokenResponse
     */
    public function issueStatelessChannelTokenByClientSecret($clientId, $clientSecret, string $contentType = self::contentTypes['issueStatelessChannelToken'][0])
    {
        return $this->issueStatelessChannelToken(
            grantType: 'client_credentials',
            clientId: $clientId,
            clientSecret: $clientSecret,
            contentType: $contentType,
        );
    }

    /**
     * Issue a stateless channel access token by JWT assertion (with HTTP info).
     *
     * @param  string $clientAssertion A JSON Web Token the client needs to create and sign with the private key of the Assertion Signing Key.
     * @param  string $contentType The value for the Content-Type header.
     *
     * @throws \LINE\Clients\ChannelAccessToken\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \LINE\Clients\ChannelAccessToken\Model\IssueStatelessChannelAccessTokenResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function issueStatelessChannelTokenByJWTAssertionWithHttpInfo($clientAssertion, string $contentType = self::contentTypes['issueStatelessChannelToken'][0])
    {
        return $this->issueStatelessChannelTokenWithHttpInfo(
            grantType: 'client_credentials',
            clientAssertionType: 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
            clientAssertion: $clientAssertion,
            contentType: $contentType,
        );
    }

    /**
     * Issue a stateless channel access token by client secret (with HTTP info).
     *
     * @param  string $clientId Channel ID.
     * @param  string $clientSecret Channel secret.
     * @param  string $contentType The value for the Content-Type header.
     *
     * @throws \LINE\Clients\ChannelAccessToken\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \LINE\Clients\ChannelAccessToken\Model\IssueStatelessChannelAccessTokenResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function issueStatelessChannelTokenByClientSecretWithHttpInfo($clientId, $clientSecret, string $contentType = self::contentTypes['issueStatelessChannelToken'][0])
    {
        return $this->issueStatelessChannelTokenWithHttpInfo(
            grantType: 'client_credentials',
            clientId: $clientId,
            clientSecret: $clientSecret,
            contentType: $contentType,
        );
    }
WRAPPERS;

    // Insert before the closing brace of the class
    $content = preg_replace('/\n}\s*$/', $wrappers . "\n}\n", $content);
    file_put_contents($filename, $content);
}

$recursive_directory_iterator = new \RecursiveDirectoryIterator(
    __DIR__ . '/../src/',
    \FilesystemIterator::SKIP_DOTS
    | \FilesystemIterator::KEY_AS_PATHNAME
    | \FilesystemIterator::CURRENT_AS_FILEINFO
);
$iterator = new RecursiveIteratorIterator($recursive_directory_iterator, RecursiveIteratorIterator::LEAVES_ONLY);
foreach ($iterator as $filename) {
    if (!str_ends_with($filename, '.php')) {
        continue;
    }
    echo $filename;
    patchFile($filename);
}

addStatelessChannelTokenWrappers();
