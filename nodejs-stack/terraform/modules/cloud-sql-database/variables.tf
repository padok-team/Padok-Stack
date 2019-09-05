variable "region" {
  default          = "europe-west4"
}

variable "tier" {
  description      = "The tier for the master instance."
}

variable "name" {
  description      = "The name of Google SQL instance"
}

variable "ip_configuration" {
  default          = [
    {
      ipv4_enabled = true
    }
  ]
}

variable "database_flags" {
  description      = "The database flags for the master instance. See [more details](https://cloud.google.com/sql/docs/mysql/flags)"
  default          = [
    {
      name         = "character_set_server"
      value        = "utf8mb4"
    }
  ]
}

variable "database_version" {
  description      = "The database version to use"
  default          = "POSTGRES_9_6"
}

variable "db_name" {
  description      = "The name of the database"
}

variable "db_collation" {
  description      = "The collation of the database (E.g en_US.UTF8)"
  default          = "utf8mb4_general_ci"
}

variable "user_name" {
  description      = "The name of the database"
}

variable "user_password" {
  description      = "The name of the database"
}
