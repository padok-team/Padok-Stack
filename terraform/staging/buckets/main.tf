terraform {
  backend "gcs" {
    credentials     = "../../staging-terraform-credentials.json"
    bucket          = "<state_bucket_name>"
    prefix          = "terraform/buckets"
  }
}

module "<project>_buckets" "<project>_staging_buckets" {
  source            = "../../modules/buckets"
  user_bucket_name  = "<project>-staging-frontoffice"
  media_bucket_name = "<project>-staging-backoffice"
}
