
# Acme Demo Social Application

## Overview

App for Collect tweet content.


## Demo

API [http://be.biagetti.info/api](http://be.biagetti.info/api)

BACKEND: [http://be.biagetti.info/admin](http://be.biagetti.info/admin)

- Sample(ReadOnly) admin user: guest/guest
- Admin user: admin/*****

An angularjs frontend client is located at [http://fe.biagetti.info](http://fe.biagetti.info)


## Configuration:

Generate a Consumer Key and Secret on the Twitter Application Management console.
Set in the parameters.

Set an admin password for admin user administration pourpose

## Task:

- acme:social:tweet:search   fetch tweets by a given tag   
  aguments: 
  - tagname (optional: default = php)

- acme:social:tweet:import  fetch tweets by a given tag and import
  - aguments: 
    - tagname (optional: default = php)
  - options:
    - auto-approve: auto approve twitter content (default: false)


## Backend:

- /admin/tweet CRUD and moderation functionality for Tweet content
- /admin/tag CRUD  for Tag
- /admin/author CRUD for Author

## API

Main End-point:

- GET    /api                       Api description
- GET    /api/posts				   App Posts
- GET    /api/posts/{id}            Post detail
- GET    /api/authors               List of all Authors
- GET    /api/authors/{id}          Author detail
- GET    /api/authors/{id}/posts    All Post of a specific Author
- GET    /api/tags                  All Tag
- GET    /api/tags/{id}             Tag detail
- GET    /api/tags/{id}/posts       All Post of a specific Tag


