# TaskList

OCSICO internship 2016 (WEB)

##Error handling 

This section describes error handling

###HTTP Status Codes

Code  | Text  | Description
:----:|-------|-------------
200   |OK     |Successful GET, PUT, Patch or RPC call
201   |Created|New resource added successfully
204   |No Response|Empty successful response (in case of DELETE requests)
400   |Bad Request|Indicates that the server cannot or will not process the request due to something which is perceived to be a client error
401   |Unauthorized|Authentication credentials were missing or incorrect.
403   |Forbidden|The request is understood, but it has been refused or access is not allowed. An accompanying error message will explain why.
404   |Not Found|The URI requested is invalid or the resource requested
409   |Conflict|The request could not be completed due to a conflict with the current state of the target resource. This code is used in situations where the user might be able to resolve the conflict and resubmit the request.
429   |Too Many Requests|Returned in API v1.1 when a request cannot be served due to the application’s rate limit having been exhausted for the resource.
500   |Server error|Please report developer team
503   |Service Unavailable|The server is currently unavailable (because it is overloaded or down for maintenance). Generally, this is a temporary state.


## User [/api/user]

###Get access token [POST|GET /oauth/v2/token]

+ Request 

        {
            "username" : "username",
            "password" : "password",
            "grant_type" : "password",
            "client_id" : "1_m1x3mkrnssg04k84wccwkoss0s4o48cgg0ok48ocgc8048w4c",
            "client_secret" : "39yb2i91dqw4w0wwggwsckwkogswssccw48gsosws4cog4o8os"
        }

    or

        {
            "refresh_token" : "refresh_token",
            "grant_type" : "refresh_token",
            "client_id" : "1_m1x3mkrnssg04k84wccwkoss0s4o48cgg0ok48ocgc8048w4c",
            "client_secret" : "39yb2i91dqw4w0wwggwsckwkogswssccw48gsosws4cog4o8os"
        }
        
+ Response 200

        {
            "access_token":"access_token",
            "expires_in":3600,
            "token_type":"bearer",
            "scope":null,
            "refresh_token":"refresh_token"
        }
  
+ Response 401

        {
             "error":"invalid_grant",
             "error_description":"The access token provided has expired."
        }

###User create [POST /api/user/reg]

+ Request

    + Headers
    
            Content-Type: "application/json"
        
    + Body
    
            {
                "username" : "username" ,
                "email" : "email",
                "plainPassword" : "plainPassword"
            }
            
+ Response 201

    + Headers
    
            Content-Type: "application/json"
        
    + Body
    
            {
                 "message" : "user created"
            }
            
+ Response 400

    + Headers
    
            Content-Type: "application/json"
        
    + Body
    
            [{
                 "property_path":"property_path",
                 "message":"message"
            }]

+ Response 409

    + Headers
    
            Content-Type: "application/json"
        
    + Body
    
            {
                 "error" : "user with the same username or email exists"
            }

###User get [GET /api/user]

+ Request
    
    + Headers
    
            Authorization: "Bearer access token"
            Content-Type: "application/json"
            
+ Response 200

    + Headers
    
            Authorization: "Bearer access token"
               Content-Type: "application/json"
        
    + Body
    
            {
                 "id" : "id",
                 "username" : "username",
                 "email" : "email"
            }

###User edit fields [PUT /api/user]
            
+ Request

    + Headers
    
            Authorization: "Bearer access token"
            Content-Type: "application/json"
        
    + Body
    
            {
                "username" : "new_username",
                "email" : "new_email",
                "plainPassword" : "plainPassword"
            }

+ Response 200

    + Headers
    
            Authorization: "Bearer access token"
               Content-Type: "application/json"
        
    + Body
    
            {
                 "message" : "user updated"
            }

+ Response 400

    + Headers
    
            Content-Type: "application/json"
        
    + Body
    
            [{
                 "property_path":"property_path",
                 "message":"message"
            }]

+ Response 409

    + Headers
    
            Content-Type: "application/json"
        
    + Body
    
            {
                 "error" : "user with the same username or email exists"
            }

### User change password [PATCH /api/user]

+ Request

    + Headers
    
            Authorization: "Bearer access token"
            Content-Type: "application/json"
        
    + Body
    
            {
                "newPassword" : "newPassword",
                "currentPassword" : "plainPassword"
            }

+ Response 200

    + Headers
    
            Authorization: "Bearer access token"
               Content-Type: "application/json"
        
    + Body
    
            {
                 "message" : "user updated"
            }

+ Response 400

    + Headers
    
            Content-Type: "application/json"
        
    + Body
    
            [{
                 "property_path":"property_path",
                 "message":"message"
            }]

+ Response 409

    + Headers
    
            Content-Type: "application/json"
        
    + Body
    
            {
                 "error" : "incorrect password"
            }

###Users get [GET /api/users]

+ Request

    + Headers
    
            Authorization: "Bearer access token"
            Content-Type: "application/json"
        

+ Response 200

    + Headers
    
            Authorization: "Bearer access token"
               Content-Type: "application/json"
        
    + Body
    
            [{
                 "id" : "id",
                 "username" : "username",
                 "email" : "email"
            }]

#### User get by {id} [GET /api/user/{id}]

+ Request

    + Headers
    
            Authorization: "Bearer access token"
            Content-Type: "application/json"
        
+ Response 200

    + Headers
    
            Authorization: "Bearer access token"
               Content-Type: "application/json"
        
    + Body
    
            {
                 "id" : "{id}",
                 "username" : "username",
                 "email" : "email"
            }

##TaskList [/tasklists]

###Get all tasklists [GET /tasklists] 

+ Request

    + Headers

            Authorization: "Bearer access token"
            Content-Type: "application/json"

+ Response 200

        [
          {
            "id": {id},
            "name": {name}
            "tasks": {tasks[]}
          },
        ]

###Get tasklist by id [GET /tasklists/{id}]

+ Request

    + Headers

            Authorization: "Bearer access token"
            Content-Type: "application/json"

+ Response 200

        {
          "id": {id},
          "name": {name}
          "tasks":{tasks[]}
        }

+ Response 404

        {
          "error": "Page not found."
        }

+ Response 403

        {
          "error": "Access denied."
        }

### Create new tasklist [POST /tasklists]

+ Request

    + Headers

            Authorization: "Bearer access token"
            Content-Type: "application/json"

    + Body 

            {
                "name": {name},
            } 

+ Response 201

        {
          "id": {id},
          "name": {name}
        } 

+ Response 422

        [
          {
            "property_path": "name",
            "message": "This value should not be blank."
          },
          {
            "property_path": "name",
            "message": "This value should not be null."
          }
        ]


###Update tasklist [PUT /tasklists/{id}]

+ Request

    + Headers

            Authorization: "Bearer access token"
            Content-Type: "application/json"

    + Body

            {
              "name": {name}
            } 

+ Response 200

        {
          "id": {id},
          "name": {name},
          "tasks": {tasks[]},
        } 

+ Response 404

        {
          "error": "Page not found."
        }

+ Response 403

        {
          "error": "Access denied."
        }

+ Response 422

        [
          {
            "property_path": "name",
            "message": "This value should not be blank."
          },
          {
            "property_path": "name",
            "message": "This value should not be null."
          }
        ]

### Delete tasklist [DELETE /tasklists/{id}]

+ Request

    + Headers

            Authorization: "Bearer access token"
            Content-Type: "application/json"

+ Response 204

+ Response 404

        {
          "error": "Page not found."
        }

+ Response 403

        {
          "error": "Access denied."
        }
        
