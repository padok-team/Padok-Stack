TODO list
---------

 - Use security group for API -> db comm ? (is this concept limited to AWS or also available with GCP)
 - DB with auto backups ?
 - List all required APIs and autorizations
 - Check that the created cluster is configured the way we want it to be: c.f. params values
 - bucket: use bucket-level authorization strategy ?
    -> How to do it ?
 - Create a custom `VPC` ?

 - Previously done:
    - Cluster multi-master -> Available through "regional" clusters
    - Delete the default node pool -> done through Terraform module conf
    - Complete database tests -> Only one still KO but it's about external access to the database

Goals
-----

**We currently want to use Terraform to provision 3 kinds of objects:**
 - PostgreSQL databases
 - Kubernetes clusters (without the apps)
 - Google Cloud Storage buckets

Prerequisite
------------

This Terraform project require the availability of the following resources:
 - A GCP project
 - A GCS bucket for Terraform backend
 - A service account with access to this project
 - The required APIs are activated

GCP design
----------

The 3 kinds of objects mentioned above:
 - belong to 3 different GCP functionalities
 - yet they can interact to with one another

**Network considerations:**
 - Sooner or later, we will want the apps in the Kubernetes cluster to be able to connect to the database
 - However:
    - The cluster is for now deployed in the `default VPC`
    - The database lies in the `Service Producer VPC`
 - So to enable private connections between apps and the database (i.e. without going through public Internet),
   you need to peer the VPCs
 - **Careful:** don't try to ping the database to test its connectivity, because inter VPC pings may not be enabled

Terraform design
----------------

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

**Actual Terraform configuration:**
 - The cluster_kube config was made using an exemple from the following post:
    -> https://github.com/terraform-providers/terraform-provider-google/issues/3746
   and so looks a lot better...
 - The db and buckets configs was done the same way

**Targetted Terraform configuration:**
 - The same way it's done for cluster_kube config
 - For now, and to keep it simple, the configuration of sub-components are separated
    - So there is less dependencies between them
    - So we can, if needed, use different Terraform version for 2 different sub-component
 - Using 5 config files
    - `terraform.tf`: Terraform main conf. As of today only only backend conf.
    - `providers.tf`: currently defining google and google-beta providers
    - `modules.tf:` use of required module based on input variables defined elsewere
    - `variables.tf:` input variables definitions
    - `vars.auto.tfvars:` input variables assignments
    - `main.tf`: resources not defined through module usage

Compatibility
-------------

**We cannot currently use Terraform last version:**
 - Terraform v0.12.x
    - Include breaking changes
    - v0.12 was released around may 20th, 2019
 - The modules mentioned above are therefore not yet compatibles with Terraform v0.12
    -> But they are compatible with the latest v0.11.x version: v0.11.4
    -> So we will use Terraform v0.11.4 whenever needed, until the necessary module updates are released

We will however use the latest version for compatible configs (E.g. buckets).

Authentication & authorization
------------------------------

**Terraform access to GCP:**
 - In order for Terraform to be able to use GCP APIs, you need to:
    - have a service account
    - download the associated API key
    - set the "credentials" variable in `vars_gen.auto.tfvars` file to the path of the key
 - Note that for testing purposes you can use the default service account of the padok-training-lab project

**GCP required APIs:**
 - gke
 - gcp sql
 - TODO list other required APIs

**GCP authorizations:**
 - module.gke.google_project_iam_member.cluster_service_account-log_writer: 1 error occurred:
	* google_project_iam_member.cluster_service_account-log_writer: Error applying IAM policy for project "padok-training-lab": Error setting IAM policy for project "padok-training-lab": googleapi: Error 403: The caller does not have permission, forbidden
   => Note required if we use an existing service account

Tests
-----

**Test on the cluster_kube config:**
 - Create a cluster and a VPC network: **OK**
 - Create a cluster, using the default VPC network: **OK**
 - Create a regional cluster: **OK**
    - Region: europe-west4
    - Zones: <region>-<a,b,c>
    - 1 node in each zone
 - Perform regular operational actions on the cluster: **OK**
    - gcloud ... get-credentials -> gives kubectl the proper credentials
    - kubectl get no -> all node are running
    - kubectl get po -> all admin pods are running, with all containers ready
    - kubectl exec -> OK
 - Terraform plan when the cluster is already there: **Not that good**
    - The module want to create new object even if they already exist
    - Why ?
 - Terraform import when the .tfstate file was lost or corrupted: **KO**
    Error message: "Error: Provider "kubernetes" depends on non-var "local.cluster_endpoint". Providers for import can currently
      only depend on variables or must be hardcoded. You can stop import
      fro m loading configurations by specifying `-config=""`."


**Test on the buckets config:**
 - Create 2 buckets: **OK**
 - Terraform plan when the cluster is already there: **OK**
    - Updated in case of minor changes
    - Unchanged when there are no changes to apply
 - Terraform import when the .tfstate file was lost or corrupted: **OK**
    - The "force_destroy" field is not imported though
    - However it seems to be without impact on instances,
    - and doesn't trigger a re-creation on later plan / apply
 - Terraform import when the .tfstate file is OK: **OK**
    - Terraform detect the resource already exist

**Test on the database:**
 - Deploy a PostgreSQL database: **OK**
 - Define database name: **OK**
 - Define user name and password: **OK**
 - Connect to the database from **inside** the cluster: **OK**
    - with private IP + peering network
 - Connect to the database from **outside** the cluster: **KO**
 - Terraform plan when the cluster is already there: **OK**
    - The user seams to always be re-created: probably because of the password definition
    - The database zone preference is always updated, even if not defined in the first place
      Probably because GCP automatically assign one in the chosen region upon creation
 - Terraform import when the .tfstate file is OK: **Not that bad**
    - The database_instance object is imported and only a little update will be trigger on next plan / apply
    - The database object is fully and correctly imported
    - The user object is imported but will need an update...
    - The password doesn't seem to be imortable...

Usage
-----

**Actions:**
 - Download Terraform v0.11.4 from the following URL:
    -> https://releases.hashicorp.com/terraform/0.11.14/terraform_0.11.14_linux_amd64.zip
 - Unzip, then copy the binary to /opt folder (for example) under the name "terraform_0.11.4"
 - ln -s /opt/terraform_0.11.4 /usr/local/bin/terraform_0.11.4
 - Update Terraform conf to your need, if needed:
    - Especially the `credential` var in `vars_gen.auto.tfvars`
 - From a directory containing terraform conf (e.g. ./db or ./cluster_kube/):
    - terraform_0.11.4 init
    - terraform_0.11.4 plan -out config.tfplan
    - terraform_0.11.4 apply config.tfplan
   Available conf dirs:
    - `./db`: deploy the database
    - `./cluster_kube`: deploy the Kubernetes cluster
    - `./buckets`: deploy 2 buckets
   **Careful**: use Terraform v0.12.x for buckets deployment
 - Update kubernetes conf
    - gcloud container clusters --region europe-west4 get-credentials bam-stack-api

**Expected result:**
 - Create of a kubernetes master node (or more than one ?)
 - Create of node pool
 - Reuse of the default VPC
 - Update masq agent conf
