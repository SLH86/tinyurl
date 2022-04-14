TINYURL
--------------------------------

This module 
1) automatically populates the slug field if left empty. 
2) Creates a redirect from /s/slug to the url given in the link field. 
3) Creates a GraphQL mutation allowing for the creating of URL nodes.

Creating URL from GraphQL
---------------------------

A URL can be created by running 

mutation {
  CreateUrl(data: { url: "https://unity.com", slug: "Unity" }) {
    ... on url {
      slug
      url
      id
    }
  }
}

Which will return

{
  "data": {
    "CreateUrl": {
      "slug": "Unity/1",
      "url": "https://unity.com",
      "id": 1
    }
  }
}