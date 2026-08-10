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
                    <ul id="tocify-header-add2farm-farmers-type-4" class="tocify-header">
                <li class="tocify-item level-1" data-unique="add2farm-farmers-type-4">
                    <a href="#add2farm-farmers-type-4">Add2Farm Farmers (Type 4)</a>
                </li>
                                    <ul id="tocify-subheader-add2farm-farmers-type-4" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="add2farm-farmers-type-4-GETapi-add2farm-farmers">
                                <a href="#add2farm-farmers-type-4-GETapi-add2farm-farmers">List all farmers (Type 4)</a>
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
                    <ul id="tocify-header-add2farm-supervisors-type-3" class="tocify-header">
                <li class="tocify-item level-1" data-unique="add2farm-supervisors-type-3">
                    <a href="#add2farm-supervisors-type-3">Add2Farm Supervisors (Type 3)</a>
                </li>
                                    <ul id="tocify-subheader-add2farm-supervisors-type-3" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="add2farm-supervisors-type-3-GETapi-add2farm-supervisors">
                                <a href="#add2farm-supervisors-type-3-GETapi-add2farm-supervisors">List all supervisors (Type 3)</a>
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
        <li>Last updated: August 10, 2026</li>
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

        <h1 id="add2farm-farmers-type-4">Add2Farm Farmers (Type 4)</h1>

    <p>CRUD APIs for managing Type 4 (Farmers) in Add2Farm</p>

                                <h2 id="add2farm-farmers-type-4-GETapi-add2farm-farmers">List all farmers (Type 4)</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Get paginated list of all farmers with search and filtering.</p>

<span id="example-requests-GETapi-add2farm-farmers">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://admin.eyt.test/api/add2farm/farmers?page=1&amp;per_page=20&amp;search=John&amp;status=Active" \
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
    "search": "John",
    "status": "Active",
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
                &quot;mobile_number&quot;: &quot;+1987654321&quot;,
                &quot;email&quot;: &quot;farmer@add2farm.local&quot;,
                &quot;type&quot;: 4,
                &quot;type_label&quot;: &quot;Farmers&quot;,
                &quot;status&quot;: &quot;Active&quot;,
                &quot;created_by_name&quot;: &quot;Admin Name&quot;,
                &quot;created_at&quot;: &quot;2026-08-07T10:30:00Z&quot;
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
               value="John"
               data-component="query">
    <br>
<p>optional Search by name or mobile_number. Example: <code>John</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="GETapi-add2farm-farmers"
               value="Active"
               data-component="query">
    <br>
<p>optional Filter by status (Active, Inactive, Disable). Example: <code>Active</code></p>
            </div>
                </form>

                    <h2 id="add2farm-farmers-type-4-POSTapi-add2farm-farmers">Create a new farmer</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Create a new Type 4 (Farmer) admin account.
Type is automatically set to 4 and cannot be changed.
Type 2 (Farm Owner) can only assign 1 project per farmer.</p>

<span id="example-requests-POSTapi-add2farm-farmers">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://admin.eyt.test/api/add2farm/farmers" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"John Farmer\",
    \"mobile_number\": \"+1987654321\",
    \"email\": \"john@example.com\",
    \"password\": \"password123\",
    \"project_rows\": [
        {
            \"project_id\": 1,
            \"status\": \"Active\"
        }
    ],
    \"password_confirmation\": \"password123\"
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
    "name": "John Farmer",
    "mobile_number": "+1987654321",
    "email": "john@example.com",
    "password": "password123",
    "project_rows": [
        {
            "project_id": 1,
            "status": "Active"
        }
    ],
    "password_confirmation": "password123"
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
        &quot;name&quot;: &quot;John Farmer&quot;,
        &quot;mobile_number&quot;: &quot;+1987654321&quot;,
        &quot;type&quot;: 4,
        &quot;type_label&quot;: &quot;Farmers&quot;,
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
        &quot;mobile_number&quot;: [
            &quot;The mobile number has already been taken.&quot;
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
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-add2farm-farmers"
               value="John Farmer"
               data-component="body">
    <br>
<p>Farmer's full name. Example: <code>John Farmer</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>mobile_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="mobile_number"                data-endpoint="POSTapi-add2farm-farmers"
               value="+1987654321"
               data-component="body">
    <br>
<p>Unique mobile number with country code. Example: <code>+1987654321</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-add2farm-farmers"
               value="john@example.com"
               data-component="body">
    <br>
<p>optional Email address. Example: <code>john@example.com</code></p>
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
<p>Password (min 8 characters). Example: <code>password123</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>project_rows</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
<br>
<p>optional Array of project assignments. Type 2 can assign max 1.</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>project_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="project_rows.0.project_id"                data-endpoint="POSTapi-add2farm-farmers"
               value=""
               data-component="body">
    <br>
<p>Must match an existing stored value.</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="project_rows.0.status"                data-endpoint="POSTapi-add2farm-farmers"
               value="Active"
               data-component="body">
    <br>
<p>Example: <code>Active</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>Active</code></li> <li><code>Inactive</code></li> <li><code>Pending</code></li></ul>
                    </div>
                                    </details>
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
        &quot;status&quot;: &quot;Active&quot;,
        &quot;created_by_name&quot;: &quot;Farm Owner Name&quot;,
        &quot;created_at&quot;: &quot;2026-08-07T10:30:00Z&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Farmer not found.&quot;
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

<p>Update farmer details including name, email, status and project assignments. Mobile number and type cannot be changed.</p>

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
    \"project_rows\": [
        {
            \"project_id\": 1,
            \"status\": \"Active\"
        }
    ]
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
    "project_rows": [
        {
            "project_id": 1,
            "status": "Active"
        }
    ]
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
        &quot;type_label&quot;: &quot;Farmer&quot;,
        &quot;status&quot;: &quot;Active&quot;,
        &quot;created_by_name&quot;: &quot;Farm Owner Name&quot;,
        &quot;created_at&quot;: &quot;2026-08-07T10:30:00Z&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Farmer not found.&quot;
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
            <b style="line-height: 2;"><code>project_rows</code></b>&nbsp;&nbsp;
<small>array</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="project_rows"                data-endpoint="PUTapi-add2farm-farmers--farmer-"
               value=""
               data-component="body">
    <br>
<p>Array of project assignments.</p>
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
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Farmer not found.&quot;
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

    <p>CRUD APIs for managing Farms - Accessible to Type 2 (Farm Owner) and Type 3 (Supervisor)</p>

                                <h2 id="add2farm-farms-GETapi-add2farm-farms">List all farms</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Get paginated list of all farms with search and filtering.
Accessible to Type 2 (Farm Owner) and Type 3 (Supervisor).</p>

<span id="example-requests-GETapi-add2farm-farms">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://admin.eyt.test/api/add2farm/farms?page=1&amp;per_page=20&amp;search=Main+Farm&amp;type=Layer&amp;assigned_to=1" \
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
    "search": "Main Farm",
    "type": "Layer",
    "assigned_to": "1",
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
                &quot;assigned_to&quot;: 1,
                &quot;assigned_admin_name&quot;: &quot;John Supervisor&quot;,
                &quot;created_by_name&quot;: &quot;Admin Name&quot;,
                &quot;created_at&quot;: &quot;2026-08-07T10:30:00Z&quot;
            }
        ],
        &quot;total&quot;: 10,
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
               value="Main Farm"
               data-component="query">
    <br>
<p>optional Search by name or location. Example: <code>Main Farm</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="type"                data-endpoint="GETapi-add2farm-farms"
               value="Layer"
               data-component="query">
    <br>
<p>optional Filter by farm type. Example: <code>Layer</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>assigned_to</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="assigned_to"                data-endpoint="GETapi-add2farm-farms"
               value="1"
               data-component="query">
    <br>
<p>optional Filter by assigned admin ID. Example: <code>1</code></p>
            </div>
                </form>

                    <h2 id="add2farm-farms-POSTapi-add2farm-farms">Create a new farm</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Create a new farm record. Accessible to Type 2 and Type 3 users.
Type 2 (Farm Owner) can create maximum 3 farms.
Type 3 (Supervisor) can create unlimited farms.
Each farmer (Type 4) can be assigned to only 1 farm.</p>

<span id="example-requests-POSTapi-add2farm-farms">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://admin.eyt.test/api/add2farm/farms" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"Main Farm\",
    \"location\": \"Village A\",
    \"type\": \"Layer\",
    \"number_of_hangars\": 5,
    \"assigned_to\": 1
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
    "name": "Main Farm",
    "location": "Village A",
    "type": "Layer",
    "number_of_hangars": 5,
    "assigned_to": 1
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
        &quot;name&quot;: &quot;Main Farm&quot;,
        &quot;location&quot;: &quot;Village A&quot;,
        &quot;type&quot;: &quot;Layer&quot;,
        &quot;number_of_hangars&quot;: 5,
        &quot;assigned_to&quot;: 1,
        &quot;assigned_admin_name&quot;: &quot;John Supervisor&quot;,
        &quot;created_at&quot;: &quot;2026-08-07T10:30:00Z&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (403):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;You have reached the maximum limit of 3 farms.&quot;
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
               value="Main Farm"
               data-component="body">
    <br>
<p>Farm name. Example: <code>Main Farm</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>location</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="location"                data-endpoint="POSTapi-add2farm-farms"
               value="Village A"
               data-component="body">
    <br>
<p>Farm location. Example: <code>Village A</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="type"                data-endpoint="POSTapi-add2farm-farms"
               value="Layer"
               data-component="body">
    <br>
<p>Farm type (Layer, Broiler, etc.). Example: <code>Layer</code></p>
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
<p>Number of hangars. Example: <code>5</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>assigned_to</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="assigned_to"                data-endpoint="POSTapi-add2farm-farms"
               value="1"
               data-component="body">
    <br>
<p>optional Admin ID (Type 4 Farmer) to assign this farm to. Example: <code>1</code></p>
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
        &quot;assigned_to&quot;: 1,
        &quot;assigned_to_name&quot;: &quot;Farmer Name&quot;,
        &quot;created_by&quot;: 1,
        &quot;created_by_name&quot;: &quot;Farm Owner Name&quot;,
        &quot;created_at&quot;: &quot;2026-08-07T10:30:00Z&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Farm not found.&quot;
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
    \"number_of_hangars\": 5,
    \"assigned_to\": 2
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
    "number_of_hangars": 5,
    "assigned_to": 2
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
        &quot;number_of_hangars&quot;: 5,
        &quot;assigned_to&quot;: 2,
        &quot;assigned_to_name&quot;: &quot;Farmer Name&quot;,
        &quot;created_by&quot;: 1,
        &quot;created_by_name&quot;: &quot;Farm Owner Name&quot;,
        &quot;created_at&quot;: &quot;2026-08-07T10:30:00Z&quot;,
        &quot;updated_at&quot;: &quot;2026-08-10T14:22:00Z&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Farm not found.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;This farmer is already assigned to another farm. Each farmer can be assigned to only 1 farm.&quot;
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
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>assigned_to</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="assigned_to"                data-endpoint="PUTapi-add2farm-farms--farm-"
               value="2"
               data-component="body">
    <br>
<p>Farmer ID to assign the farm to. Example: <code>2</code></p>
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
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Farm not found.&quot;
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

                <h1 id="add2farm-supervisors-type-3">Add2Farm Supervisors (Type 3)</h1>

    <p>CRUD APIs for managing Type 3 (Supervisors) in Add2Farm</p>

                                <h2 id="add2farm-supervisors-type-3-GETapi-add2farm-supervisors">List all supervisors (Type 3)</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Get paginated list of all supervisors with search and filtering.</p>

<span id="example-requests-GETapi-add2farm-supervisors">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://admin.eyt.test/api/add2farm/supervisors?page=1&amp;per_page=20&amp;search=John&amp;status=Active" \
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
    "search": "John",
    "status": "Active",
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
                &quot;status&quot;: &quot;Active&quot;,
                &quot;created_by_name&quot;: &quot;Admin Name&quot;,
                &quot;created_at&quot;: &quot;2026-08-07T10:30:00Z&quot;
            }
        ],
        &quot;total&quot;: 10,
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
               value="John"
               data-component="query">
    <br>
<p>optional Search by name or mobile_number. Example: <code>John</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="GETapi-add2farm-supervisors"
               value="Active"
               data-component="query">
    <br>
<p>optional Filter by status (Active, Inactive, Disable). Example: <code>Active</code></p>
            </div>
                </form>

                    <h2 id="add2farm-supervisors-type-3-POSTapi-add2farm-supervisors">Create a new supervisor</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Create a new Type 3 (Supervisor) admin account.
Type is automatically set to 3 and cannot be changed.</p>

<span id="example-requests-POSTapi-add2farm-supervisors">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://admin.eyt.test/api/add2farm/supervisors" \
    --header "Authorization: Bearer {YOUR_AUTH_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"John Supervisor\",
    \"mobile_number\": \"+1234567890\",
    \"email\": \"john@example.com\",
    \"password\": \"password123\",
    \"project_rows\": [
        {
            \"project_id\": 1,
            \"status\": \"Active\"
        }
    ],
    \"password_confirmation\": \"password123\"
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
    "name": "John Supervisor",
    "mobile_number": "+1234567890",
    "email": "john@example.com",
    "password": "password123",
    "project_rows": [
        {
            "project_id": 1,
            "status": "Active"
        }
    ],
    "password_confirmation": "password123"
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
        &quot;type&quot;: 3,
        &quot;type_label&quot;: &quot;Supervisor&quot;,
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
        &quot;mobile_number&quot;: [
            &quot;The mobile number has already been taken.&quot;
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
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-add2farm-supervisors"
               value="John Supervisor"
               data-component="body">
    <br>
<p>Supervisor's full name. Example: <code>John Supervisor</code></p>
        </div>
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
<p>Unique mobile number with country code. Example: <code>+1234567890</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-add2farm-supervisors"
               value="john@example.com"
               data-component="body">
    <br>
<p>optional Email address. Example: <code>john@example.com</code></p>
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
<p>Password (min 8 characters). Example: <code>password123</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>project_rows</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
<br>
<p>optional Array of project assignments.</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>project_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="project_rows.0.project_id"                data-endpoint="POSTapi-add2farm-supervisors"
               value=""
               data-component="body">
    <br>
<p>Must match an existing stored value.</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="project_rows.0.status"                data-endpoint="POSTapi-add2farm-supervisors"
               value="Active"
               data-component="body">
    <br>
<p>Example: <code>Active</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>Active</code></li> <li><code>Inactive</code></li> <li><code>Pending</code></li></ul>
                    </div>
                                    </details>
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
        &quot;status&quot;: &quot;Active&quot;,
        &quot;created_by_name&quot;: &quot;Admin Name&quot;,
        &quot;created_at&quot;: &quot;2026-08-07T10:30:00Z&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Supervisor not found.&quot;
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

<p>Update supervisor details including name, email, status and project assignments. Mobile number and type cannot be changed.</p>

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
    \"project_rows\": [
        {
            \"project_id\": 1,
            \"status\": \"Active\"
        }
    ]
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
    "project_rows": [
        {
            "project_id": 1,
            "status": "Active"
        }
    ]
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
        &quot;type_label&quot;: &quot;Supervisor&quot;,
        &quot;status&quot;: &quot;Active&quot;,
        &quot;created_by_name&quot;: &quot;Admin Name&quot;,
        &quot;created_at&quot;: &quot;2026-08-07T10:30:00Z&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Supervisor not found.&quot;
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
            <b style="line-height: 2;"><code>project_rows</code></b>&nbsp;&nbsp;
<small>array</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="project_rows"                data-endpoint="PUTapi-add2farm-supervisors--supervisor-"
               value=""
               data-component="body">
    <br>
<p>Array of project assignments.</p>
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
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Supervisor not found.&quot;
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
