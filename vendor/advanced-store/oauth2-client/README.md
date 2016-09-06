<a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/4.0/"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-nd/4.0/80x15.png" /></a><br />This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/4.0/">Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License</a>.
<br><br>
<h1>OAuth2 - Client</h1>
<p>
    This Package can be used to connect to an OAuth2-Service and fetch OAuth2 restricted resources.
</p>

<h2>1. Installation</h2>

<h3>via Composer</h3>

<p>
    Update your <code>composer.json</code> "require"-Section with this line:
</p>

<pre>
    <code>
    <b>"advanced-store/oauth2-client": "dev-master"</b>
    </code>
</pre>

<p>
   After adding this line, run the following command:
</p>

<pre>
    <code>
    <b>composer install</b>
    or
    <b>composer update</b>
    </code>
</pre>

<h2>2. Configuration</h2>

<p>
    You need to publish the configuration file of this package with:
</p>

<pre>
    <code>
    <b>php artisan config:publish advanced-store/oauth2-client</b>
    </code>
</pre>

<p>
    The Config-File path is <code>app/config/advanced-store/oauth2-client/config.php</code>
    <br><br>
    Here you have to add/modify the values for your purpose.
    <br>
    [client.id]       - Client-ID of your Application
    <br>
    [client.secret]   - Client-Secret of your Application
    <br>
    [scopes]          - Scopes for your Application (comma separated)

</p>

<p>
    After modifying the Package-Config, update the <code>app/config/app.php</code>
    <br>
    Modify your <b>providers and aliases</b> section, with your preferred alias:<br><br>

    <code><b>'providers' => array('AdvancedStore\Oauth2Client\Oauth2ClientServiceProvider')</b></code>
    <br>
    <code><b>'aliases' => array('OAuth2Client'	=> 'AdvancedStore\Oauth2Client\Facades\Oauth2ClientFacade')</b></code>
</p>

<h2>3. Examples</h2>

<h3>Fetch Access-Token</h3>
<pre>
    <code>
    <b>
    OAuth2Client::fetchAccessToken('http://3rd.party.com/api/access_token', 'password', array(
        'username'	=> $credentials['username'],
        'password'	=> $credentials['password'],
        'scope'		=> Config::get('oauth2-client::scopes'),
    ));
    </b>
    </code>
</pre>

<h3>Requesting a OAuth2 - Restricted Resource/URL</h3>
<pre>
    <code>
    <b>
    $result = OAuth2Client::fetch('http://3rd.party.com/api/restriced/resource');
    </b>
    </code>
</pre>

<h2>4. Constants</h2>

<h4>Auth methods</h4>
<ul>
    <li>AUTH_TYPE_URI</li>
    <li>AUTH_TYPE_AUTHORIZATION_BASIC</li>
    <li>AUTH_TYPE_FORM</li>
</ul>

<h4>Access token types</h4>
<ul>
    <li>ACCESS_TOKEN_URI</li>
    <li>ACCESS_TOKEN_BEARER</li>
    <li>ACCESS_TOKEN_OAUTH</li>
    <li>ACCESS_TOKEN_MAC</li>
</ul>

<h4>Grant types</h4>
<ul>
    <li>GRANT_TYPE_AUTH_CODE</li>
    <li>GRANT_TYPE_PASSWORD</li>
    <li>GRANT_TYPE_CLIENT_CREDENTIALS</li>
    <li>GRANT_TYPE_REFRESH_TOKEN</li>
</ul>

<h4>HTTP Methods</h4>
<ul>
    <li>HTTP_METHOD_GET</li>
    <li>HTTP_METHOD_POST</li>
    <li>HTTP_METHOD_PUT</li>
    <li>HTTP_METHOD_DELETE</li>
    <li>HTTP_METHOD_HEAD</li>
    <li>HTTP_METHOD_PATCH</li>
</ul>

<h4>HTTP Form content types</h4>
<ul>
    <li>HTTP_FORM_CONTENT_TYPE_APPLICATION</li>
    <li>HTTP_FORM_CONTENT_TYPE_MULTIPART</li>
</ul>