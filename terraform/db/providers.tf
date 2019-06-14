provider "google" {
  version     = "1.12"
  credentials = "${ file(var.credentials) }"
  project     = "${var.project_id}"
  region      = "${var.region}"
}
