# BAM stack in the cloud

This project is about making BAM stack functional and easy to deploy in the cloud.

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

# Brief about the BAM stack

The BAM stack is composed of :
 - The Node app the user will send request to
 - A PostgreSQL database
 - AN external Firebase project, only used for user authentication
