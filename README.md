# Don't use, go to gitlab -> https://gitlab.com/padok-team/Padok-Stack
# Padok Stack

This repository contains all the resources used to build and deploy a standard Padok Cloud Stack:
* [Terraform code](./terraform/README.md) to build cloud infrastructure on GCP or AWS.
* Dockerization instructions for standard nodeJS (such as a BAM stack) or PHP applications
* Helm charts to deploy dockerized apps in the cloud
* Standard prometheus/grafana monitoring and alerting stack.


---

This project is about making **BAM stack** functional and easy to deploy in the cloud.

To make this happen you will have to:
 1. [Init & Dockerize the app](docker/README.md):
     - Use BAM tools in order to initiate a bam-stack-like GIT repository
     - Include some Docker files
     - Manage Firebase authent
     - Customize the app config
 2. [Provision your environment with Terraform](terraform/README.md):
     - Create a GKE Kubernetes cluster
     - Create a GCP SQL Database
     - Create GCS buckets
 3. [Deploy the app with Helm](kubernetes/README.md)

**Careful:** the part 3 won't work if you didn't complete part 1 and 2 first.

# How we want to send the BAM stack to the cloud

**Hypothesis:** A BAM stack project is created using the create-bam-stack repo that kind of duplicate itself in order to produce two child, isolated, repo.

So this repo should give you everything you need (conf, READMEs, ...) to:
 1. Transform the app in the child repos into Dockerized apps
 2. Create every resource needed to deploy the app in the cloud

# How to use this repo

This repo contains three kind of things:
 - Automatic processes, run from this repo
    - In this case you just need to customize the configuration to your need,
    - and run the proper tool with this conf (E.g Terraform)
 - Automatic processes, run from create-bam-stack repo
    - In this case you first need to copy some files to the targetted repo,
    - then to customize configuration
    - And then finaly to run the tool (E.g. Docker)
 - Manual processes
    - Described within READMEs

So just follow the READMEs mentioned at the beginning of this page and be ready to perform either manual or automatic processes, either in this repo or in the child ones.  

# Brief about the BAM stack

The BAM stack is composed of :
 - **The Node app the user will send request to**
    - Listen: 3000
    - Answer to URL:
       - "/": answer 200 OK
       - "/graphql": display a webpage where you can make requests
    - config:
       - Format: YAML
       - Params:
          - db_host
          - db_name
          - user
          - password
    - Authent:
       - To the database with user/password
       - To Firebase with a key
 - **A PostgreSQL database**
    - Listen: 5432
    - Should have a db, user and password pre-configured
 - **An external Firebase project, only used for user authentication**
    - With an existing project
    - The user that will need to use the BAM stack need to have proper access to this project

# Targetted architecture

For a first run we will target the following architecture:
 - The app will be hosted in Google Cloud Platform
 - In GCP we will deploy, using Terraform
    - A Kubernetes cluster for the app itself
    - A SQL database, apart from the cluster
 - We will init Helm in the cluster (manually for now)
 - And using Helm we will deploy a packaged version of the Dockerized verison of the app
 - Which mean that the app need to be dockerized first
