terraform {
  backend "gcs" {
    credentials     = "../../production-terraform-credentials.json"
    bucket          = "<state_bucket_name>"
    prefix          = "terraform/buckets"
  }
}

module "<project>_buckets" "clind_production_buckets" {
  source            = "../../modules/buckets"
  bucket_frontoffice_name  = "<project>-production-frontoffice"
  bucket_backoffice_name = "<project>-production-backoffice"
}
