variable "name" {
  description = "The name of Google SQL instance"
}

variable "db_name" {
  description = "The name of the database"
}

variable "db_collation" {
  description = "The collation of the database (E.g en_US.UTF8)"
  default = "en_US.UTF8"
}

variable "user_name" {
  description = "The name of the database"
}

variable "user_password" {
  description = "The name of the database"
}
