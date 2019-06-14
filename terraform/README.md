TODO
----

 - List required APIs and autorizations
 - Create a custom VPC ?
 - Check that the created cluster is configured the way we want it to be
 - Delete the default node pool ?

Goals
-----

**We currently want to use Terraform to provision 3 kinds of objects:**
 - PostgreSQL databases
 - Kubernetes clusters (without the apps)
 - Google Cloud Storage buckets

Design
------

**We will do this using Terraform modules:**
 - Available on the Terraform public registry: https://registry.terraform.io/
 - Verified by HashiCorp

**3 useful modules have been identified so far:**
 - sql-db:
    - URL: https://registry.terraform.io/modules/GoogleCloudPlatform/sql-db/google/1.1.1
    - Provide:
       - GCP hosted SQL databases (either MySQL or PostgreSQL)
       - Database user account
 - kubernetes-engine:
    - URL: https://registry.terraform.io/modules/terraform-google-modules/kubernetes-engine/google/2.1.0
    - Provide:
       - Kubernetes cluster and node pools provioning
       - Service account creation
 - vpc:
    - URL: https://registry.terraform.io/modules/terraform-google-modules/network/google/0.8.0
    - Provide:
       - VPC creation

**Terraform configuration:**
 - The db config was the first test,
   so its development is rather basic.
 - The cluster_kube config was made using an exemple from the following post:
    -> https://github.com/terraform-providers/terraform-provider-google/issues/3746
   and so looks a lot better...

Compatibility
-------------

**We cannot currently use Terraform last version:**
 - Terraform v0.12.x
    - Include breaking changes
    - v0.12 was released around may 20th, 2019
 - The modules mentioned above are therefore not yet compatibles with Terraform v0.12
    -> But they are compatible with the latest v0.11.x version: v0.11.4
    -> So we will use Terraform v0.11.4 whenever needed, until the necessary module updates are released

Authentication & authorization
------------------------------

**GCP required APIs:**
 - gke
 - gcp sql
 - TODO list other required APIs

**GCP authorizations:**
 - module.gke.google_project_iam_member.cluster_service_account-log_writer: 1 error occurred:
	* google_project_iam_member.cluster_service_account-log_writer: Error applying IAM policy for project "padok-training-lab": Error setting IAM policy for project "padok-training-lab": googleapi: Error 403: The caller does not have permission, forbidden
   => Note required if we use an existing service account

Usage
-----

**Actions:**
 - Download Terraform v0.11.4 from the following URL:
    -> https://releases.hashicorp.com/terraform/0.11.14/terraform_0.11.14_linux_amd64.zip
 - Unzip, then copy the binary to /opt folder (for example) under the name "terraform_0.11.4"
 - ln -s /opt/terraform_0.11.4 /usr/local/bin/terraform_0.11.4
 - From a directory containing terraform conf (e.g. ./db or ./cluster_kube/):
     - terraform_0.11.4 init
     - terraform_0.11.4 plan -out config.tfplan
     - terraform_0.11.4 apply config.tfplan
 - Update kubernetes conf
     - gcloud container clusters --region europe-west4 get-credentials bam-stack-api

**Expected result:**
 - Create of a kubernetes master node (or more than one ?)
 - Create of node pool
 - Reuse of the default VPC
 - Update masq agent conf
