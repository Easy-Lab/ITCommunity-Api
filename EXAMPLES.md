# Examples of Usage

**Notice:** Don't forget to add `Content-Type: application/json` to your requests.


**Get JWT token:**

```
{
	"username": "admin",
	"password": "developer"
}
```

**Get list of all users**

```
[GET] http://[host]/users
```

**Get second page the list**

```
[GET] http://[host]/users?page=2
```

By default if Request don't have`limit` parameter Response will return 10 results.

**Get 20 results per page**

```
[GET] http://[host]/users?limit=20
```

**Get unlimited results per page**
`[GET] http://[host]/users?limit=0`

You can combine freely combine all available parameters.

`[GET] http://[host]/users?limit=20&page=2`

**Get users with its reviews**
You can also expand book listing of it's reviews.

```
[GET] http://[host]/users?expand=reviews
[GET] http://[host]/users?expand=reviews&limit=20&page=2
```

## Filtering

**Get User of given username**

`[GET] http://[host]/users?user_filter[username]=username`
