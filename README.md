# aaxis-test

This is a technical test for AAXIS, use port 80 to access the API.
Read the disclaimer at the end of the document if you are using Docker on Windows.

## Installation

### STEP 1: 
Clone the repository

### STEP 2:
run the following command to build and run the containers
```bash
docker-compose up -d
```

### STEP 3:
Run the following command to enter the container

```bash 
docker exec -it -w /var/www/symfony custom-php bash
```
### STEP 4:
run "composer install" (Inside the container)
or run 
```bash
docker exec -it -w /var/www/symfony custom-php composer install
```
(Outside the container)
### STEP 5:
run "symfony console doctrine:migrations:migrate" to execute the migrations
or 
```bash
docker exec -it -w /var/www/symfony custom-php symfony console doctrine:migrations:migrate --no-interaction
```
(Outside the container)
### STEP 6:
run "symfony console app:create-user" to create a user
by default the user email is "aaxis@test.com" and the password is "aaxis_test"

```bash
docker exec -it -w /var/www/symfony custom-php symfony console app:create-user
```
(Outside the container)

## Commands

There are two commands available:
1. app:create-user: Creates a user with the parameters email and password. If none is provided it will create a user with 
email => aaxis@test.com and password => aaxis_test
2. app:get-user : Gets the user with the provided email or ID.

## Usage

1. When using Postman, Insomania or other use http://localhost:80
2. There is a DB adminer in http://localhost:8080, use it to check the DB if needed
3. There is a json file in the root of the project called "requests.json" that can be imported to Postman to test the API
****
## Routes
**Token chosen:**
For the simplicity of the test I chose to use an API KEY token. Each user has an API KEY that is generated when the user is created.
All /api/products endpoints require the API KEY to be sent in the header as "X-API-KEY" to be able to access the endpoint.
**Example**
```bash
curl -X POST -H "Content-Type: application/json" -H "X-API-KEY: your-api-key" -d '[
    {
        "sku": "123458",
        "product_name": "Producto 1",
        "description": "Descripción del Producto 1"
    }
]' http://localhost:80/api/products
```


### POST http://localhost:80/api/products
**Payload Schema:**

The endpoint expects the following JSON structure in the request payload:

```json
{
  "$schema": "http://json-schema.org/draft-07/schema#",
  "type": "array",
  "items": {
    "type": "object",
    "properties": {
      "sku": {
        "type": "string",
        "description": "The unique identifier for the product"
      },
      "product_name": {
        "type": "string",
        "description": "The name of the product"
      },
      "description": {
        "type": "string",
        "description": "A brief description of the product"
      }
    },
    "required": ["sku", "product_name"],
    "additionalProperties": false
  },
  "headers": {
    "type": "object",
    "properties": {
      "X-API-KEY": {
        "type": "string",
        "description": "The API key for authentication"
      }
    },
    "required": ["X-API-KEY"]
  }
}
```
**Example Response for Partial Success (200):**

This is an example of a response when some products are successfully added and others fail:

```json
{
  "status": "Partial success",
  "data": {
    "added_products": [
      "123488"
    ],
    "failed_products": [
      {
        "sku": "12351",
        "error": "SKU already exists"
      }
    ]
  }
}
```
**Example Response for Success (201):**
```json
{
  "status": "All products added successfully!",
  "data": {
    "added_products": [
      "123488"
    ]
  }
}
```
### PATCH http://localhost:80/api/products
**Payload Schema:**
The endpoint expects the following JSON structure in the request payload:
```json
{
  "$schema": "http://json-schema.org/draft-07/schema#",
  "type": "array",
  "items": {
    "type": "object",
    "properties": {
      "sku": {
        "type": "string",
        "description": "The unique identifier for the product"
      },
      "product_name": {
        "type": "string",
        "description": "The name of the product"
      },
      "description": {
        "type": "string",
        "description": "A brief description of the product"
      }
    },
    "required": ["sku", "product_name"],
    "additionalProperties": false
  },
  "headers": {
    "type": "object",
    "properties": {
      "X-API-KEY": {
        "type": "string",
        "description": "The API key for authentication"
      }
    },
    "required": ["X-API-KEY"]
  }
}
```
**Example Response for Partial Success (200):**

This is an example of a response when some products are successfully added and others fail:

```json
{
  "status": "Partial success",
  "data": {
    "updated_products": [
      "12345"
    ],
    "failed_products": [
      {
        "sku": "AA",
        "error": "Product not found"
      }
    ]
  }
}
```
**Example Response for Success (200):**
```json
{
  "status": "All products updated successfully!"
}
```
### GET http://localhost:80/api/products
**Response Format:**

The endpoint returns a JSON array of product objects. Each object in the array includes product details. Below is an example of the structure of each product object in the array:

```json
{
  "type": "array",
  "items": {
    "type": "object",
    "properties": {
      "id": {
        "type": "integer",
        "description": "The unique identifier of the product"
      },
      "sku": {
        "type": "string",
        "description": "The unique identifier for the product"
      },
      "productName": {
        "type": "string",
        "description": "The name of the product"
      },
      "description": {
        "type": "string",
        "description": "A brief description of the product"
      }
    }
  },
  "headers": {
    "type": "object",
    "properties": {
      "X-API-KEY": {
        "type": "string",
        "description": "The API key for authentication"
      }
    },
    "required": ["X-API-KEY"]
  }
}
```
***
### Extra commands
### POST http://localhost:80/api/user
With this endpoint you can retrieve the API KEY of a user by providing the email and password of the user.
**Payload Schema:**
This endpoint expects the following JSON structure in the request payload:

```json
{
  "type": "object",
  "properties": {
    "email": {
      "type": "string",
      "format": "email"
    },
    "password": {
      "type": "string"
    }
  },
  "required": ["email", "password"]
}
```

### POST http://localhost:80/api/user/create
With this endpoint you can create a user.
**Payload Schema:**
This endpoint expects the following JSON structure in the request payload:

```json
{
  "type": "object",
  "properties": {
    "email": {
      "type": "string",
      "format": "email"
    },
    "password": {
      "type": "string"
    }
  },
  "required": ["email", "password"]
}
```
## Disclaimer
When using Docker on Windows, the user
might experience slow performance with bind-volumes. While there are 
several ways to address this, they are considered outside the scope of
this challenge. One approach is to copy the project into the nginx container
and run it from there.
