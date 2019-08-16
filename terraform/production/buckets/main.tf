terraform {
  backend "gcs" {
    credentials     = "../../<production_project_name>-terraform-credentials.json"
    bucket          = "<production_project_name>-terraform-state"
    prefix          = "terraform/buckets"
  }
}

module "<company_name>_buckets" "<production_project_name>_buckets" {
  source            = "../../modules/buckets"
  bucket_frontoffice_name  = "<production_project_name>-frontoffice"
  bucket_backoffice_name = "<production_project_name>-backoffice"
}
