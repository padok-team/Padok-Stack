provider "google" {
  version     = "2.7.0"
  credentials = "${ file(var.credentials) }"
  project     = "${var.project_id}"
  region      = "${var.region}"
}
