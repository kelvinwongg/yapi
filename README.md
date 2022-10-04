# YAPI
A zero-configuration, single declarative YAML document, HTTP CRUD API framwork.

# Story
You want to quickly setup a API endpoint for some data. There are many ways and frameworks that does that. But the problem is, as a professional developer, you want to work straight into the business, not spending your life for a repetitive story from every frameworks that come across:

> You've spending weeks or months on learning a new framwork, reading its documentations, testing and playing along. Then, for the deliverable, you write a RESTful endpoint that simply do CRUD operations from a database.

In fact this is most of the time in my career that I spent a lot of time (or months) on doing this whenever I go to a new company, just to simply store form data into a database, or even no one would bother reading this data later. And most of the study and learning are just mapping the new jargon or workflow into what you have learnt many time before.

Recently I went to a new job, and going the same process again. This time I have to tackle the poorly documented Drupal, and tried to use it as a CMS and a application framework for some custom JSON api. THe feeling of life-wasting came again, makes me want to quit. Then a thought blinks into my mind: Why cannot I write a YAML file that define a JSON api with simple CRUD operation, just like what I did with Docker and Kubernetes?

**The YAPI project is just does that!**

# How it works
YAPI is complying with OAS3.
YAPI is all about a YAML file, You feed it with a YAML file, and it does things for you.
Be it construct a database and create a RESTful API endpoints that does normal CRUD operation to the database.
You can also hook into the endpoints and doing just whatever you want with your own programming languages, or opt-in to response to client with the methods provided by YAPI.
