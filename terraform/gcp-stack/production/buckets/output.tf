output "please_configure_frontoffice_bucket" {
  value = "${module.<production_project_name>_buckets.please_configure_frontoffice_bucket}"
}

output "please_configure_backoffice_bucket" {
  value = "${module.<production_project_name>_buckets.please_configure_backoffice_bucket}"
}
