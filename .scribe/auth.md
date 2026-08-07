# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer {YOUR_AUTH_TOKEN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

You can retrieve your token by registering or logging in at the `/api/add2farm/auth/register` or `/api/add2farm/auth/login` endpoints.
