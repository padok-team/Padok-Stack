# Terraform

We use terraform to create Google Cloud resources such as buckets and Google Kubernetes Engine clusters.

## Table of contents
* [Install](#Install-⬆️)
* [Set up and configuration](#Set-up-and-configuration-⬆️)
  * [GCP Project name](#GCP-Project-name-⬆️)
  * [Google Cloud APIs](#Google-Cloud-APIs-⬆️)
  * [Service account](#Service-account-⬆️)
  * [State bucket](#State-bucket-⬆️)
* [Create resources with terraform](#Create-resources-with-terraform-⬆️)
* [Inspect created resources](#Inspect-created-resources-⬆️)
* [Buckets access](#Buckets-access-⬆️)

## Install

To install terraform, run the following:
```shell
$ wget https://releases.hashicorp.com/terraform/0.11.14/terraform_0.11.14_linux_amd64.zip
$ unzip terraform_0.11.14_linux_amd64.zip
$ sudo mv terraform /opt/terraform_0.11.4
$ sudo ln -s /opt/terraform_0.11.4 /usr/local/bin/terraform_0.11.4
```
_We currently use terraform 0.11.4 because terraform 0.12.X introduced breaking changes and all the Google Cloud terraform modules we use are not currently compatible vith terraform 0.12.X_

## Set up and configuration [⬆️](#Table-of-contents)

### GCP Project name [⬆️](#Table-of-contents)

To keep the commands in this README ad generic as possible, we use the `GCP_PROJECT` environment variable, you can set it with:
```shell
$ export GCP_PROJECT=<project_name>
```
We recommand the following convention:
* Staging project name : `<company>-staging`
* Production project name : `<company>-production`

### Google Cloud APIs [⬆️](#Table-of-contents)

To use terraform, you need to activate some Google Cloud APIs:
```shell
$ gcloud services enable sqladmin.googleapis.com --project="$GCP_PROJECT"
$ gcloud services enable compute.googleapis.com --project="$GCP_PROJECT"
$ gcloud services enable servicenetworking.googleapis.com --project="$GCP_PROJECT"
$ gcloud services enable cloudresourcemanager.googleapis.com --project="$GCP_PROJECT"
$ gcloud services enable container.googleapis.com --project="$GCP_PROJECT"
```

### Service account [⬆️](#Table-of-contents)

To use terraform, you need a keyfile from a service account with appropriate roles to create the GCP resources. If the service account has already been created, you only need to create and download a keyfile (last step):
* Create the terraform service account:
```shell
$ gcloud iam service-accounts create terraform --project="$GCP_PROJECT"
```
* Grant it the roles required to create GCP resources
  <details><summary><b>Show commands</b></summary>
  * To create buckets:
  ```shell
  $ gcloud projects add-iam-policy-binding "$GCP_PROJECT" --member serviceAccount:terraform@"$GCP_PROJECT".iam.gserviceaccount.com --role roles/storage.admin
  ```
  * To create CloudSQL databases:
  ```shell
  $ gcloud projects add-iam-policy-binding "$GCP_PROJECT" --member serviceAccount:terraform@"$GCP_PROJECT".iam.gserviceaccount.com --role roles/cloudsql.admin
  ```
  * To create GKE clusters:
  ```shell
  $ gcloud projects add-iam-policy-binding "$GCP_PROJECT" --member serviceAccount:terraform@"$GCP_PROJECT".iam.gserviceaccount.com --role roles/compute.admin
  $ gcloud projects add-iam-policy-binding "$GCP_PROJECT" --member serviceAccount:terraform@"$GCP_PROJECT".iam.gserviceaccount.com --role roles/container.admin
  $ gcloud projects add-iam-policy-binding "$GCP_PROJECT" --member serviceAccount:terraform@"$GCP_PROJECT".iam.gserviceaccount.com --role roles/iam.serviceAccountUser
  ```
  </details>
* download a json keyfile for the service account:
```shell
$ gcloud iam service-accounts keys create "$GCP_PROJECT"-terraform-credentials.json --iam-account=terraform@"$GCP_PROJECT".iam.gserviceaccount.com
```
_Execute this last command in the same directory as this README._

### State bucket [⬆️](#Table-of-contents)

You need a bucket to store terraform state. Create the bucket with:
```shell
$ gsutil mb -p "$GCP_PROJECT" -c regional -l europe-west4 gs://"$GCP_PROJECT"-terraform-state/
```
Grant access to the bucket to the terraform service account:
```shell
$ gsutil iam ch serviceAccount:terraform@"$GCP_PROJECT".iam.gserviceaccount.com:legacyBucketOwner gs://"$GCP_PROJECT"-terraform-state/
```

## Create resources with terraform [⬆️](#Table-of-contents)

We use terraform to create the GKE cluster, the CloudSQL database and the media and user buckets, for both staging and production environments.
We rely on the following external community maintained modules:
* [kubernetes-engine](https://registry.terraform.io/modules/terraform-google-modules/kubernetes-engine/google/3.0.0)
* [sql-db](https://registry.terraform.io/modules/GoogleCloudPlatform/sql-db/google/1.2.0)

Before runing the following commands, make sure to replace every occurence of `<company_name>`, `<production_project_name>` and `<staging_project_name>` in every file under `production` and `staging` with appropriate values.

To create a resource, go to the `<environment>/<resource>` directory and run the following comands:
* Initialize terraform:
```shell
$ terraform_0.11.4 init
```
* Generate a plan:
```shell
$ terraform_0.11.4 plan -out config.tfplan
```
* Apply the plan (create/edit the resources):
```shell
$ terraform_0.11.4 apply config.tfplan
```

## Inspect created resources [⬆️](#Table-of-contents)

You can check the resources you created on the Google Cloud Console:

* GKE cluster: [here](https://console.cloud.google.com/kubernetes/list)
* CloudSQL database: [here](https://console.cloud.google.com/sql/instances)
* Buckets: [here](https://console.cloud.google.com/storage/browser)

## Buckets access [⬆️](#Table-of-contents)

The first time you create the buckets, you have to set their IAM policies for the service account that the app will be using:
```shell
$ gsutil iam ch serviceAccount:<app_service_account_name>@"$GCP_PROJECT".iam.gserviceaccount.com:legacyBucketOwner gs://"$GCP_PROJECT"-frontoffice/
$ gsutil iam ch serviceAccount:<app_service_account_name>@"$GCP_PROJECT".iam.gserviceaccount.com:legacyBucketOwner gs://"$GCP_PROJECT"-backoffice/
```

## Cluster kube context [⬆️](#Table-of-contents)

To configure `kubectl` to use the created cluster:
```shell
$ gcloud container clusters get-credentials gke-cluster --region europe-west4 --project "$GCP_PROJECT"
```
