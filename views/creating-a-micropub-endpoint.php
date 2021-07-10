<div class="narrow">
  <?= partial('partials/header') ?>

<?php ob_start() ?>
## Creating a Micropub Endpoint

After a client has obtained an access token and discovered the user's Micropub endpoint
it is ready to make requests to create posts.

### The Request

This is not intended to be a comprehensive guide to Micropub, and only includes the
fields that this client sends.

The request to create a post will be sent with as a standard HTTP form-encoded request
The example code here is written in PHP but the idea is applicable in any language.

The request will contain the following POST parameters:

* `h=entry` - Indicates the type of object being created, in this case an <a href="http://indiewebcamp.com/h-entry">h-entry</a>.
* `content` - The text content the user entered
* `category` - A comma-separated list of tags that you entered
* `location` - A "geo" URI including the latitude and longitude of the photo if included. (Will look like `geo:37.786971,-122.399677;u=50`, where u=50 indicates the "uncertainty" of the location in meters)
* `in-reply-to` - If set, this is a URL that the post is in reply to

The request will also contain an access token in the HTTP `Authorization` header:

<pre>
Authorization: Bearer XXXXXXXX
</pre>


### Verifying Access Tokens

Before you can begin processing the request, you must first verify the access token is valid
and contains at least the "post" scope.

How exactly you do this is dependent on your architecture. You can query the token endpoint
to check if an access token is still valid. See <a href="https://tokens.indieauth.com/#verify">tokens.indieauth.com</a>
for more information.

Once you have looked up the token info, you need to make a determination
about whether that access token is still valid. You'll have the following information
at hand that can be used to check:

* `me` - The user who this access token corresponds to.
* `client_id` - The app that generated the token.
* `scope` - The list of scopes that were authorized by the user.
* `issued_at` - The date the token was issued.

Keep in mind that it may be possible for another user besides yourself to have created
an access token at your token endpoint, so the first thing you'll do when verifying
is making sure the "me" parameter matches your own domain. This way you are the only
one that can create posts on your website.


### Validating the Request Parameters

A valid request to create a post will contain the parameters listed above. For now,
you can verify the presence of everything in the list, or you can try to genericize your
micropub endpoint so that it can also create <a href="http://ownyourgram.com/creating-a-micropub-endpoint">photo posts</a>.

At a bare minimum, a Micropub request will contain the following:

* `h=entry`
* `content`

The access token must also contain at least the "post" scope.


### The Response

Once you've validated the access token and checked for the presence of all required parameters,
you can create a post in your website with the information provided.

If a post was successfully created, the endpoint must return an `HTTP 201` response with a
`Location` header that points to the URL of the post. No body is required for the response.

<pre>
HTTP/1.1 201 Created
Location: http://example.com/post/100
</pre>

If there was an error, the response should include an HTTP error code as appropriate,
and optionally an HTML or other body with more information. Below is a list of possible errors.

* `HTTP 401 Unauthorized` - No access token was provided in the request.
* `HTTP 403 Forbidden` - An access token was provided, but the authenticated user does not have permission to complete the request.
* `HTTP 400 Bad Request` - Something was wrong with the request, such as a missing "h" parameter, or other missing data. The response body may contain more human-readable information about the error.



<?= \Michelf\Markdown::defaultTransform(ob_get_clean()) ?>
</div>
