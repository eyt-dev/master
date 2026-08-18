<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Add2Farm API Documentation</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "http://admin.eyt.test";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.11.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.11.0.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduction" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduction">
                    <a href="#introduction">Introduction</a>
                </li>
                            </ul>
                    <ul id="tocify-header-authenticating-requests" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authenticating-requests">
                    <a href="#authenticating-requests">Authenticating requests</a>
                </li>
                            </ul>
                    <ul id="tocify-header-add2farm-authentication" class="tocify-header">
                <li class="tocify-item level-1" data-unique="add2farm-authentication">
                    <a href="#add2farm-authentication">Add2Farm Authentication</a>
                </li>
                                    <ul id="tocify-subheader-add2farm-authentication" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="add2farm-authentication-POSTapi-add2farm-auth-register">
                                <a href="#add2farm-authentication-POSTapi-add2farm-auth-register">Register a new user</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-authentication-POSTapi-add2farm-auth-login">
                                <a href="#add2farm-authentication-POSTapi-add2farm-auth-login">Login user</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-authentication-POSTapi-add2farm-auth-verify-otp">
                                <a href="#add2farm-authentication-POSTapi-add2farm-auth-verify-otp">Verify OTP</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-authentication-POSTapi-add2farm-auth-resend-otp">
                                <a href="#add2farm-authentication-POSTapi-add2farm-auth-resend-otp">Resend OTP</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-authentication-POSTapi-add2farm-auth-forgot-password">
                                <a href="#add2farm-authentication-POSTapi-add2farm-auth-forgot-password">Forgot Password - Step 1</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-authentication-POSTapi-add2farm-auth-verify-otp">
                                <a href="#add2farm-authentication-POSTapi-add2farm-auth-verify-otp">Verify OTP - Step 2 (2FA)</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-authentication-POSTapi-add2farm-auth-reset-password">
                                <a href="#add2farm-authentication-POSTapi-add2farm-auth-reset-password">Reset Password - Step 3</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-add2farm-daily-records" class="tocify-header">
                <li class="tocify-item level-1" data-unique="add2farm-daily-records">
                    <a href="#add2farm-daily-records">Add2Farm Daily Records</a>
                </li>
                                    <ul id="tocify-subheader-add2farm-daily-records" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="add2farm-daily-records-GETapi-add2farm-daily-records">
                                <a href="#add2farm-daily-records-GETapi-add2farm-daily-records">List all daily records</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-daily-records-POSTapi-add2farm-daily-records">
                                <a href="#add2farm-daily-records-POSTapi-add2farm-daily-records">Create a new daily record</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-daily-records-GETapi-add2farm-daily-records--daily_record-">
                                <a href="#add2farm-daily-records-GETapi-add2farm-daily-records--daily_record-">Get a single daily record</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-daily-records-PUTapi-add2farm-daily-records--daily_record-">
                                <a href="#add2farm-daily-records-PUTapi-add2farm-daily-records--daily_record-">Update a daily record</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-daily-records-DELETEapi-add2farm-daily-records--daily_record-">
                                <a href="#add2farm-daily-records-DELETEapi-add2farm-daily-records--daily_record-">Delete a daily record</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-add2farm-dropdowns" class="tocify-header">
                <li class="tocify-item level-1" data-unique="add2farm-dropdowns">
                    <a href="#add2farm-dropdowns">Add2Farm Dropdowns</a>
                </li>
                                    <ul id="tocify-subheader-add2farm-dropdowns" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="add2farm-dropdowns-GETapi-add2farm-dropdowns-farms">
                                <a href="#add2farm-dropdowns-GETapi-add2farm-dropdowns-farms">Get farms dropdown list</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-dropdowns-GETapi-add2farm-dropdowns-suppliers">
                                <a href="#add2farm-dropdowns-GETapi-add2farm-dropdowns-suppliers">Get suppliers dropdown list</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-dropdowns-GETapi-add2farm-dropdowns-supervisors">
                                <a href="#add2farm-dropdowns-GETapi-add2farm-dropdowns-supervisors">Get supervisors dropdown list</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-add2farm-farmers-type-4" class="tocify-header">
                <li class="tocify-item level-1" data-unique="add2farm-farmers-type-4">
                    <a href="#add2farm-farmers-type-4">Add2Farm Farmers (Type 4)</a>
                </li>
                                    <ul id="tocify-subheader-add2farm-farmers-type-4" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="add2farm-farmers-type-4-GETapi-add2farm-farmers">
                                <a href="#add2farm-farmers-type-4-GETapi-add2farm-farmers">List all farmers</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-farmers-type-4-POSTapi-add2farm-farmers">
                                <a href="#add2farm-farmers-type-4-POSTapi-add2farm-farmers">Create a new farmer</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-farmers-type-4-GETapi-add2farm-farmers--farmer-">
                                <a href="#add2farm-farmers-type-4-GETapi-add2farm-farmers--farmer-">Get a single farmer</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-farmers-type-4-PUTapi-add2farm-farmers--farmer-">
                                <a href="#add2farm-farmers-type-4-PUTapi-add2farm-farmers--farmer-">Update a farmer</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-farmers-type-4-DELETEapi-add2farm-farmers--farmer-">
                                <a href="#add2farm-farmers-type-4-DELETEapi-add2farm-farmers--farmer-">Delete a farmer</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-add2farm-farms" class="tocify-header">
                <li class="tocify-item level-1" data-unique="add2farm-farms">
                    <a href="#add2farm-farms">Add2Farm Farms</a>
                </li>
                                    <ul id="tocify-subheader-add2farm-farms" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="add2farm-farms-GETapi-add2farm-farms">
                                <a href="#add2farm-farms-GETapi-add2farm-farms">List all farms</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-farms-POSTapi-add2farm-farms">
                                <a href="#add2farm-farms-POSTapi-add2farm-farms">Create a new farm</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-farms-GETapi-add2farm-farms--farm-">
                                <a href="#add2farm-farms-GETapi-add2farm-farms--farm-">Get a single farm</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-farms-PUTapi-add2farm-farms--farm-">
                                <a href="#add2farm-farms-PUTapi-add2farm-farms--farm-">Update a farm</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-farms-DELETEapi-add2farm-farms--farm-">
                                <a href="#add2farm-farms-DELETEapi-add2farm-farms--farm-">Delete a farm</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-add2farm-flocks" class="tocify-header">
                <li class="tocify-item level-1" data-unique="add2farm-flocks">
                    <a href="#add2farm-flocks">Add2Farm Flocks</a>
                </li>
                                    <ul id="tocify-subheader-add2farm-flocks" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="add2farm-flocks-GETapi-add2farm-flocks">
                                <a href="#add2farm-flocks-GETapi-add2farm-flocks">List all flocks</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-flocks-POSTapi-add2farm-flocks">
                                <a href="#add2farm-flocks-POSTapi-add2farm-flocks">Create a new flock</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-flocks-GETapi-add2farm-flocks--flock-">
                                <a href="#add2farm-flocks-GETapi-add2farm-flocks--flock-">Get a single flock</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-flocks-PUTapi-add2farm-flocks--flock-">
                                <a href="#add2farm-flocks-PUTapi-add2farm-flocks--flock-">Update a flock</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-flocks-DELETEapi-add2farm-flocks--flock-">
                                <a href="#add2farm-flocks-DELETEapi-add2farm-flocks--flock-">Delete a flock</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-add2farm-profile" class="tocify-header">
                <li class="tocify-item level-1" data-unique="add2farm-profile">
                    <a href="#add2farm-profile">Add2Farm Profile</a>
                </li>
                                    <ul id="tocify-subheader-add2farm-profile" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="add2farm-profile-GETapi-add2farm-profile">
                                <a href="#add2farm-profile-GETapi-add2farm-profile">Get user profile</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-profile-PUTapi-add2farm-profile">
                                <a href="#add2farm-profile-PUTapi-add2farm-profile">Update user profile</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-profile-GETapi-add2farm-profile-change-password">
                                <a href="#add2farm-profile-GETapi-add2farm-profile-change-password">Change password</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-add2farm-supervisors-type-3" class="tocify-header">
                <li class="tocify-item level-1" data-unique="add2farm-supervisors-type-3">
                    <a href="#add2farm-supervisors-type-3">Add2Farm Supervisors (Type 3)</a>
                </li>
                                    <ul id="tocify-subheader-add2farm-supervisors-type-3" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="add2farm-supervisors-type-3-GETapi-add2farm-supervisors">
                                <a href="#add2farm-supervisors-type-3-GETapi-add2farm-supervisors">List all supervisors</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-supervisors-type-3-POSTapi-add2farm-supervisors">
                                <a href="#add2farm-supervisors-type-3-POSTapi-add2farm-supervisors">Create a new supervisor</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-supervisors-type-3-GETapi-add2farm-supervisors--supervisor-">
                                <a href="#add2farm-supervisors-type-3-GETapi-add2farm-supervisors--supervisor-">Get a single supervisor</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-supervisors-type-3-PUTapi-add2farm-supervisors--supervisor-">
                                <a href="#add2farm-supervisors-type-3-PUTapi-add2farm-supervisors--supervisor-">Update a supervisor</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="add2farm-supervisors-type-3-DELETEapi-add2farm-supervisors--supervisor-">
                                <a href="#add2farm-supervisors-type-3-DELETEapi-add2farm-supervisors--supervisor-">Delete a supervisor</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: August 12, 2026</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<p>Complete API documentation for Add2Farm authentication and user management system.</p>
<aside>
    <strong>Base URL</strong>: <code>http://admin.eyt.test</code>
</aside>
<pre><code>Welcome to the Add2Farm API documentation. This documentation provides all the information you need to integrate with our API.

&lt;aside&gt;As you scroll, you'll see code examples for working with the API in different programming languages in the dark area to the right (or as part of the content on mobile).
You can switch the language used with the tabs at the top right (or from the nav menu at the top left on mobile).&lt;/aside&gt;

## Authentication
The Add2Farm API uses Sanctum token-based authentication. Register or login to obtain your access token, then include it in the Authorization header of all protected requests.</code></pre>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>To authenticate requests, include an <strong><code>Authorization</code></strong> header with the value <strong><code>"Bearer {YOUR_AUTH_TOKEN}"</code></strong>.</p>
<p>All authenticated endpoints are marked with a <code>requires authentication</code> badge in the documentation below.</p>
<p>You can retrieve your token by registering or logging in at the <code>/api/add2farm/auth/register</code> or <code>/api/add2farm/auth/login</code> endpoints.</p>

        <h1 id="add2farm-authentication">Add2Farm Authentication</h1>

    

                                <h2 id="add2farm-authentication-POSTapi-add2farm-auth-register">Register a new user</h2>

<p>
</p>

<p>Register a new user account for Add2Farm mobile/web application.</p>

<span id="example-requests-POSTapi-add2farm-auth-register">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://admin.eyt.test/api/add2farm/auth/register" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"mobile_number\": \"+1234567890\",
    \"name\": \"John Doe\",
    \"password\": \"password123\",
    \"password_confirmation\": \"password123\",
    \"type\": 2
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/auth/register"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "mobile_number": "+1234567890",
    "name": "John Doe",
    "password": "password123",
    "password_confirmation": "password123",
    "type": 2
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-add2farm-auth-register">
            <blockquote>
            <p>Example response (201):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;OTP sent successfully.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;errors&quot;: {
        &quot;mobile_number&quot;: [
            &quot;The mobile number has already been taken.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-add2farm-auth-register" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-add2farm-auth-register"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-add2farm-auth-register"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-add2farm-auth-register" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-add2farm-auth-register">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-add2farm-auth-register" data-method="POST"
      data-path="api/add2farm/auth/register"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-add2farm-auth-register', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-add2farm-auth-register"
                    onclick="tryItOut('POSTapi-add2farm-auth-register');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-add2farm-auth-register"
                    onclick="cancelTryOut('POSTapi-add2farm-auth-register');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-add2farm-auth-register"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/add2farm/auth/register</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-add2farm-auth-register"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-add2farm-auth-register"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>mobile_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="mobile_number"                data-endpoint="POSTapi-add2farm-auth-register"
               value="+1234567890"
               data-component="body">
    <br>
<p>User mobile number with country code. Must be unique. Example: <code>+1234567890</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-add2farm-auth-register"
               value="John Doe"
               data-component="body">
    <br>
<p>User full name. Optional, defaults to Add2Farm User. Example: <code>John Doe</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-add2farm-auth-register"
               value="password123"
               data-component="body">
    <br>
<p>Password (minimum 8 characters). Example: <code>password123</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password_confirmation</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password_confirmation"                data-endpoint="POSTapi-add2farm-auth-register"
               value="password123"
               data-component="body">
    <br>
<p>Password confirmation. Example: <code>password123</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="type"                data-endpoint="POSTapi-add2farm-auth-register"
               value="2"
               data-component="body">
    <br>
<p>User type: 1=SuperAdmin, 2=FarmOwner. Example: <code>2</code></p>
        </div>
        </form>

                    <h2 id="add2farm-authentication-POSTapi-add2farm-auth-login">Login user</h2>

<p>
</p>

<p>Login to Add2Farm with mobile number and password.</p>

<span id="example-requests-POSTapi-add2farm-auth-login">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://admin.eyt.test/api/add2farm/auth/login" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"mobile_number\": \"+1234567890\",
    \"password\": \"password123\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/auth/login"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "mobile_number": "+1234567890",
    "password": "password123"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-add2farm-auth-login">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Login successful.&quot;,
    &quot;data&quot;: {
        &quot;user&quot;: {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;John Doe&quot;,
            &quot;mobile_number&quot;: &quot;+1234567890&quot;,
            &quot;type&quot;: 2,
            &quot;type_label&quot;: &quot;Farm Owner&quot;,
            &quot;status&quot;: &quot;Active&quot;
        },
        &quot;token&quot;: &quot;1|abcdefghijklmnopqrstuvwxyz123456789&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-add2farm-auth-login" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-add2farm-auth-login"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-add2farm-auth-login"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-add2farm-auth-login" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-add2farm-auth-login">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-add2farm-auth-login" data-method="POST"
      data-path="api/add2farm/auth/login"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-add2farm-auth-login', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-add2farm-auth-login"
                    onclick="tryItOut('POSTapi-add2farm-auth-login');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-add2farm-auth-login"
                    onclick="cancelTryOut('POSTapi-add2farm-auth-login');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-add2farm-auth-login"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/add2farm/auth/login</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-add2farm-auth-login"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-add2farm-auth-login"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>mobile_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="mobile_number"                data-endpoint="POSTapi-add2farm-auth-login"
               value="+1234567890"
               data-component="body">
    <br>
<p>User mobile number with country code. Example: <code>+1234567890</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-add2farm-auth-login"
               value="password123"
               data-component="body">
    <br>
<p>User password. Example: <code>password123</code></p>
        </div>
        </form>

                    <h2 id="add2farm-authentication-POSTapi-add2farm-auth-verify-otp">Verify OTP</h2>

<p>
</p>

<p>Verify OTP code sent to user mobile number during registration or password reset.</p>

<span id="example-requests-POSTapi-add2farm-auth-verify-otp">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://admin.eyt.test/api/add2farm/auth/verify-otp" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"mobile_number\": \"+1234567890\",
    \"otp\": \"123456\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/auth/verify-otp"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "mobile_number": "+1234567890",
    "otp": "123456"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-add2farm-auth-verify-otp">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;OTP verified successfully.&quot;,
    &quot;data&quot;: {
        &quot;user&quot;: {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;John Doe&quot;,
            &quot;mobile_number&quot;: &quot;+1234567890&quot;,
            &quot;type&quot;: 2,
            &quot;type_label&quot;: &quot;Farm Owner&quot;,
            &quot;status&quot;: &quot;Active&quot;
        },
        &quot;token&quot;: &quot;1|abcdefghijklmnopqrstuvwxyz123456789&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-add2farm-auth-verify-otp" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-add2farm-auth-verify-otp"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-add2farm-auth-verify-otp"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-add2farm-auth-verify-otp" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-add2farm-auth-verify-otp">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-add2farm-auth-verify-otp" data-method="POST"
      data-path="api/add2farm/auth/verify-otp"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-add2farm-auth-verify-otp', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-add2farm-auth-verify-otp"
                    onclick="tryItOut('POSTapi-add2farm-auth-verify-otp');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-add2farm-auth-verify-otp"
                    onclick="cancelTryOut('POSTapi-add2farm-auth-verify-otp');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-add2farm-auth-verify-otp"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/add2farm/auth/verify-otp</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-add2farm-auth-verify-otp"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-add2farm-auth-verify-otp"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>mobile_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="mobile_number"                data-endpoint="POSTapi-add2farm-auth-verify-otp"
               value="+1234567890"
               data-component="body">
    <br>
<p>User mobile number. Example: <code>+1234567890</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>otp</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="otp"                data-endpoint="POSTapi-add2farm-auth-verify-otp"
               value="123456"
               data-component="body">
    <br>
<p>OTP code (6 digits). Example: <code>123456</code></p>
        </div>
        </form>

                    <h2 id="add2farm-authentication-POSTapi-add2farm-auth-resend-otp">Resend OTP</h2>

<p>
</p>

<p>Resend OTP code to user mobile number.</p>

<span id="example-requests-POSTapi-add2farm-auth-resend-otp">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://admin.eyt.test/api/add2farm/auth/resend-otp" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"mobile_number\": \"+1234567890\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/auth/resend-otp"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "mobile_number": "+1234567890"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-add2farm-auth-resend-otp">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;OTP resent successfully.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-add2farm-auth-resend-otp" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-add2farm-auth-resend-otp"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-add2farm-auth-resend-otp"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-add2farm-auth-resend-otp" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-add2farm-auth-resend-otp">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-add2farm-auth-resend-otp" data-method="POST"
      data-path="api/add2farm/auth/resend-otp"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-add2farm-auth-resend-otp', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-add2farm-auth-resend-otp"
                    onclick="tryItOut('POSTapi-add2farm-auth-resend-otp');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-add2farm-auth-resend-otp"
                    onclick="cancelTryOut('POSTapi-add2farm-auth-resend-otp');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-add2farm-auth-resend-otp"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/add2farm/auth/resend-otp</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-add2farm-auth-resend-otp"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-add2farm-auth-resend-otp"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>mobile_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="mobile_number"                data-endpoint="POSTapi-add2farm-auth-resend-otp"
               value="+1234567890"
               data-component="body">
    <br>
<p>User mobile number. Example: <code>+1234567890</code></p>
        </div>
        </form>

                    <h2 id="add2farm-authentication-POSTapi-add2farm-auth-forgot-password">Forgot Password - Step 1</h2>

<p>
</p>

<p>Request password reset by providing mobile number. An OTP will be sent to verify identity before allowing password change.</p>

<span id="example-requests-POSTapi-add2farm-auth-forgot-password">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://admin.eyt.test/api/add2farm/auth/forgot-password" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"mobile_number\": \"+1234567890\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/auth/forgot-password"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "mobile_number": "+1234567890"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-add2farm-auth-forgot-password">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;OTP sent to your mobile number. Please verify it to reset your password.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;User not found.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-add2farm-auth-forgot-password" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-add2farm-auth-forgot-password"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-add2farm-auth-forgot-password"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-add2farm-auth-forgot-password" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-add2farm-auth-forgot-password">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-add2farm-auth-forgot-password" data-method="POST"
      data-path="api/add2farm/auth/forgot-password"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-add2farm-auth-forgot-password', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-add2farm-auth-forgot-password"
                    onclick="tryItOut('POSTapi-add2farm-auth-forgot-password');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-add2farm-auth-forgot-password"
                    onclick="cancelTryOut('POSTapi-add2farm-auth-forgot-password');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-add2farm-auth-forgot-password"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/add2farm/auth/forgot-password</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-add2farm-auth-forgot-password"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-add2farm-auth-forgot-password"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>mobile_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="mobile_number"                data-endpoint="POSTapi-add2farm-auth-forgot-password"
               value="+1234567890"
               data-component="body">
    <br>
<p>User mobile number. Example: <code>+1234567890</code></p>
        </div>
        </form>

                    <h2 id="add2farm-authentication-POSTapi-add2farm-auth-verify-otp">Verify OTP - Step 2 (2FA)</h2>

<p>
</p>

<p>Verify OTP code sent during password reset or registration. This is required before you can reset your password or complete registration.</p>

<span id="example-requests-POSTapi-add2farm-auth-verify-otp">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://admin.eyt.test/api/add2farm/auth/verify-otp" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"mobile_number\": \"+1234567890\",
    \"otp\": \"123456\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/auth/verify-otp"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "mobile_number": "+1234567890",
    "otp": "123456"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-add2farm-auth-verify-otp">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;OTP verified successfully.&quot;,
    &quot;data&quot;: {
        &quot;user&quot;: {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;John Doe&quot;,
            &quot;mobile_number&quot;: &quot;+1234567890&quot;,
            &quot;type&quot;: 2,
            &quot;type_label&quot;: &quot;Farm Owner&quot;,
            &quot;status&quot;: &quot;Active&quot;
        },
        &quot;token&quot;: &quot;1|abcdefghijklmnopqrstuvwxyz123456789&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Invalid or expired OTP.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-add2farm-auth-verify-otp" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-add2farm-auth-verify-otp"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-add2farm-auth-verify-otp"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-add2farm-auth-verify-otp" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-add2farm-auth-verify-otp">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-add2farm-auth-verify-otp" data-method="POST"
      data-path="api/add2farm/auth/verify-otp"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-add2farm-auth-verify-otp', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-add2farm-auth-verify-otp"
                    onclick="tryItOut('POSTapi-add2farm-auth-verify-otp');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-add2farm-auth-verify-otp"
                    onclick="cancelTryOut('POSTapi-add2farm-auth-verify-otp');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-add2farm-auth-verify-otp"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/add2farm/auth/verify-otp</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-add2farm-auth-verify-otp"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-add2farm-auth-verify-otp"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>mobile_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="mobile_number"                data-endpoint="POSTapi-add2farm-auth-verify-otp"
               value="+1234567890"
               data-component="body">
    <br>
<p>User mobile number. Example: <code>+1234567890</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>otp</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="otp"                data-endpoint="POSTapi-add2farm-auth-verify-otp"
               value="123456"
               data-component="body">
    <br>
<p>OTP code (6 digits) sent to mobile number. Required for 2FA. Example: <code>123456</code></p>
        </div>
        </form>

                    <h2 id="add2farm-authentication-POSTapi-add2farm-auth-reset-password">Reset Password - Step 3</h2>

<p>
</p>

<p>Reset password using token from OTP verification. No mobile number needed - token identifies the user.</p>

<span id="example-requests-POSTapi-add2farm-auth-reset-password">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://admin.eyt.test/api/add2farm/auth/reset-password" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"token\": \"1|abcdefghijklmnopqrstuvwxyz123456789\",
    \"password\": \"newpassword123\",
    \"password_confirmation\": \"newpassword123\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/auth/reset-password"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "token": "1|abcdefghijklmnopqrstuvwxyz123456789",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-add2farm-auth-reset-password">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Password reset successfully. Please login with new password.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Invalid or expired token. Please verify OTP first.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;User not found.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;errors&quot;: {
        &quot;password&quot;: [
            &quot;The password confirmation does not match.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-add2farm-auth-reset-password" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-add2farm-auth-reset-password"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-add2farm-auth-reset-password"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-add2farm-auth-reset-password" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-add2farm-auth-reset-password">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-add2farm-auth-reset-password" data-method="POST"
      data-path="api/add2farm/auth/reset-password"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-add2farm-auth-reset-password', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-add2farm-auth-reset-password"
                    onclick="tryItOut('POSTapi-add2farm-auth-reset-password');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-add2farm-auth-reset-password"
                    onclick="cancelTryOut('POSTapi-add2farm-auth-reset-password');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-add2farm-auth-reset-password"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/add2farm/auth/reset-password</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-add2farm-auth-reset-password"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-add2farm-auth-reset-password"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>token</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="token"                data-endpoint="POSTapi-add2farm-auth-reset-password"
               value="1|abcdefghijklmnopqrstuvwxyz123456789"
               data-component="body">
    <br>
<p>Token received from verify-otp endpoint (Step 2). Identifies the user - no mobile number needed. Example: <code>1|abcdefghijklmnopqrstuvwxyz123456789</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-add2farm-auth-reset-password"
               value="newpassword123"
               data-component="body">
    <br>
<p>New password (minimum 8 characters). Example: <code>newpassword123</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password_confirmation</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password_confirmation"                data-endpoint="POSTapi-add2farm-auth-reset-password"
               value="newpassword123"
               data-component="body">
    <br>
<p>New password confirmation. Example: <code>newpassword123</code></p>
        </div>
        </form>

                <h1 id="add2farm-daily-records">Add2Farm Daily Records</h1>

    

                                <h2 id="add2farm-daily-records-GETapi-add2farm-daily-records">List all daily records</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Get paginated list of all daily records with search and filtering.</p>

<span id="example-requests-GETapi-add2farm-daily-records">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://admin.eyt.test/api/add2farm/daily-records?page=1&amp;per_page=20&amp;flock_id=1&amp;farm_id=1&amp;record_date=1786060800" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/daily-records"
);

const params = {
    "page": "1",
    "per_page": "20",
    "flock_id": "1",
    "farm_id": "1",
    "record_date": "1786060800",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-add2farm-daily-records">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Daily records retrieved successfully.&quot;,
    &quot;data&quot;: {
        &quot;current_page&quot;: 1,
        &quot;data&quot;: [
            {
                &quot;id&quot;: 1,
                &quot;record_date&quot;: &quot;2026-08-07&quot;,
                &quot;farm_id&quot;: 1,
                &quot;farm_name&quot;: &quot;Main Farm&quot;,
                &quot;flock_id&quot;: 1,
                &quot;flock_name&quot;: &quot;Farm1-Flock4&quot;,
                &quot;hangar_id&quot;: 1,
                &quot;hangar_name&quot;: &quot;Farm1-Hangar1&quot;,
                &quot;feed_kg&quot;: 450.5,
                &quot;eggs_tray_30&quot;: 12,
                &quot;eggs_count&quot;: 360,
                &quot;eggs_weight&quot;: 18.5,
                &quot;chicks_weight&quot;: 1.85,
                &quot;mortality&quot;: 5
            }
        ],
        &quot;total&quot;: 50,
        &quot;last_page&quot;: 3
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-add2farm-daily-records" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-add2farm-daily-records"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-add2farm-daily-records"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-add2farm-daily-records" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-add2farm-daily-records">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-add2farm-daily-records" data-method="GET"
      data-path="api/add2farm/daily-records"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-add2farm-daily-records', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-add2farm-daily-records"
                    onclick="tryItOut('GETapi-add2farm-daily-records');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-add2farm-daily-records"
                    onclick="cancelTryOut('GETapi-add2farm-daily-records');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-add2farm-daily-records"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/add2farm/daily-records</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-add2farm-daily-records"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-add2farm-daily-records"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-add2farm-daily-records"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-add2farm-daily-records"
               value="1"
               data-component="query">
    <br>
<p>optional Pagination page number. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-add2farm-daily-records"
               value="20"
               data-component="query">
    <br>
<p>optional Items per page. Default: 15. Example: <code>20</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>flock_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="flock_id"                data-endpoint="GETapi-add2farm-daily-records"
               value="1"
               data-component="query">
    <br>
<p>optional Filter by flock ID. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>farm_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="farm_id"                data-endpoint="GETapi-add2farm-daily-records"
               value="1"
               data-component="query">
    <br>
<p>optional Filter by farm ID. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>record_date</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="record_date"                data-endpoint="GETapi-add2farm-daily-records"
               value="1786060800"
               data-component="query">
    <br>
<p>optional Filter by record date (format: yyyy-mm-dd). Example: <code>1786060800</code></p>
            </div>
                </form>

                    <h2 id="add2farm-daily-records-POSTapi-add2farm-daily-records">Create a new daily record</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Create a new daily record for a flock/hangar combination.</p>

<span id="example-requests-POSTapi-add2farm-daily-records">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://admin.eyt.test/api/add2farm/daily-records" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"record_date\": \"07-08-2026\",
    \"flock_id\": 1,
    \"hangar_id\": 1,
    \"feed_kg\": 450.5,
    \"eggs_tray_30\": 12,
    \"eggs_count\": 360,
    \"eggs_weight\": 18.5,
    \"chicks_weight\": 1.85,
    \"mortality\": 5
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/daily-records"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "record_date": "07-08-2026",
    "flock_id": 1,
    "hangar_id": 1,
    "feed_kg": 450.5,
    "eggs_tray_30": 12,
    "eggs_count": 360,
    "eggs_weight": 18.5,
    "chicks_weight": 1.85,
    "mortality": 5
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-add2farm-daily-records">
            <blockquote>
            <p>Example response (201):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Daily record created successfully.&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;record_date&quot;: &quot;2026-08-07&quot;,
        &quot;farm_id&quot;: 1,
        &quot;farm_name&quot;: &quot;Main Farm&quot;,
        &quot;flock_id&quot;: 1,
        &quot;flock_name&quot;: &quot;Farm1-Flock4&quot;,
        &quot;hangar_id&quot;: 1,
        &quot;hangar_name&quot;: &quot;Farm1-Hangar1&quot;,
        &quot;feed_kg&quot;: 450.5,
        &quot;eggs_tray_30&quot;: 12,
        &quot;eggs_count&quot;: 360,
        &quot;eggs_weight&quot;: 18.5,
        &quot;chicks_weight&quot;: 1.85,
        &quot;mortality&quot;: 5,
        &quot;created_by&quot;: 1,
        &quot;created_by_name&quot;: &quot;Admin Name&quot;,
        &quot;created_at&quot;: &quot;2026-08-07T10:30:00Z&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-add2farm-daily-records" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-add2farm-daily-records"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-add2farm-daily-records"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-add2farm-daily-records" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-add2farm-daily-records">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-add2farm-daily-records" data-method="POST"
      data-path="api/add2farm/daily-records"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-add2farm-daily-records', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-add2farm-daily-records"
                    onclick="tryItOut('POSTapi-add2farm-daily-records');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-add2farm-daily-records"
                    onclick="cancelTryOut('POSTapi-add2farm-daily-records');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-add2farm-daily-records"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/add2farm/daily-records</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-add2farm-daily-records"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-add2farm-daily-records"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-add2farm-daily-records"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>record_date</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="record_date"                data-endpoint="POSTapi-add2farm-daily-records"
               value="07-08-2026"
               data-component="body">
    <br>
<p>Record date (format: dd-mm-yyyy). Example: <code>07-08-2026</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>flock_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="flock_id"                data-endpoint="POSTapi-add2farm-daily-records"
               value="1"
               data-component="body">
    <br>
<p>Flock ID. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>hangar_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="hangar_id"                data-endpoint="POSTapi-add2farm-daily-records"
               value="1"
               data-component="body">
    <br>
<p>Hangar ID. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>feed_kg</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="feed_kg"                data-endpoint="POSTapi-add2farm-daily-records"
               value="450.5"
               data-component="body">
    <br>
<p>Feed quantity in kg. Example: <code>450.5</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>eggs_tray_30</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="eggs_tray_30"                data-endpoint="POSTapi-add2farm-daily-records"
               value="12"
               data-component="body">
    <br>
<p>Number of egg trays (30 count). Example: <code>12</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>eggs_count</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="eggs_count"                data-endpoint="POSTapi-add2farm-daily-records"
               value="360"
               data-component="body">
    <br>
<p>Egg count. Example: <code>360</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>eggs_weight</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="eggs_weight"                data-endpoint="POSTapi-add2farm-daily-records"
               value="18.5"
               data-component="body">
    <br>
<p>Eggs weight in kg. Example: <code>18.5</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>chicks_weight</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="chicks_weight"                data-endpoint="POSTapi-add2farm-daily-records"
               value="1.85"
               data-component="body">
    <br>
<p>Chicks weight in kg. Example: <code>1.85</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>mortality</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="mortality"                data-endpoint="POSTapi-add2farm-daily-records"
               value="5"
               data-component="body">
    <br>
<p>Mortality count. Example: <code>5</code></p>
        </div>
        </form>

                    <h2 id="add2farm-daily-records-GETapi-add2farm-daily-records--daily_record-">Get a single daily record</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retrieve detailed information of a specific daily record.</p>

<span id="example-requests-GETapi-add2farm-daily-records--daily_record-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://admin.eyt.test/api/add2farm/daily-records/1" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/daily-records/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-add2farm-daily-records--daily_record-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Daily record retrieved successfully.&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;record_date&quot;: &quot;2026-08-07&quot;,
        &quot;farm_id&quot;: 1,
        &quot;farm_name&quot;: &quot;Main Farm&quot;,
        &quot;flock_id&quot;: 1,
        &quot;flock_name&quot;: &quot;Farm1-Flock4&quot;,
        &quot;hangar_id&quot;: 1,
        &quot;hangar_name&quot;: &quot;Farm1-Hangar1&quot;,
        &quot;feed_kg&quot;: 450.5,
        &quot;eggs_tray_30&quot;: 12,
        &quot;eggs_count&quot;: 360,
        &quot;eggs_weight&quot;: 18.5,
        &quot;chicks_weight&quot;: 1.85,
        &quot;mortality&quot;: 5,
        &quot;created_by&quot;: 1,
        &quot;created_by_name&quot;: &quot;Admin Name&quot;,
        &quot;created_at&quot;: &quot;2026-08-07T10:30:00Z&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-add2farm-daily-records--daily_record-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-add2farm-daily-records--daily_record-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-add2farm-daily-records--daily_record-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-add2farm-daily-records--daily_record-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-add2farm-daily-records--daily_record-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-add2farm-daily-records--daily_record-" data-method="GET"
      data-path="api/add2farm/daily-records/{daily_record}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-add2farm-daily-records--daily_record-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-add2farm-daily-records--daily_record-"
                    onclick="tryItOut('GETapi-add2farm-daily-records--daily_record-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-add2farm-daily-records--daily_record-"
                    onclick="cancelTryOut('GETapi-add2farm-daily-records--daily_record-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-add2farm-daily-records--daily_record-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/add2farm/daily-records/{daily_record}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-add2farm-daily-records--daily_record-"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-add2farm-daily-records--daily_record-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-add2farm-daily-records--daily_record-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>daily_record</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="daily_record"                data-endpoint="GETapi-add2farm-daily-records--daily_record-"
               value="1"
               data-component="url">
    <br>
<p>The daily record ID. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="add2farm-daily-records-PUTapi-add2farm-daily-records--daily_record-">Update a daily record</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Update an existing daily record.</p>

<span id="example-requests-PUTapi-add2farm-daily-records--daily_record-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://admin.eyt.test/api/add2farm/daily-records/1" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"record_date\": \"07-08-2026\",
    \"hangar_id\": 1,
    \"feed_kg\": 450.5,
    \"eggs_tray_30\": 12,
    \"eggs_count\": 360,
    \"eggs_weight\": 18.5,
    \"chicks_weight\": 1.85,
    \"mortality\": 5
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/daily-records/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "record_date": "07-08-2026",
    "hangar_id": 1,
    "feed_kg": 450.5,
    "eggs_tray_30": 12,
    "eggs_count": 360,
    "eggs_weight": 18.5,
    "chicks_weight": 1.85,
    "mortality": 5
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-add2farm-daily-records--daily_record-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Daily record updated successfully.&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;record_date&quot;: &quot;2026-08-07&quot;,
        &quot;farm_id&quot;: 1,
        &quot;farm_name&quot;: &quot;Main Farm&quot;,
        &quot;flock_id&quot;: 1,
        &quot;flock_name&quot;: &quot;Farm1-Flock4&quot;,
        &quot;hangar_id&quot;: 1,
        &quot;hangar_name&quot;: &quot;Farm1-Hangar1&quot;,
        &quot;feed_kg&quot;: 450.5,
        &quot;eggs_tray_30&quot;: 12,
        &quot;eggs_count&quot;: 360,
        &quot;eggs_weight&quot;: 18.5,
        &quot;chicks_weight&quot;: 1.85,
        &quot;mortality&quot;: 5,
        &quot;created_by&quot;: 1,
        &quot;created_by_name&quot;: &quot;Admin Name&quot;,
        &quot;created_at&quot;: &quot;2026-08-07T10:30:00Z&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-add2farm-daily-records--daily_record-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-add2farm-daily-records--daily_record-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-add2farm-daily-records--daily_record-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-add2farm-daily-records--daily_record-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-add2farm-daily-records--daily_record-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-add2farm-daily-records--daily_record-" data-method="PUT"
      data-path="api/add2farm/daily-records/{daily_record}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-add2farm-daily-records--daily_record-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-add2farm-daily-records--daily_record-"
                    onclick="tryItOut('PUTapi-add2farm-daily-records--daily_record-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-add2farm-daily-records--daily_record-"
                    onclick="cancelTryOut('PUTapi-add2farm-daily-records--daily_record-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-add2farm-daily-records--daily_record-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/add2farm/daily-records/{daily_record}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PUTapi-add2farm-daily-records--daily_record-"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-add2farm-daily-records--daily_record-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-add2farm-daily-records--daily_record-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>daily_record</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="daily_record"                data-endpoint="PUTapi-add2farm-daily-records--daily_record-"
               value="1"
               data-component="url">
    <br>
<p>The daily record ID. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>record_date</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="record_date"                data-endpoint="PUTapi-add2farm-daily-records--daily_record-"
               value="07-08-2026"
               data-component="body">
    <br>
<p>Record date (format: dd-mm-yyyy). Example: <code>07-08-2026</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>hangar_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="hangar_id"                data-endpoint="PUTapi-add2farm-daily-records--daily_record-"
               value="1"
               data-component="body">
    <br>
<p>Hangar ID. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>feed_kg</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="feed_kg"                data-endpoint="PUTapi-add2farm-daily-records--daily_record-"
               value="450.5"
               data-component="body">
    <br>
<p>Feed quantity in kg. Example: <code>450.5</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>eggs_tray_30</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="eggs_tray_30"                data-endpoint="PUTapi-add2farm-daily-records--daily_record-"
               value="12"
               data-component="body">
    <br>
<p>Number of egg trays (30 count). Example: <code>12</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>eggs_count</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="eggs_count"                data-endpoint="PUTapi-add2farm-daily-records--daily_record-"
               value="360"
               data-component="body">
    <br>
<p>Egg count. Example: <code>360</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>eggs_weight</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="eggs_weight"                data-endpoint="PUTapi-add2farm-daily-records--daily_record-"
               value="18.5"
               data-component="body">
    <br>
<p>Eggs weight in kg. Example: <code>18.5</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>chicks_weight</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="chicks_weight"                data-endpoint="PUTapi-add2farm-daily-records--daily_record-"
               value="1.85"
               data-component="body">
    <br>
<p>Chicks weight in kg. Example: <code>1.85</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>mortality</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="mortality"                data-endpoint="PUTapi-add2farm-daily-records--daily_record-"
               value="5"
               data-component="body">
    <br>
<p>Mortality count. Example: <code>5</code></p>
        </div>
        </form>

                    <h2 id="add2farm-daily-records-DELETEapi-add2farm-daily-records--daily_record-">Delete a daily record</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Delete a daily record.</p>

<span id="example-requests-DELETEapi-add2farm-daily-records--daily_record-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://admin.eyt.test/api/add2farm/daily-records/1" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/daily-records/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-add2farm-daily-records--daily_record-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Daily record deleted successfully.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-add2farm-daily-records--daily_record-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-add2farm-daily-records--daily_record-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-add2farm-daily-records--daily_record-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-add2farm-daily-records--daily_record-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-add2farm-daily-records--daily_record-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-add2farm-daily-records--daily_record-" data-method="DELETE"
      data-path="api/add2farm/daily-records/{daily_record}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-add2farm-daily-records--daily_record-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-add2farm-daily-records--daily_record-"
                    onclick="tryItOut('DELETEapi-add2farm-daily-records--daily_record-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-add2farm-daily-records--daily_record-"
                    onclick="cancelTryOut('DELETEapi-add2farm-daily-records--daily_record-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-add2farm-daily-records--daily_record-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/add2farm/daily-records/{daily_record}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="DELETEapi-add2farm-daily-records--daily_record-"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-add2farm-daily-records--daily_record-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-add2farm-daily-records--daily_record-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>daily_record</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="daily_record"                data-endpoint="DELETEapi-add2farm-daily-records--daily_record-"
               value="1"
               data-component="url">
    <br>
<p>The daily record ID. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="add2farm-dropdowns">Add2Farm Dropdowns</h1>

    

                                <h2 id="add2farm-dropdowns-GETapi-add2farm-dropdowns-farms">Get farms dropdown list</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Fetch list of farms with id and name only for dropdown/select usage.</p>

<span id="example-requests-GETapi-add2farm-dropdowns-farms">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://admin.eyt.test/api/add2farm/dropdowns/farms" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/dropdowns/farms"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-add2farm-dropdowns-farms">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Farms retrieved successfully.&quot;,
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;Main Farm&quot;
        },
        {
            &quot;id&quot;: 2,
            &quot;name&quot;: &quot;Secondary Farm&quot;
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-add2farm-dropdowns-farms" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-add2farm-dropdowns-farms"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-add2farm-dropdowns-farms"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-add2farm-dropdowns-farms" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-add2farm-dropdowns-farms">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-add2farm-dropdowns-farms" data-method="GET"
      data-path="api/add2farm/dropdowns/farms"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-add2farm-dropdowns-farms', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-add2farm-dropdowns-farms"
                    onclick="tryItOut('GETapi-add2farm-dropdowns-farms');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-add2farm-dropdowns-farms"
                    onclick="cancelTryOut('GETapi-add2farm-dropdowns-farms');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-add2farm-dropdowns-farms"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/add2farm/dropdowns/farms</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-add2farm-dropdowns-farms"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-add2farm-dropdowns-farms"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-add2farm-dropdowns-farms"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="add2farm-dropdowns-GETapi-add2farm-dropdowns-suppliers">Get suppliers dropdown list</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Fetch list of chicks suppliers with id and name only for dropdown/select usage.</p>

<span id="example-requests-GETapi-add2farm-dropdowns-suppliers">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://admin.eyt.test/api/add2farm/dropdowns/suppliers" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/dropdowns/suppliers"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-add2farm-dropdowns-suppliers">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Suppliers retrieved successfully.&quot;,
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;Al-Rowad Farm&quot;
        },
        {
            &quot;id&quot;: 2,
            &quot;name&quot;: &quot;Premium Chicks Co&quot;
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-add2farm-dropdowns-suppliers" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-add2farm-dropdowns-suppliers"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-add2farm-dropdowns-suppliers"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-add2farm-dropdowns-suppliers" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-add2farm-dropdowns-suppliers">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-add2farm-dropdowns-suppliers" data-method="GET"
      data-path="api/add2farm/dropdowns/suppliers"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-add2farm-dropdowns-suppliers', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-add2farm-dropdowns-suppliers"
                    onclick="tryItOut('GETapi-add2farm-dropdowns-suppliers');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-add2farm-dropdowns-suppliers"
                    onclick="cancelTryOut('GETapi-add2farm-dropdowns-suppliers');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-add2farm-dropdowns-suppliers"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/add2farm/dropdowns/suppliers</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-add2farm-dropdowns-suppliers"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-add2farm-dropdowns-suppliers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-add2farm-dropdowns-suppliers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="add2farm-dropdowns-GETapi-add2farm-dropdowns-supervisors">Get supervisors dropdown list</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Fetch list of supervisors (Type 3 admins) with id and name only for dropdown/select usage.</p>

<span id="example-requests-GETapi-add2farm-dropdowns-supervisors">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://admin.eyt.test/api/add2farm/dropdowns/supervisors" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/dropdowns/supervisors"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-add2farm-dropdowns-supervisors">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Supervisors retrieved successfully.&quot;,
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;John Supervisor&quot;
        },
        {
            &quot;id&quot;: 3,
            &quot;name&quot;: &quot;Alice Supervisor&quot;
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-add2farm-dropdowns-supervisors" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-add2farm-dropdowns-supervisors"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-add2farm-dropdowns-supervisors"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-add2farm-dropdowns-supervisors" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-add2farm-dropdowns-supervisors">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-add2farm-dropdowns-supervisors" data-method="GET"
      data-path="api/add2farm/dropdowns/supervisors"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-add2farm-dropdowns-supervisors', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-add2farm-dropdowns-supervisors"
                    onclick="tryItOut('GETapi-add2farm-dropdowns-supervisors');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-add2farm-dropdowns-supervisors"
                    onclick="cancelTryOut('GETapi-add2farm-dropdowns-supervisors');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-add2farm-dropdowns-supervisors"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/add2farm/dropdowns/supervisors</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-add2farm-dropdowns-supervisors"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-add2farm-dropdowns-supervisors"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-add2farm-dropdowns-supervisors"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="add2farm-farmers-type-4">Add2Farm Farmers (Type 4)</h1>

    

                                <h2 id="add2farm-farmers-type-4-GETapi-add2farm-farmers">List all farmers</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Get paginated list of all farmers with search and filtering.</p>

<span id="example-requests-GETapi-add2farm-farmers">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://admin.eyt.test/api/add2farm/farmers?page=1&amp;per_page=20&amp;search=Farmer1" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/farmers"
);

const params = {
    "page": "1",
    "per_page": "20",
    "search": "Farmer1",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-add2farm-farmers">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Farmers retrieved successfully.&quot;,
    &quot;data&quot;: {
        &quot;current_page&quot;: 1,
        &quot;data&quot;: [
            {
                &quot;id&quot;: 2,
                &quot;name&quot;: &quot;Farmer Name&quot;,
                &quot;mobile_number&quot;: &quot;+0987654321&quot;,
                &quot;email&quot;: &quot;farmer@add2farm.local&quot;,
                &quot;type&quot;: 4,
                &quot;type_label&quot;: &quot;Farmer&quot;,
                &quot;status&quot;: &quot;Active&quot;
            }
        ],
        &quot;total&quot;: 10,
        &quot;last_page&quot;: 1
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-add2farm-farmers" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-add2farm-farmers"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-add2farm-farmers"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-add2farm-farmers" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-add2farm-farmers">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-add2farm-farmers" data-method="GET"
      data-path="api/add2farm/farmers"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-add2farm-farmers', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-add2farm-farmers"
                    onclick="tryItOut('GETapi-add2farm-farmers');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-add2farm-farmers"
                    onclick="cancelTryOut('GETapi-add2farm-farmers');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-add2farm-farmers"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/add2farm/farmers</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-add2farm-farmers"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-add2farm-farmers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-add2farm-farmers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-add2farm-farmers"
               value="1"
               data-component="query">
    <br>
<p>optional Pagination page number. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-add2farm-farmers"
               value="20"
               data-component="query">
    <br>
<p>optional Items per page. Default: 15. Example: <code>20</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>search</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="search"                data-endpoint="GETapi-add2farm-farmers"
               value="Farmer1"
               data-component="query">
    <br>
<p>optional Search by name or mobile number. Example: <code>Farmer1</code></p>
            </div>
                </form>

                    <h2 id="add2farm-farmers-type-4-POSTapi-add2farm-farmers">Create a new farmer</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Create a new farmer account (Type 4).</p>

<span id="example-requests-POSTapi-add2farm-farmers">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://admin.eyt.test/api/add2farm/farmers" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"mobile_number\": \"+0987654321\",
    \"name\": \"Jane Farmer\",
    \"email\": \"farmer@example.com\",
    \"password\": \"password123\",
    \"password_confirmation\": \"password123\",
    \"notes\": \"Experienced farmer with 5 years background\",
    \"image\": \"(binary)\",
    \"status\": \"Active\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/farmers"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "mobile_number": "+0987654321",
    "name": "Jane Farmer",
    "email": "farmer@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "notes": "Experienced farmer with 5 years background",
    "image": "(binary)",
    "status": "Active"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-add2farm-farmers">
            <blockquote>
            <p>Example response (201):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Farmer created successfully.&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: 2,
        &quot;name&quot;: &quot;Jane Farmer&quot;,
        &quot;mobile_number&quot;: &quot;+0987654321&quot;,
        &quot;email&quot;: &quot;farmer@example.com&quot;,
        &quot;type&quot;: 4,
        &quot;type_label&quot;: &quot;Farmer&quot;,
        &quot;status&quot;: &quot;Active&quot;,
        &quot;notes&quot;: &quot;Experienced farmer with 5 years background&quot;,
        &quot;image&quot;: &quot;uploads/farmers/farmer_image_123.jpg&quot;,
        &quot;created_at&quot;: &quot;2026-08-07T10:30:00Z&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;errors&quot;: {
        &quot;mobile_number&quot;: [
            &quot;The mobile number has already been taken.&quot;
        ],
        &quot;email&quot;: [
            &quot;The email has already been taken.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-add2farm-farmers" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-add2farm-farmers"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-add2farm-farmers"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-add2farm-farmers" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-add2farm-farmers">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-add2farm-farmers" data-method="POST"
      data-path="api/add2farm/farmers"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-add2farm-farmers', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-add2farm-farmers"
                    onclick="tryItOut('POSTapi-add2farm-farmers');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-add2farm-farmers"
                    onclick="cancelTryOut('POSTapi-add2farm-farmers');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-add2farm-farmers"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/add2farm/farmers</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-add2farm-farmers"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-add2farm-farmers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-add2farm-farmers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>mobile_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="mobile_number"                data-endpoint="POSTapi-add2farm-farmers"
               value="+0987654321"
               data-component="body">
    <br>
<p>Farmer mobile number with country code. Must be unique. Example: <code>+0987654321</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-add2farm-farmers"
               value="Jane Farmer"
               data-component="body">
    <br>
<p>Farmer full name. Example: <code>Jane Farmer</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-add2farm-farmers"
               value="farmer@example.com"
               data-component="body">
    <br>
<p>Farmer email address. Example: <code>farmer@example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-add2farm-farmers"
               value="password123"
               data-component="body">
    <br>
<p>Password (minimum 8 characters). Example: <code>password123</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password_confirmation</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password_confirmation"                data-endpoint="POSTapi-add2farm-farmers"
               value="password123"
               data-component="body">
    <br>
<p>Password confirmation. Example: <code>password123</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>notes</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="notes"                data-endpoint="POSTapi-add2farm-farmers"
               value="Experienced farmer with 5 years background"
               data-component="body">
    <br>
<p>Optional notes about the farmer. Example: <code>Experienced farmer with 5 years background</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>image</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="image"                data-endpoint="POSTapi-add2farm-farmers"
               value="(binary)"
               data-component="body">
    <br>
<p>Profile image (jpeg, png, gif). Max 2MB. Example: <code>(binary)</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="POSTapi-add2farm-farmers"
               value="Active"
               data-component="body">
    <br>
<p>Status (Active, Inactive, Disable). Example: <code>Active</code></p>
        </div>
        </form>

                    <h2 id="add2farm-farmers-type-4-GETapi-add2farm-farmers--farmer-">Get a single farmer</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retrieve detailed information of a specific farmer.</p>

<span id="example-requests-GETapi-add2farm-farmers--farmer-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://admin.eyt.test/api/add2farm/farmers/2" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/farmers/2"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-add2farm-farmers--farmer-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Farmer retrieved successfully.&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: 2,
        &quot;name&quot;: &quot;Farmer Name&quot;,
        &quot;mobile_number&quot;: &quot;+0987654321&quot;,
        &quot;email&quot;: &quot;farmer@add2farm.local&quot;,
        &quot;type&quot;: 4,
        &quot;type_label&quot;: &quot;Farmer&quot;,
        &quot;status&quot;: &quot;Active&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-add2farm-farmers--farmer-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-add2farm-farmers--farmer-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-add2farm-farmers--farmer-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-add2farm-farmers--farmer-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-add2farm-farmers--farmer-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-add2farm-farmers--farmer-" data-method="GET"
      data-path="api/add2farm/farmers/{farmer}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-add2farm-farmers--farmer-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-add2farm-farmers--farmer-"
                    onclick="tryItOut('GETapi-add2farm-farmers--farmer-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-add2farm-farmers--farmer-"
                    onclick="cancelTryOut('GETapi-add2farm-farmers--farmer-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-add2farm-farmers--farmer-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/add2farm/farmers/{farmer}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-add2farm-farmers--farmer-"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-add2farm-farmers--farmer-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-add2farm-farmers--farmer-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>farmer</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="farmer"                data-endpoint="GETapi-add2farm-farmers--farmer-"
               value="2"
               data-component="url">
    <br>
<p>The farmer ID. Example: <code>2</code></p>
            </div>
                    </form>

                    <h2 id="add2farm-farmers-type-4-PUTapi-add2farm-farmers--farmer-">Update a farmer</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Update farmer details including name, email, status and project assignments.</p>

<span id="example-requests-PUTapi-add2farm-farmers--farmer-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://admin.eyt.test/api/add2farm/farmers/2" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"Jane Farmer Updated\",
    \"email\": \"jane.updated@example.com\",
    \"status\": \"Active\",
    \"notes\": \"Updated notes\",
    \"image\": \"(binary)\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/farmers/2"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "Jane Farmer Updated",
    "email": "jane.updated@example.com",
    "status": "Active",
    "notes": "Updated notes",
    "image": "(binary)"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-add2farm-farmers--farmer-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Farmer updated successfully.&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: 2,
        &quot;name&quot;: &quot;Jane Farmer Updated&quot;,
        &quot;mobile_number&quot;: &quot;+0987654321&quot;,
        &quot;email&quot;: &quot;jane.updated@example.com&quot;,
        &quot;type&quot;: 4,
        &quot;status&quot;: &quot;Active&quot;,
        &quot;notes&quot;: &quot;Updated notes&quot;,
        &quot;image&quot;: &quot;uploads/farmers/farmer_image_456.jpg&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-add2farm-farmers--farmer-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-add2farm-farmers--farmer-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-add2farm-farmers--farmer-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-add2farm-farmers--farmer-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-add2farm-farmers--farmer-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-add2farm-farmers--farmer-" data-method="PUT"
      data-path="api/add2farm/farmers/{farmer}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-add2farm-farmers--farmer-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-add2farm-farmers--farmer-"
                    onclick="tryItOut('PUTapi-add2farm-farmers--farmer-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-add2farm-farmers--farmer-"
                    onclick="cancelTryOut('PUTapi-add2farm-farmers--farmer-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-add2farm-farmers--farmer-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/add2farm/farmers/{farmer}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PUTapi-add2farm-farmers--farmer-"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-add2farm-farmers--farmer-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-add2farm-farmers--farmer-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>farmer</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="farmer"                data-endpoint="PUTapi-add2farm-farmers--farmer-"
               value="2"
               data-component="url">
    <br>
<p>The farmer ID. Example: <code>2</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="PUTapi-add2farm-farmers--farmer-"
               value="Jane Farmer Updated"
               data-component="body">
    <br>
<p>Farmer's full name. Example: <code>Jane Farmer Updated</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="PUTapi-add2farm-farmers--farmer-"
               value="jane.updated@example.com"
               data-component="body">
    <br>
<p>Email address. Example: <code>jane.updated@example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="PUTapi-add2farm-farmers--farmer-"
               value="Active"
               data-component="body">
    <br>
<p>Status (Active, Inactive, Disable). Example: <code>Active</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>notes</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="notes"                data-endpoint="PUTapi-add2farm-farmers--farmer-"
               value="Updated notes"
               data-component="body">
    <br>
<p>Optional notes about the farmer. Example: <code>Updated notes</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>image</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="image"                data-endpoint="PUTapi-add2farm-farmers--farmer-"
               value="(binary)"
               data-component="body">
    <br>
<p>Profile image (jpeg, png, gif). Max 2MB. Example: <code>(binary)</code></p>
        </div>
        </form>

                    <h2 id="add2farm-farmers-type-4-DELETEapi-add2farm-farmers--farmer-">Delete a farmer</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Delete a farmer and their project status records.</p>

<span id="example-requests-DELETEapi-add2farm-farmers--farmer-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://admin.eyt.test/api/add2farm/farmers/2" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/farmers/2"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-add2farm-farmers--farmer-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Farmer deleted successfully.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-add2farm-farmers--farmer-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-add2farm-farmers--farmer-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-add2farm-farmers--farmer-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-add2farm-farmers--farmer-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-add2farm-farmers--farmer-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-add2farm-farmers--farmer-" data-method="DELETE"
      data-path="api/add2farm/farmers/{farmer}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-add2farm-farmers--farmer-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-add2farm-farmers--farmer-"
                    onclick="tryItOut('DELETEapi-add2farm-farmers--farmer-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-add2farm-farmers--farmer-"
                    onclick="cancelTryOut('DELETEapi-add2farm-farmers--farmer-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-add2farm-farmers--farmer-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/add2farm/farmers/{farmer}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="DELETEapi-add2farm-farmers--farmer-"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-add2farm-farmers--farmer-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-add2farm-farmers--farmer-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>farmer</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="farmer"                data-endpoint="DELETEapi-add2farm-farmers--farmer-"
               value="2"
               data-component="url">
    <br>
<p>The farmer ID. Example: <code>2</code></p>
            </div>
                    </form>

                <h1 id="add2farm-farms">Add2Farm Farms</h1>

    

                                <h2 id="add2farm-farms-GETapi-add2farm-farms">List all farms</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Get paginated list of all farms with search and filtering.</p>

<span id="example-requests-GETapi-add2farm-farms">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://admin.eyt.test/api/add2farm/farms?page=1&amp;per_page=20&amp;search=Farm1" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/farms"
);

const params = {
    "page": "1",
    "per_page": "20",
    "search": "Farm1",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-add2farm-farms">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Farms retrieved successfully.&quot;,
    &quot;data&quot;: {
        &quot;current_page&quot;: 1,
        &quot;data&quot;: [
            {
                &quot;id&quot;: 1,
                &quot;name&quot;: &quot;Main Farm&quot;,
                &quot;location&quot;: &quot;Village A&quot;,
                &quot;type&quot;: &quot;Layer&quot;,
                &quot;number_of_hangars&quot;: 5,
                &quot;assigned_to&quot;: 1
            }
        ],
        &quot;total&quot;: 8,
        &quot;last_page&quot;: 1
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-add2farm-farms" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-add2farm-farms"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-add2farm-farms"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-add2farm-farms" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-add2farm-farms">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-add2farm-farms" data-method="GET"
      data-path="api/add2farm/farms"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-add2farm-farms', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-add2farm-farms"
                    onclick="tryItOut('GETapi-add2farm-farms');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-add2farm-farms"
                    onclick="cancelTryOut('GETapi-add2farm-farms');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-add2farm-farms"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/add2farm/farms</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-add2farm-farms"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-add2farm-farms"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-add2farm-farms"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-add2farm-farms"
               value="1"
               data-component="query">
    <br>
<p>optional Pagination page number. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-add2farm-farms"
               value="20"
               data-component="query">
    <br>
<p>optional Items per page. Default: 15. Example: <code>20</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>search</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="search"                data-endpoint="GETapi-add2farm-farms"
               value="Farm1"
               data-component="query">
    <br>
<p>optional Search by farm name or location. Example: <code>Farm1</code></p>
            </div>
                </form>

                    <h2 id="add2farm-farms-POSTapi-add2farm-farms">Create a new farm</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Create a new farm with details.</p>

<span id="example-requests-POSTapi-add2farm-farms">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://admin.eyt.test/api/add2farm/farms" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"Green Valley Farm\",
    \"location\": \"North Region\",
    \"type\": \"Poultry\",
    \"number_of_hangars\": 5
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/farms"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "Green Valley Farm",
    "location": "North Region",
    "type": "Poultry",
    "number_of_hangars": 5
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-add2farm-farms">
            <blockquote>
            <p>Example response (201):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Farm created successfully.&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;Green Valley Farm&quot;,
        &quot;location&quot;: &quot;North Region&quot;,
        &quot;type&quot;: &quot;Poultry&quot;,
        &quot;number_of_hangars&quot;: 5,
        &quot;created_by&quot;: 1,
        &quot;created_by_name&quot;: &quot;Admin Name&quot;,
        &quot;created_at&quot;: &quot;2026-08-07T10:30:00Z&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;errors&quot;: {
        &quot;name&quot;: [
            &quot;The name field is required.&quot;
        ],
        &quot;number_of_hangars&quot;: [
            &quot;The number of hangars must be between 1 and 999.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-add2farm-farms" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-add2farm-farms"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-add2farm-farms"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-add2farm-farms" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-add2farm-farms">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-add2farm-farms" data-method="POST"
      data-path="api/add2farm/farms"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-add2farm-farms', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-add2farm-farms"
                    onclick="tryItOut('POSTapi-add2farm-farms');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-add2farm-farms"
                    onclick="cancelTryOut('POSTapi-add2farm-farms');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-add2farm-farms"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/add2farm/farms</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-add2farm-farms"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-add2farm-farms"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-add2farm-farms"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-add2farm-farms"
               value="Green Valley Farm"
               data-component="body">
    <br>
<p>Farm name. Example: <code>Green Valley Farm</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>location</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="location"                data-endpoint="POSTapi-add2farm-farms"
               value="North Region"
               data-component="body">
    <br>
<p>Farm location. Example: <code>North Region</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="type"                data-endpoint="POSTapi-add2farm-farms"
               value="Poultry"
               data-component="body">
    <br>
<p>Farm type (Poultry, Layer, Broiler, etc.). Example: <code>Poultry</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>number_of_hangars</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="number_of_hangars"                data-endpoint="POSTapi-add2farm-farms"
               value="5"
               data-component="body">
    <br>
<p>Number of hangars (1-999). Example: <code>5</code></p>
        </div>
        </form>

                    <h2 id="add2farm-farms-GETapi-add2farm-farms--farm-">Get a single farm</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retrieve detailed information of a specific farm.</p>

<span id="example-requests-GETapi-add2farm-farms--farm-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://admin.eyt.test/api/add2farm/farms/1" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/farms/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-add2farm-farms--farm-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Farm retrieved successfully.&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;Main Farm&quot;,
        &quot;location&quot;: &quot;Village A&quot;,
        &quot;type&quot;: &quot;Layer&quot;,
        &quot;number_of_hangars&quot;: 5,
        &quot;assigned_to&quot;: 1
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-add2farm-farms--farm-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-add2farm-farms--farm-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-add2farm-farms--farm-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-add2farm-farms--farm-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-add2farm-farms--farm-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-add2farm-farms--farm-" data-method="GET"
      data-path="api/add2farm/farms/{farm}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-add2farm-farms--farm-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-add2farm-farms--farm-"
                    onclick="tryItOut('GETapi-add2farm-farms--farm-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-add2farm-farms--farm-"
                    onclick="cancelTryOut('GETapi-add2farm-farms--farm-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-add2farm-farms--farm-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/add2farm/farms/{farm}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-add2farm-farms--farm-"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-add2farm-farms--farm-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-add2farm-farms--farm-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>farm</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="farm"                data-endpoint="GETapi-add2farm-farms--farm-"
               value="1"
               data-component="url">
    <br>
<p>The farm ID. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="add2farm-farms-PUTapi-add2farm-farms--farm-">Update a farm</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Update farm details including name, location, type, hangars count and farmer assignment.</p>

<span id="example-requests-PUTapi-add2farm-farms--farm-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://admin.eyt.test/api/add2farm/farms/1" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"Green Valley Farm Updated\",
    \"location\": \"North Region Updated\",
    \"type\": \"Poultry\",
    \"number_of_hangars\": 5
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/farms/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "Green Valley Farm Updated",
    "location": "North Region Updated",
    "type": "Poultry",
    "number_of_hangars": 5
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-add2farm-farms--farm-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Farm updated successfully.&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;Green Valley Farm Updated&quot;,
        &quot;location&quot;: &quot;North Region Updated&quot;,
        &quot;type&quot;: &quot;Poultry&quot;,
        &quot;number_of_hangars&quot;: 5
    }
}</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-add2farm-farms--farm-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-add2farm-farms--farm-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-add2farm-farms--farm-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-add2farm-farms--farm-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-add2farm-farms--farm-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-add2farm-farms--farm-" data-method="PUT"
      data-path="api/add2farm/farms/{farm}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-add2farm-farms--farm-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-add2farm-farms--farm-"
                    onclick="tryItOut('PUTapi-add2farm-farms--farm-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-add2farm-farms--farm-"
                    onclick="cancelTryOut('PUTapi-add2farm-farms--farm-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-add2farm-farms--farm-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/add2farm/farms/{farm}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PUTapi-add2farm-farms--farm-"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-add2farm-farms--farm-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-add2farm-farms--farm-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>farm</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="farm"                data-endpoint="PUTapi-add2farm-farms--farm-"
               value="1"
               data-component="url">
    <br>
<p>The farm ID. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="PUTapi-add2farm-farms--farm-"
               value="Green Valley Farm Updated"
               data-component="body">
    <br>
<p>Farm name. Example: <code>Green Valley Farm Updated</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>location</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="location"                data-endpoint="PUTapi-add2farm-farms--farm-"
               value="North Region Updated"
               data-component="body">
    <br>
<p>Farm location. Example: <code>North Region Updated</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="type"                data-endpoint="PUTapi-add2farm-farms--farm-"
               value="Poultry"
               data-component="body">
    <br>
<p>Farm type. Example: <code>Poultry</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>number_of_hangars</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="number_of_hangars"                data-endpoint="PUTapi-add2farm-farms--farm-"
               value="5"
               data-component="body">
    <br>
<p>Number of hangars (1-999). Example: <code>5</code></p>
        </div>
        </form>

                    <h2 id="add2farm-farms-DELETEapi-add2farm-farms--farm-">Delete a farm</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Delete a farm and all its related records.</p>

<span id="example-requests-DELETEapi-add2farm-farms--farm-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://admin.eyt.test/api/add2farm/farms/1" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/farms/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-add2farm-farms--farm-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Farm deleted successfully.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-add2farm-farms--farm-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-add2farm-farms--farm-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-add2farm-farms--farm-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-add2farm-farms--farm-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-add2farm-farms--farm-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-add2farm-farms--farm-" data-method="DELETE"
      data-path="api/add2farm/farms/{farm}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-add2farm-farms--farm-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-add2farm-farms--farm-"
                    onclick="tryItOut('DELETEapi-add2farm-farms--farm-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-add2farm-farms--farm-"
                    onclick="cancelTryOut('DELETEapi-add2farm-farms--farm-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-add2farm-farms--farm-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/add2farm/farms/{farm}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="DELETEapi-add2farm-farms--farm-"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-add2farm-farms--farm-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-add2farm-farms--farm-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>farm</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="farm"                data-endpoint="DELETEapi-add2farm-farms--farm-"
               value="1"
               data-component="url">
    <br>
<p>The farm ID. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="add2farm-flocks">Add2Farm Flocks</h1>

    

                                <h2 id="add2farm-flocks-GETapi-add2farm-flocks">List all flocks</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Get paginated list of all flocks with search and filtering.</p>

<span id="example-requests-GETapi-add2farm-flocks">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://admin.eyt.test/api/add2farm/flocks?page=1&amp;per_page=20&amp;search=Flock1" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/flocks"
);

const params = {
    "page": "1",
    "per_page": "20",
    "search": "Flock1",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-add2farm-flocks">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Flocks retrieved successfully.&quot;,
    &quot;data&quot;: {
        &quot;current_page&quot;: 1,
        &quot;data&quot;: [
            {
                &quot;id&quot;: 1,
                &quot;name&quot;: &quot;Farm1-Flock4&quot;,
                &quot;farm_id&quot;: 1,
                &quot;farm_name&quot;: &quot;Main Farm&quot;,
                &quot;chicks_supplier_id&quot;: 1,
                &quot;chicks_supplier_name&quot;: &quot;Al-Rowad Farm&quot;,
                &quot;breed&quot;: &quot;Broiler,Cobb 500&quot;,
                &quot;start_date&quot;: &quot;2026-05-18&quot;,
                &quot;total_quantity&quot;: 12500
            }
        ],
        &quot;total&quot;: 10,
        &quot;last_page&quot;: 1
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-add2farm-flocks" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-add2farm-flocks"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-add2farm-flocks"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-add2farm-flocks" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-add2farm-flocks">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-add2farm-flocks" data-method="GET"
      data-path="api/add2farm/flocks"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-add2farm-flocks', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-add2farm-flocks"
                    onclick="tryItOut('GETapi-add2farm-flocks');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-add2farm-flocks"
                    onclick="cancelTryOut('GETapi-add2farm-flocks');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-add2farm-flocks"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/add2farm/flocks</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-add2farm-flocks"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-add2farm-flocks"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-add2farm-flocks"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-add2farm-flocks"
               value="1"
               data-component="query">
    <br>
<p>optional Pagination page number. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-add2farm-flocks"
               value="20"
               data-component="query">
    <br>
<p>optional Items per page. Default: 15. Example: <code>20</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>search</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="search"                data-endpoint="GETapi-add2farm-flocks"
               value="Flock1"
               data-component="query">
    <br>
<p>optional Search by flock name. Example: <code>Flock1</code></p>
            </div>
                </form>

                    <h2 id="add2farm-flocks-POSTapi-add2farm-flocks">Create a new flock</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Create a new flock with hangar allocations.</p>

<span id="example-requests-POSTapi-add2farm-flocks">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://admin.eyt.test/api/add2farm/flocks" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"Farm1-Flock4\",
    \"farm_id\": 1,
    \"chicks_supplier_id\": 1,
    \"breed\": \"Broiler,Cobb 500\",
    \"start_date\": \"18-05-2026\",
    \"total_quantity\": 12500,
    \"hangar_allocations\": [
        {
            \"hangar_id\": 1,
            \"quantity\": 3000
        },
        {
            \"hangar_id\": 2,
            \"quantity\": 3000
        }
    ]
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/flocks"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "Farm1-Flock4",
    "farm_id": 1,
    "chicks_supplier_id": 1,
    "breed": "Broiler,Cobb 500",
    "start_date": "18-05-2026",
    "total_quantity": 12500,
    "hangar_allocations": [
        {
            "hangar_id": 1,
            "quantity": 3000
        },
        {
            "hangar_id": 2,
            "quantity": 3000
        }
    ]
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-add2farm-flocks">
            <blockquote>
            <p>Example response (201):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Flock created successfully.&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;Farm1-Flock4&quot;,
        &quot;farm_id&quot;: 1,
        &quot;farm_name&quot;: &quot;Main Farm&quot;,
        &quot;chicks_supplier_id&quot;: 1,
        &quot;chicks_supplier_name&quot;: &quot;Al-Rowad Farm&quot;,
        &quot;breed&quot;: &quot;Broiler,Cobb 500&quot;,
        &quot;start_date&quot;: &quot;2026-05-18&quot;,
        &quot;total_quantity&quot;: 12500,
        &quot;created_by&quot;: 1,
        &quot;created_by_name&quot;: &quot;Admin Name&quot;,
        &quot;created_at&quot;: &quot;2026-08-07T10:30:00Z&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-add2farm-flocks" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-add2farm-flocks"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-add2farm-flocks"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-add2farm-flocks" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-add2farm-flocks">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-add2farm-flocks" data-method="POST"
      data-path="api/add2farm/flocks"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-add2farm-flocks', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-add2farm-flocks"
                    onclick="tryItOut('POSTapi-add2farm-flocks');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-add2farm-flocks"
                    onclick="cancelTryOut('POSTapi-add2farm-flocks');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-add2farm-flocks"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/add2farm/flocks</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-add2farm-flocks"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-add2farm-flocks"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-add2farm-flocks"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-add2farm-flocks"
               value="Farm1-Flock4"
               data-component="body">
    <br>
<p>Flock name. Example: <code>Farm1-Flock4</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>farm_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="farm_id"                data-endpoint="POSTapi-add2farm-flocks"
               value="1"
               data-component="body">
    <br>
<p>Farm ID. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>chicks_supplier_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="chicks_supplier_id"                data-endpoint="POSTapi-add2farm-flocks"
               value="1"
               data-component="body">
    <br>
<p>Chicks supplier ID. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>breed</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="breed"                data-endpoint="POSTapi-add2farm-flocks"
               value="Broiler,Cobb 500"
               data-component="body">
    <br>
<p>Breed name. Example: <code>Broiler,Cobb 500</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>start_date</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="start_date"                data-endpoint="POSTapi-add2farm-flocks"
               value="18-05-2026"
               data-component="body">
    <br>
<p>Start date (format: dd-mm-yyyy). Example: <code>18-05-2026</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>total_quantity</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="total_quantity"                data-endpoint="POSTapi-add2farm-flocks"
               value="12500"
               data-component="body">
    <br>
<p>Total number of chicks. Example: <code>12500</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>hangar_allocations</code></b>&nbsp;&nbsp;
<small>array</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="hangar_allocations"                data-endpoint="POSTapi-add2farm-flocks"
               value=""
               data-component="body">
    <br>
<p>Array of hangar allocations.</p>
        </div>
        </form>

                    <h2 id="add2farm-flocks-GETapi-add2farm-flocks--flock-">Get a single flock</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retrieve detailed information of a specific flock.</p>

<span id="example-requests-GETapi-add2farm-flocks--flock-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://admin.eyt.test/api/add2farm/flocks/1" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/flocks/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-add2farm-flocks--flock-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Flock retrieved successfully.&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;Farm1-Flock4&quot;,
        &quot;farm_id&quot;: 1,
        &quot;farm_name&quot;: &quot;Main Farm&quot;,
        &quot;chicks_supplier_id&quot;: 1,
        &quot;chicks_supplier_name&quot;: &quot;Al-Rowad Farm&quot;,
        &quot;breed&quot;: &quot;Broiler,Cobb 500&quot;,
        &quot;start_date&quot;: &quot;2026-05-18&quot;,
        &quot;total_quantity&quot;: 12500,
        &quot;created_by&quot;: 1,
        &quot;created_by_name&quot;: &quot;Admin Name&quot;,
        &quot;hangars&quot;: [
            {
                &quot;hangar_id&quot;: 1,
                &quot;hangar_name&quot;: &quot;Farm1-Hangar1&quot;,
                &quot;quantity&quot;: 3000
            }
        ],
        &quot;created_at&quot;: &quot;2026-08-07T10:30:00Z&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-add2farm-flocks--flock-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-add2farm-flocks--flock-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-add2farm-flocks--flock-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-add2farm-flocks--flock-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-add2farm-flocks--flock-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-add2farm-flocks--flock-" data-method="GET"
      data-path="api/add2farm/flocks/{flock}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-add2farm-flocks--flock-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-add2farm-flocks--flock-"
                    onclick="tryItOut('GETapi-add2farm-flocks--flock-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-add2farm-flocks--flock-"
                    onclick="cancelTryOut('GETapi-add2farm-flocks--flock-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-add2farm-flocks--flock-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/add2farm/flocks/{flock}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-add2farm-flocks--flock-"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-add2farm-flocks--flock-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-add2farm-flocks--flock-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>flock</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="flock"                data-endpoint="GETapi-add2farm-flocks--flock-"
               value="1"
               data-component="url">
    <br>
<p>The flock ID. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="add2farm-flocks-PUTapi-add2farm-flocks--flock-">Update a flock</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Update flock details and hangar allocations.</p>

<span id="example-requests-PUTapi-add2farm-flocks--flock-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://admin.eyt.test/api/add2farm/flocks/1" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"Farm1-Flock4 Updated\",
    \"chicks_supplier_id\": 1,
    \"breed\": \"Broiler,Cobb 500\",
    \"start_date\": \"18-05-2026\",
    \"total_quantity\": 12500,
    \"hangar_allocations\": [
        {
            \"hangar_id\": 1,
            \"quantity\": 3000
        }
    ]
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/flocks/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "Farm1-Flock4 Updated",
    "chicks_supplier_id": 1,
    "breed": "Broiler,Cobb 500",
    "start_date": "18-05-2026",
    "total_quantity": 12500,
    "hangar_allocations": [
        {
            "hangar_id": 1,
            "quantity": 3000
        }
    ]
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-add2farm-flocks--flock-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Flock updated successfully.&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;Farm1-Flock4 Updated&quot;,
        &quot;farm_id&quot;: 1,
        &quot;farm_name&quot;: &quot;Main Farm&quot;,
        &quot;chicks_supplier_id&quot;: 1,
        &quot;chicks_supplier_name&quot;: &quot;Al-Rowad Farm&quot;,
        &quot;breed&quot;: &quot;Broiler,Cobb 500&quot;,
        &quot;start_date&quot;: &quot;2026-05-18&quot;,
        &quot;total_quantity&quot;: 12500,
        &quot;created_by&quot;: 1,
        &quot;created_by_name&quot;: &quot;Admin Name&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-add2farm-flocks--flock-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-add2farm-flocks--flock-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-add2farm-flocks--flock-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-add2farm-flocks--flock-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-add2farm-flocks--flock-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-add2farm-flocks--flock-" data-method="PUT"
      data-path="api/add2farm/flocks/{flock}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-add2farm-flocks--flock-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-add2farm-flocks--flock-"
                    onclick="tryItOut('PUTapi-add2farm-flocks--flock-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-add2farm-flocks--flock-"
                    onclick="cancelTryOut('PUTapi-add2farm-flocks--flock-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-add2farm-flocks--flock-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/add2farm/flocks/{flock}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PUTapi-add2farm-flocks--flock-"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-add2farm-flocks--flock-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-add2farm-flocks--flock-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>flock</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="flock"                data-endpoint="PUTapi-add2farm-flocks--flock-"
               value="1"
               data-component="url">
    <br>
<p>The flock ID. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="PUTapi-add2farm-flocks--flock-"
               value="Farm1-Flock4 Updated"
               data-component="body">
    <br>
<p>Flock name. Example: <code>Farm1-Flock4 Updated</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>chicks_supplier_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="chicks_supplier_id"                data-endpoint="PUTapi-add2farm-flocks--flock-"
               value="1"
               data-component="body">
    <br>
<p>Chicks supplier ID. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>breed</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="breed"                data-endpoint="PUTapi-add2farm-flocks--flock-"
               value="Broiler,Cobb 500"
               data-component="body">
    <br>
<p>Breed name. Example: <code>Broiler,Cobb 500</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>start_date</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="start_date"                data-endpoint="PUTapi-add2farm-flocks--flock-"
               value="18-05-2026"
               data-component="body">
    <br>
<p>Start date (format: dd-mm-yyyy). Example: <code>18-05-2026</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>total_quantity</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="total_quantity"                data-endpoint="PUTapi-add2farm-flocks--flock-"
               value="12500"
               data-component="body">
    <br>
<p>Total number of chicks. Example: <code>12500</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>hangar_allocations</code></b>&nbsp;&nbsp;
<small>array</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="hangar_allocations"                data-endpoint="PUTapi-add2farm-flocks--flock-"
               value=""
               data-component="body">
    <br>
<p>Array of hangar allocations.</p>
        </div>
        </form>

                    <h2 id="add2farm-flocks-DELETEapi-add2farm-flocks--flock-">Delete a flock</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Delete a flock and all its hangar allocations.</p>

<span id="example-requests-DELETEapi-add2farm-flocks--flock-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://admin.eyt.test/api/add2farm/flocks/1" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/flocks/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-add2farm-flocks--flock-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Flock deleted successfully.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-add2farm-flocks--flock-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-add2farm-flocks--flock-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-add2farm-flocks--flock-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-add2farm-flocks--flock-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-add2farm-flocks--flock-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-add2farm-flocks--flock-" data-method="DELETE"
      data-path="api/add2farm/flocks/{flock}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-add2farm-flocks--flock-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-add2farm-flocks--flock-"
                    onclick="tryItOut('DELETEapi-add2farm-flocks--flock-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-add2farm-flocks--flock-"
                    onclick="cancelTryOut('DELETEapi-add2farm-flocks--flock-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-add2farm-flocks--flock-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/add2farm/flocks/{flock}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="DELETEapi-add2farm-flocks--flock-"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-add2farm-flocks--flock-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-add2farm-flocks--flock-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>flock</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="flock"                data-endpoint="DELETEapi-add2farm-flocks--flock-"
               value="1"
               data-component="url">
    <br>
<p>The flock ID. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="add2farm-profile">Add2Farm Profile</h1>

    

                                <h2 id="add2farm-profile-GETapi-add2farm-profile">Get user profile</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retrieve the authenticated user profile information.</p>

<span id="example-requests-GETapi-add2farm-profile">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://admin.eyt.test/api/add2farm/profile" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/profile"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-add2farm-profile">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Profile retrieved successfully.&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;John Doe&quot;,
        &quot;mobile_number&quot;: &quot;+1234567890&quot;,
        &quot;email&quot;: &quot;john@example.com&quot;,
        &quot;type&quot;: 2,
        &quot;type_label&quot;: &quot;Farm Owner&quot;,
        &quot;status&quot;: &quot;Active&quot;,
        &quot;created_at&quot;: &quot;2026-08-07T10:30:00Z&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-add2farm-profile" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-add2farm-profile"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-add2farm-profile"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-add2farm-profile" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-add2farm-profile">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-add2farm-profile" data-method="GET"
      data-path="api/add2farm/profile"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-add2farm-profile', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-add2farm-profile"
                    onclick="tryItOut('GETapi-add2farm-profile');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-add2farm-profile"
                    onclick="cancelTryOut('GETapi-add2farm-profile');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-add2farm-profile"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/add2farm/profile</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-add2farm-profile"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-add2farm-profile"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-add2farm-profile"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="add2farm-profile-PUTapi-add2farm-profile">Update user profile</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Update the authenticated user profile information (name, email).</p>

<span id="example-requests-PUTapi-add2farm-profile">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://admin.eyt.test/api/add2farm/profile" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"John Doe Updated\",
    \"email\": \"john.updated@example.com\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/profile"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "John Doe Updated",
    "email": "john.updated@example.com"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-add2farm-profile">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Profile updated successfully.&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;John Doe Updated&quot;,
        &quot;mobile_number&quot;: &quot;+1234567890&quot;,
        &quot;email&quot;: &quot;john.updated@example.com&quot;,
        &quot;type&quot;: 2,
        &quot;type_label&quot;: &quot;Farm Owner&quot;,
        &quot;status&quot;: &quot;Active&quot;,
        &quot;created_at&quot;: &quot;2026-08-07T10:30:00Z&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;errors&quot;: {
        &quot;email&quot;: [
            &quot;The email has already been taken.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-add2farm-profile" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-add2farm-profile"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-add2farm-profile"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-add2farm-profile" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-add2farm-profile">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-add2farm-profile" data-method="PUT"
      data-path="api/add2farm/profile"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-add2farm-profile', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-add2farm-profile"
                    onclick="tryItOut('PUTapi-add2farm-profile');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-add2farm-profile"
                    onclick="cancelTryOut('PUTapi-add2farm-profile');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-add2farm-profile"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/add2farm/profile</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PUTapi-add2farm-profile"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-add2farm-profile"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-add2farm-profile"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="PUTapi-add2farm-profile"
               value="John Doe Updated"
               data-component="body">
    <br>
<p>User full name. Example: <code>John Doe Updated</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="PUTapi-add2farm-profile"
               value="john.updated@example.com"
               data-component="body">
    <br>
<p>User email address. Example: <code>john.updated@example.com</code></p>
        </div>
        </form>

                    <h2 id="add2farm-profile-GETapi-add2farm-profile-change-password">Change password</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Change the authenticated user password. Current password verification is required.</p>

<span id="example-requests-GETapi-add2farm-profile-change-password">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://admin.eyt.test/api/add2farm/profile/change-password" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"current_password\": \"currentpassword123\",
    \"password\": \"newpassword123\",
    \"password_confirmation\": \"newpassword123\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/profile/change-password"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "current_password": "currentpassword123",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
};

fetch(url, {
    method: "GET",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-add2farm-profile-change-password">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Password changed successfully.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;errors&quot;: {
        &quot;current_password&quot;: [
            &quot;The current password is incorrect.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-add2farm-profile-change-password" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-add2farm-profile-change-password"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-add2farm-profile-change-password"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-add2farm-profile-change-password" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-add2farm-profile-change-password">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-add2farm-profile-change-password" data-method="GET"
      data-path="api/add2farm/profile/change-password"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-add2farm-profile-change-password', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-add2farm-profile-change-password"
                    onclick="tryItOut('GETapi-add2farm-profile-change-password');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-add2farm-profile-change-password"
                    onclick="cancelTryOut('GETapi-add2farm-profile-change-password');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-add2farm-profile-change-password"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/add2farm/profile/change-password</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-add2farm-profile-change-password"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-add2farm-profile-change-password"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-add2farm-profile-change-password"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>current_password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="current_password"                data-endpoint="GETapi-add2farm-profile-change-password"
               value="currentpassword123"
               data-component="body">
    <br>
<p>Current password for verification. Example: <code>currentpassword123</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="GETapi-add2farm-profile-change-password"
               value="newpassword123"
               data-component="body">
    <br>
<p>New password (minimum 8 characters). Example: <code>newpassword123</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password_confirmation</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password_confirmation"                data-endpoint="GETapi-add2farm-profile-change-password"
               value="newpassword123"
               data-component="body">
    <br>
<p>New password confirmation. Example: <code>newpassword123</code></p>
        </div>
        </form>

                <h1 id="add2farm-supervisors-type-3">Add2Farm Supervisors (Type 3)</h1>

    

                                <h2 id="add2farm-supervisors-type-3-GETapi-add2farm-supervisors">List all supervisors</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Get paginated list of all supervisors with search and filtering.</p>

<span id="example-requests-GETapi-add2farm-supervisors">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://admin.eyt.test/api/add2farm/supervisors?page=1&amp;per_page=20&amp;search=Supervisor1" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/supervisors"
);

const params = {
    "page": "1",
    "per_page": "20",
    "search": "Supervisor1",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-add2farm-supervisors">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Supervisors retrieved successfully.&quot;,
    &quot;data&quot;: {
        &quot;current_page&quot;: 1,
        &quot;data&quot;: [
            {
                &quot;id&quot;: 1,
                &quot;name&quot;: &quot;Supervisor Name&quot;,
                &quot;mobile_number&quot;: &quot;+1234567890&quot;,
                &quot;email&quot;: &quot;supervisor@add2farm.local&quot;,
                &quot;type&quot;: 3,
                &quot;type_label&quot;: &quot;Supervisor&quot;,
                &quot;status&quot;: &quot;Active&quot;
            }
        ],
        &quot;total&quot;: 5,
        &quot;last_page&quot;: 1
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-add2farm-supervisors" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-add2farm-supervisors"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-add2farm-supervisors"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-add2farm-supervisors" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-add2farm-supervisors">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-add2farm-supervisors" data-method="GET"
      data-path="api/add2farm/supervisors"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-add2farm-supervisors', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-add2farm-supervisors"
                    onclick="tryItOut('GETapi-add2farm-supervisors');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-add2farm-supervisors"
                    onclick="cancelTryOut('GETapi-add2farm-supervisors');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-add2farm-supervisors"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/add2farm/supervisors</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-add2farm-supervisors"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-add2farm-supervisors"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-add2farm-supervisors"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-add2farm-supervisors"
               value="1"
               data-component="query">
    <br>
<p>optional Pagination page number. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-add2farm-supervisors"
               value="20"
               data-component="query">
    <br>
<p>optional Items per page. Default: 15. Example: <code>20</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>search</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="search"                data-endpoint="GETapi-add2farm-supervisors"
               value="Supervisor1"
               data-component="query">
    <br>
<p>optional Search by name or mobile number. Example: <code>Supervisor1</code></p>
            </div>
                </form>

                    <h2 id="add2farm-supervisors-type-3-POSTapi-add2farm-supervisors">Create a new supervisor</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Create a new supervisor account (Type 3).</p>

<span id="example-requests-POSTapi-add2farm-supervisors">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://admin.eyt.test/api/add2farm/supervisors" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"mobile_number\": \"+1234567890\",
    \"name\": \"John Supervisor\",
    \"email\": \"supervisor@example.com\",
    \"password\": \"password123\",
    \"password_confirmation\": \"password123\",
    \"notes\": \"Senior supervisor with 10 years experience\",
    \"image\": \"(binary)\",
    \"status\": \"Active\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/supervisors"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "mobile_number": "+1234567890",
    "name": "John Supervisor",
    "email": "supervisor@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "notes": "Senior supervisor with 10 years experience",
    "image": "(binary)",
    "status": "Active"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-add2farm-supervisors">
            <blockquote>
            <p>Example response (201):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Supervisor created successfully.&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;John Supervisor&quot;,
        &quot;mobile_number&quot;: &quot;+1234567890&quot;,
        &quot;email&quot;: &quot;supervisor@example.com&quot;,
        &quot;type&quot;: 3,
        &quot;type_label&quot;: &quot;Supervisor&quot;,
        &quot;status&quot;: &quot;Active&quot;,
        &quot;notes&quot;: &quot;Senior supervisor with 10 years experience&quot;,
        &quot;image&quot;: &quot;uploads/supervisors/supervisor_image_123.jpg&quot;,
        &quot;created_at&quot;: &quot;2026-08-07T10:30:00Z&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;errors&quot;: {
        &quot;mobile_number&quot;: [
            &quot;The mobile number has already been taken.&quot;
        ],
        &quot;email&quot;: [
            &quot;The email has already been taken.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-add2farm-supervisors" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-add2farm-supervisors"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-add2farm-supervisors"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-add2farm-supervisors" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-add2farm-supervisors">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-add2farm-supervisors" data-method="POST"
      data-path="api/add2farm/supervisors"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-add2farm-supervisors', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-add2farm-supervisors"
                    onclick="tryItOut('POSTapi-add2farm-supervisors');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-add2farm-supervisors"
                    onclick="cancelTryOut('POSTapi-add2farm-supervisors');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-add2farm-supervisors"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/add2farm/supervisors</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-add2farm-supervisors"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-add2farm-supervisors"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-add2farm-supervisors"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>mobile_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="mobile_number"                data-endpoint="POSTapi-add2farm-supervisors"
               value="+1234567890"
               data-component="body">
    <br>
<p>Supervisor mobile number with country code. Must be unique. Example: <code>+1234567890</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-add2farm-supervisors"
               value="John Supervisor"
               data-component="body">
    <br>
<p>Supervisor full name. Example: <code>John Supervisor</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-add2farm-supervisors"
               value="supervisor@example.com"
               data-component="body">
    <br>
<p>Supervisor email address. Example: <code>supervisor@example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-add2farm-supervisors"
               value="password123"
               data-component="body">
    <br>
<p>Password (minimum 8 characters). Example: <code>password123</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password_confirmation</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password_confirmation"                data-endpoint="POSTapi-add2farm-supervisors"
               value="password123"
               data-component="body">
    <br>
<p>Password confirmation. Example: <code>password123</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>notes</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="notes"                data-endpoint="POSTapi-add2farm-supervisors"
               value="Senior supervisor with 10 years experience"
               data-component="body">
    <br>
<p>Optional notes about the supervisor. Example: <code>Senior supervisor with 10 years experience</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>image</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="image"                data-endpoint="POSTapi-add2farm-supervisors"
               value="(binary)"
               data-component="body">
    <br>
<p>Profile image (jpeg, png, gif). Max 2MB. Example: <code>(binary)</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="POSTapi-add2farm-supervisors"
               value="Active"
               data-component="body">
    <br>
<p>Status (Active, Inactive, Disable). Example: <code>Active</code></p>
        </div>
        </form>

                    <h2 id="add2farm-supervisors-type-3-GETapi-add2farm-supervisors--supervisor-">Get a single supervisor</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retrieve detailed information of a specific supervisor.</p>

<span id="example-requests-GETapi-add2farm-supervisors--supervisor-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://admin.eyt.test/api/add2farm/supervisors/1" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/supervisors/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-add2farm-supervisors--supervisor-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Supervisor retrieved successfully.&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;Supervisor Name&quot;,
        &quot;mobile_number&quot;: &quot;+1234567890&quot;,
        &quot;email&quot;: &quot;supervisor@add2farm.local&quot;,
        &quot;type&quot;: 3,
        &quot;type_label&quot;: &quot;Supervisor&quot;,
        &quot;status&quot;: &quot;Active&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-add2farm-supervisors--supervisor-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-add2farm-supervisors--supervisor-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-add2farm-supervisors--supervisor-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-add2farm-supervisors--supervisor-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-add2farm-supervisors--supervisor-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-add2farm-supervisors--supervisor-" data-method="GET"
      data-path="api/add2farm/supervisors/{supervisor}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-add2farm-supervisors--supervisor-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-add2farm-supervisors--supervisor-"
                    onclick="tryItOut('GETapi-add2farm-supervisors--supervisor-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-add2farm-supervisors--supervisor-"
                    onclick="cancelTryOut('GETapi-add2farm-supervisors--supervisor-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-add2farm-supervisors--supervisor-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/add2farm/supervisors/{supervisor}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-add2farm-supervisors--supervisor-"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-add2farm-supervisors--supervisor-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-add2farm-supervisors--supervisor-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>supervisor</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="supervisor"                data-endpoint="GETapi-add2farm-supervisors--supervisor-"
               value="1"
               data-component="url">
    <br>
<p>The supervisor ID. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="add2farm-supervisors-type-3-PUTapi-add2farm-supervisors--supervisor-">Update a supervisor</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Update supervisor details including name, email, status and project assignments.</p>

<span id="example-requests-PUTapi-add2farm-supervisors--supervisor-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://admin.eyt.test/api/add2farm/supervisors/1" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"John Supervisor Updated\",
    \"email\": \"john.updated@example.com\",
    \"status\": \"Active\",
    \"notes\": \"Updated notes\",
    \"image\": \"(binary)\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/supervisors/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "John Supervisor Updated",
    "email": "john.updated@example.com",
    "status": "Active",
    "notes": "Updated notes",
    "image": "(binary)"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-add2farm-supervisors--supervisor-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Supervisor updated successfully.&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;John Supervisor Updated&quot;,
        &quot;mobile_number&quot;: &quot;+1234567890&quot;,
        &quot;email&quot;: &quot;john.updated@example.com&quot;,
        &quot;type&quot;: 3,
        &quot;status&quot;: &quot;Active&quot;,
        &quot;notes&quot;: &quot;Updated notes&quot;,
        &quot;image&quot;: &quot;uploads/supervisors/supervisor_image_456.jpg&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-add2farm-supervisors--supervisor-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-add2farm-supervisors--supervisor-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-add2farm-supervisors--supervisor-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-add2farm-supervisors--supervisor-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-add2farm-supervisors--supervisor-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-add2farm-supervisors--supervisor-" data-method="PUT"
      data-path="api/add2farm/supervisors/{supervisor}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-add2farm-supervisors--supervisor-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-add2farm-supervisors--supervisor-"
                    onclick="tryItOut('PUTapi-add2farm-supervisors--supervisor-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-add2farm-supervisors--supervisor-"
                    onclick="cancelTryOut('PUTapi-add2farm-supervisors--supervisor-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-add2farm-supervisors--supervisor-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/add2farm/supervisors/{supervisor}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PUTapi-add2farm-supervisors--supervisor-"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-add2farm-supervisors--supervisor-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-add2farm-supervisors--supervisor-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>supervisor</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="supervisor"                data-endpoint="PUTapi-add2farm-supervisors--supervisor-"
               value="1"
               data-component="url">
    <br>
<p>The supervisor ID. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="PUTapi-add2farm-supervisors--supervisor-"
               value="John Supervisor Updated"
               data-component="body">
    <br>
<p>Supervisor's full name. Example: <code>John Supervisor Updated</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="PUTapi-add2farm-supervisors--supervisor-"
               value="john.updated@example.com"
               data-component="body">
    <br>
<p>Email address. Example: <code>john.updated@example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="PUTapi-add2farm-supervisors--supervisor-"
               value="Active"
               data-component="body">
    <br>
<p>Status (Active, Inactive, Disable). Example: <code>Active</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>notes</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="notes"                data-endpoint="PUTapi-add2farm-supervisors--supervisor-"
               value="Updated notes"
               data-component="body">
    <br>
<p>Optional notes about the supervisor. Example: <code>Updated notes</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>image</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="image"                data-endpoint="PUTapi-add2farm-supervisors--supervisor-"
               value="(binary)"
               data-component="body">
    <br>
<p>Profile image (jpeg, png, gif). Max 2MB. Example: <code>(binary)</code></p>
        </div>
        </form>

                    <h2 id="add2farm-supervisors-type-3-DELETEapi-add2farm-supervisors--supervisor-">Delete a supervisor</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Delete a supervisor and their project status records.</p>

<span id="example-requests-DELETEapi-add2farm-supervisors--supervisor-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://admin.eyt.test/api/add2farm/supervisors/1" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://admin.eyt.test/api/add2farm/supervisors/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-add2farm-supervisors--supervisor-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Supervisor deleted successfully.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-add2farm-supervisors--supervisor-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-add2farm-supervisors--supervisor-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-add2farm-supervisors--supervisor-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-add2farm-supervisors--supervisor-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-add2farm-supervisors--supervisor-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-add2farm-supervisors--supervisor-" data-method="DELETE"
      data-path="api/add2farm/supervisors/{supervisor}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-add2farm-supervisors--supervisor-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-add2farm-supervisors--supervisor-"
                    onclick="tryItOut('DELETEapi-add2farm-supervisors--supervisor-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-add2farm-supervisors--supervisor-"
                    onclick="cancelTryOut('DELETEapi-add2farm-supervisors--supervisor-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-add2farm-supervisors--supervisor-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/add2farm/supervisors/{supervisor}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="DELETEapi-add2farm-supervisors--supervisor-"
               value="Bearer {YOUR_AUTH_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-add2farm-supervisors--supervisor-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-add2farm-supervisors--supervisor-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>supervisor</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="supervisor"                data-endpoint="DELETEapi-add2farm-supervisors--supervisor-"
               value="1"
               data-component="url">
    <br>
<p>The supervisor ID. Example: <code>1</code></p>
            </div>
                    </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                            </div>
            </div>
</div>
</body>
</html>
