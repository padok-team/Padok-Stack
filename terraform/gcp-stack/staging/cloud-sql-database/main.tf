terraform {
  backend "gcs" {
    credentials = "../../<staging_project_name>-terraform-credentials.json"
    bucket      = "<staging_project_name>-terraform-state"
    prefix      = "terraform/database"
  }
}

module "<company_name>_database" "<staging_project_name>_database" {
  source        = "../../modules/cloud-sql-database"
  tier          = "db-f1-micro"
  name          = "database"

  db_name       = "db"
  user_name     = "user"
  user_password = "password"
}
