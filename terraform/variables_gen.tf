variable "credentials" {
  description = "The credentials to connect GCP (required)"
}

variable "project_id" {
  description = "The project ID to host the cluster in (required)"
}

variable "region" {
  description = "Region the buckets will be part of"
}
