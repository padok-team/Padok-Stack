module "database" {
  source           = "GoogleCloudPlatform/sql-db/google"
  version          = "v1.2.0"

  region           = "${var.region}"
  tier             = "${var.tier}"
  name             = "${var.name}"
  ip_configuration = "${var.ip_configuration}"

  database_flags   = "${var.database_flags}"
  database_version = "${var.database_version}"

  db_name          = "${var.db_name}"
  db_collation     = "${var.db_collation}"
  user_name        = "${var.user_name}"
  user_password    = "${var.user_password}"
}
