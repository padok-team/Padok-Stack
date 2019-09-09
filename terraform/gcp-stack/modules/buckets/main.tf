resource "google_storage_bucket" "bucket_frontoffice" {
  name          = "${var.bucket_frontoffice_name}"
  location      = "${var.region}"

  storage_class = "${var.storage_class}"

  force_destroy = "${var.force_destroy}"

  # Recommended
  versioning {
    enabled     = "${var.versioning_enabled}"
  }
}

resource "google_storage_bucket" "bucket_backoffice" {
  name          = "${var.bucket_backoffice_name}"
  location      = "${var.region}"

  storage_class = "${var.storage_class}"

  force_destroy = "${var.force_destroy}"

  # Recommended
  versioning {
    enabled     = "${var.versioning_enabled}"
  }
}
