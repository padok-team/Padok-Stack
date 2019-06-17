BAM stack in the cloud
======================

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
 3. [Deploy the app with Helm](README_kubernetes.md)
