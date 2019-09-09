terraform {
  backend "gcs" {
    credentials     = "../../<staging_project_name>-terraform-credentials.json"
    bucket          = "<staging_project_name>-terraform-state"
    prefix          = "terraform/buckets"
  }
}

module "<company_name>_buckets" "<staging_project_name>_staging_buckets" {
  source            = "../../modules/buckets"
  user_bucket_name  = "<staging_project_name>-frontoffice"
  media_bucket_name = "<staging_project_name>-backoffice"
}
