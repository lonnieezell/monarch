<h1>API Exploration</h1>

<div>
    <button id="get-json" hx-get="/api/foo" hx-target="#json-wrap">Get JSON</button>
    <button id="post-json" hx-post="/api/foo" hx-target="#json-wrap">Post JSON</button>
    <button id="put-json" hx-put="/api/foo" hx-target="#json-wrap">Put JSON</button>
    <button id="delete-json" hx-delete="/api/foo" hx-target="#json-wrap">Delete JSON</button>
</div>

<article id="json-wrap">
    Here comes target of request
</article>
