provider "google" {
  credentials = "${file("/work/keys/padok-training-lab-d7eb320aae0e.json")}"
  project     = "padok-training-lab"
  region      = "europe-west4"
  version     = "1.12"
}

module "database" {
  source = "GoogleCloudPlatform/sql-db/google"

  region = "europe-west4"
  name   = "rpx-test-database-02"

#  authorized_gae_applications = []
#  backup_configuration = {}

  database_flags = []
  database_version = "POSTGRES_9_6"

  db_name       = "db_test"
  db_collation  = "en_US.UTF8"
  user_name     = "user_test"
  user_password = "pwd_test"
}
