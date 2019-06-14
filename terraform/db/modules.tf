module "database" {
  source = "GoogleCloudPlatform/sql-db/google"

  region = "${var.region}"
  name   = "${var.name}"

#  authorized_gae_applications = []
#  backup_configuration = {}

  database_flags = []
  database_version = "POSTGRES_9_6"

  db_name       = "${var.db_name}"
  db_collation  = "${var.db_collation}"
  user_name     = "${var.user_name}"
  user_password = "${var.user_password}"
}
