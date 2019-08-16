variable "bucket_frontoffice_name" {
  description = "Name of the frontoffice bucket"
}

variable "region" {
  description = "Region the buckets will be part of"
  default     = "europe-west4"
}

variable "bucket_backoffice_name" {
  description = "Name of the backoffice bucket"
}

variable "storage_class" {
  description = "Define storage class for the buckets (MULTI-REGIONAL, REGIONAL, NEARLINE, COLDLINE)"
  default     = "REGIONAL"
}

variable "force_destroy" {
  description = "When deleting a bucket, this boolean option will delete all contained objects. If you try to delete a bucket that contains objects, Terraform will fail that run"
  default     = "true"
}

variable "versioning_enabled" {
  description = "Configure bucket versioning block"
  default     = "true"
}
