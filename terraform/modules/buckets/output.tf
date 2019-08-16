output "please_configure_frontoffice_bucket" {
  value = "gsutil iam ch serviceAccount:<service_account_name>@<project>.iam.gserviceaccount.com:legacyBucketOwner gs://<bucket_frontoffice_name>/"
}

output "please_configure_backoffice_bucket" {
  value = "gsutil iam ch serviceAccount:<service_account_name>@<project>.iam.gserviceaccount.com:legacyBucketOwner gs://<bucket_backoffice_name>/"
}
