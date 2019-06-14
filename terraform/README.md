TODO list
---------

 - **Comlete database tests**
 - Cluster multi-master ?
 - List all required APIs and autorizations
 - Create a custom `VPC` ?
 - Check that the created cluster is configured the way we want it to be: c.f. params values
 - Delete the default node pool ?
 - bucket: use bucket-level authorization strategy ?
    -> How to do it ?

Goals
-----

**We currently want to use Terraform to provision 3 kinds of objects:**
 - PostgreSQL databases
 - Kubernetes clusters (without the apps)
 - Google Cloud Storage buckets
 - DB with auto backups ?
 - Bucket: set bucket level authorization strategy ?
    -> How to do it ?

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

**Actual Terraform configuration:**
 - The db config was the first test,
   so its development is rather basic.
 - The cluster_kube config was made using an exemple from the following post:
    -> https://github.com/terraform-providers/terraform-provider-google/issues/3746
   and so looks a lot better...

**Targetted Terraform configuration:**
 - The same way it's done for cluster_kube config
 - For now, and to keep it simple, the configuration of sub-components are separated
    - So there is less dependencies between them
    - So we can, if needed, use different Terraform version for 2 different sub-component
 - Using 4 config files
    - `providers.tf`: currently defining google and google-beta providers
    - `modules.tf:` use of required module based on input variables defined elsewere
    - `variables.tf:` input variables definitions
    - `vars.auto.tfvars:` input variables assignments

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

Tests
-----

Test on the cluster_kube config:
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


Test on the buckets config:
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

Test on the database:
 - Deploy a PostgreSQL database: **OK**
 - Define database name: **OK**
 - Define user name and password: **OK**
 - Connect to the database from **inside** the cluster: **KO**
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
