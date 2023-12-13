# aaxis-test

This is a technical test for AAXIS

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
docker exec -it -w /var/www/symfony custom-php symfony console doctrine:migrations:migrate
```
(Outside the container)
## Usage

1. When using Postman, Insomania or other use http://localhost:80
2. There is a DB adminer in http://localhost:8080, use it to check the DB if needed
3. There is a json file in the root of the project called "requests.json" that can be imported to Postman to test the API

## Routes
### POST http://localhost:80/api/products/add
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
  }
}
```
**Example Response for Partial Success (207):**

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
**Example Response for Success (200):**
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
### PATCH http://localhost:80/api/products/update
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
  }
}
```
**Example Response for Partial Success (207):**

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
### GET http://localhost:80/api/products/list
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
  }
}
```
## Disclaimer

When using Docker on Windows, the user
might experience slow performance with bind-volumes. While there are 
several ways to address this, they are considered outside the scope of
this challenge. One approach is to copy the project into the nginx container
and run it from there.
