# YAPI
A zero-configuration, single declarative YAML document, HTTP CRUD API framwork.

# Installation
Install YAPI in your project root via `composer`
```sh
composer require kelvinwongg/yapi
```

# Usage
`/api.yaml`
```yml
openapi: 3.0.0

info:
  version: 1.0.0
  title: 'HR API'

paths:
  /employees:
    get:
      description: 'Obtain information about all employees from the HR database'
      parameters: 
        - name: bodyLimit
          in: query
          required: true
          description: The amount of employees returned
          schema:
            type: integer
            minimum: 10
            maximum: 20
            example: 15
        - name: pageLimit
          in: query
          # required: true
          description: The pages to return employees info
          schema:
            type: integer
            minimum: 1
            maximum: 5
            example: 2
      responses:
        200:
          description: Successful pull of employee info
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/Employees'

components:
  schemas:
    Employees:
      description: 'Array of employee info'
      type: array
      items:
        $ref: '#/components/schemas/Employee'
    Employee:
      description: 'Model containing employee info'
      type: object
      properties:
        id:
          type: integer
          example: 4
        employee_name:
          type: string
          example: Ryan Pinkham
        employee_title:
          type: string
          example: Market Manager
```

`/index.php`
```php
require_once __DIR__ . '/vendor/autoload.php';
$yapi = new \Kelvinwongg\Yapi\Yapi('./api.yaml');
```

`/paths/Employees.php`
```php
<?php

class Employees
{	
	public function get($file, $request, $response, $crudHook)
	{
		$response->setContent([
			[
				'id' => 1,
				'employee_name' => 'Steve Davis',
				'employee_title' => 'Staff'
			],
			[
				'id' => 2,
				'employee_name' => 'Ronnie Hart',
				'employee_title' => 'Admin'
			]
		]);
	}
}
```

**Request**
```console
curl -L -X GET 'http://localhost/employees?bodyLimit=10'
```

**Response**
```json
[
  {"id":1,"employee_name":"Steve Davis","employee_title":"Staff"},
  {"id":2,"employee_name":"Ronnie Hart","employee_title":"Admin"}
]
```

# How it works
1. YAPI is complying with OAS3.

2. YAPI is all about a YAML file, You feed it with a YAML file, and it does things for you.

3. Be it construct a database and create a RESTful API endpoints that does normal CRUD operation to the database.

4. You can also hook into the endpoints and doing just whatever you want with your own programming languages, or opt-in to response to client with the methods provided by YAPI.

# The Story
You want to quickly setup a API endpoint for some data. There are many ways and frameworks that does that. But the problem is, as a professional developer, you want to work straight into the business, not spending your life for a repetitive story from every frameworks that come across:

> You've spending weeks or months on learning a new framwork, reading its documentations, testing and playing along. Then, for the deliverable, you write a RESTful endpoint that simply do CRUD operations from a database.

In fact this is most of the time in my career that I spent a lot of time (or months) on doing this whenever I go to a new company, just to simply store form data into a database, or even no one would bother reading this data later. And most of the study and learning are just mapping the new jargon or workflow into what you have learnt many time before.

Recently I went to a new job, and going the same process again. This time I have to tackle the poorly documented Drupal, and tried to use it as a CMS and a application framework for some custom JSON api. THe feeling of life-wasting came again, makes me want to quit. Then a thought blinks into my mind: Why cannot I write a YAML file that define a JSON api with simple CRUD operation, just like what I did with Docker and Kubernetes?

### The YAPI project is just does that!